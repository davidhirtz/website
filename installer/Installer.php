<?php

declare(strict_types=1);

namespace Installer;

use Composer\IO\IOInterface;
use Composer\Package\Locker;
use Composer\Script\Event;
use FilesystemIterator;
use PDO;
use PDOException;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;
use stdClass;

/**
 * Turns a fresh `composer create-project` copy of the template into a project: it asks for the project's name,
 * preset and database, renames what carries the template's name, writes the gitignored configuration, migrates,
 * and finally removes itself, so a project carries none of this.
 *
 * Every answer can be given ahead through an `INSTALL_*` environment variable (`INSTALL_NAME`, `INSTALL_DB_USER`,
 * …), which is what a non-interactive run (`--no-interaction`, CI) relies on; a question without one takes its
 * default. A run that stopped half way is repeated with `composer run-script post-create-project-cmd`: every step
 * leaves alone what an earlier run already changed.
 */
final class Installer
{
    private const string TEMPLATE_NAME = 'CMS Template';
    private const string TEMPLATE_TEST_DATABASE = 'cms_template_test';

    private const string LOCAL_CONFIG = <<<'PHP'
        <?php

        declare(strict_types=1);

        use yii\helpers\ArrayHelper;

        // The gitignored configuration of this machine, selected by `YII_ENV=local`: the development configuration
        // plus whatever only this checkout needs.
        return ArrayHelper::merge(require __DIR__ . '/dev.php', [
        ]);

        PHP;

    private string $name;
    private string $slug;
    private string $package;
    private string $license;
    private string $preset;
    private string $dbHost;
    private string $dbName;
    private string $dbUser;
    private string $dbPassword;
    private bool $update;
    private bool $migrate;
    private bool $createUser;
    private bool $npm;
    private bool $git;
    private string $remote;
    private bool $databaseFailed = false;
    private bool $hasPages = false;

    private function __construct(
        private readonly IOInterface $io,
        private readonly string $root,
    ) {
    }

    public static function postCreateProject(Event $event): void
    {
        (new self($event->getIO(), dirname(__DIR__)))->run();
    }

    private function run(): void
    {
        $this->io->write(['', '<info>Setting up a new CMS project</info>', '']);

        $this->askQuestions();

        $this->step('Naming the project');
        $this->rename();

        if ($this->preset !== 'bare') {
            $this->step("Applying the \"$this->preset\" preset");
            $this->copyDirectory("$this->root/installer/presets/$this->preset", $this->root);
        }

        // The template's lock is as old as its last release, so a new project would start on outdated bundles.
        // Before the migrations, which then include those of the newer versions.
        if ($this->update) {
            $this->step('Updating the bundles');
            $this->composer(['update', 'davidhirtz/*', '--with-all-dependencies', '--no-interaction']);
        }

        $this->step('Writing the local configuration');
        $this->writeConfig();

        $hasDatabase = $this->createDatabases();

        if ($hasDatabase && $this->migrate) {
            $this->step('Migrating the databases');
            foreach (['prod' => $this->dbName, 'test' => "{$this->dbName}_test"] as $env => $database) {
                if ($this->yii(['migrate', '--interactive=0'], ['YII_ENV' => $env], quiet: true) === 0) {
                    $this->io->write("  $database");
                }
            }

            // The pages are attributed to the owner, so they wait for the first user.
            if ($this->createUser) {
                $this->step('Creating the first user');

                if ($this->yii(['user/create']) === 0) {
                    $this->step('Creating the first pages');
                    $this->hasPages = $this->yii(['setup', '--interactive=0'], quiet: true) === 0;
                }
            }
        }

        if ($this->npm) {
            $this->step('Installing the Node dependencies');

            if ($this->exec(['npm', 'install']) === 0 && $this->preset !== 'bare') {
                $this->exec(['npm', 'run', 'build']);
            }
        }

        $this->step('Removing the installer');
        $this->removeInstaller();

        if ($this->git) {
            $this->step('Creating the git repository');
            $this->initGit();
        }

        $this->writeNextSteps($hasDatabase);
    }

    private function askQuestions(): void
    {
        $this->name = $this->ask('NAME', 'Project name', ucwords(str_replace(['-', '_'], ' ', basename($this->root))));
        $this->slug = $this->ask('SLUG', 'Project slug', self::slugify($this->name));
        $this->package = $this->ask('PACKAGE', 'Composer package name', "app/$this->slug");
        $this->license = $this->ask('LICENSE', 'License, "MIT" keeps the LICENSE file', 'proprietary');
        $this->preset = $this->askPreset();
        $this->update = $this->confirm('UPDATE', 'Update the davidhirtz/* bundles to their latest versions?', true);

        $this->io->write(['', 'The database, created when it does not exist yet. The tests use a second one with the suffix "_test".']);

        $this->dbHost = $this->ask('DB_HOST', 'Database host', '127.0.0.1');
        $this->dbName = $this->ask('DB_NAME', 'Database name', str_replace('-', '_', $this->slug));
        $this->dbUser = $this->ask('DB_USER', 'Database user', 'root');
        $this->dbPassword = $this->askSecret('DB_PASSWORD', 'Database password');

        $this->io->write('');

        $this->migrate = $this->confirm('MIGRATE', 'Run the migrations?', true) && $this->checkDatabaseConnection();
        $this->createUser = $this->migrate && $this->confirm('USER', 'Create the first user?', $this->io->isInteractive());
        $this->npm = $this->confirm('NPM', 'Install the Node dependencies?', self::commandExists('npm'));
        $this->git = !is_dir("$this->root/.git") && $this->confirm('GIT', 'Create a git repository with an "Initial commit"?', true);
        $this->remote = $this->git ? $this->ask('GIT_REMOTE', 'Git remote "origin", empty for none', '') : '';
    }

    /**
     * Connects before anything is written, so wrong credentials are asked again instead of skipping the migrations,
     * the first user and the pages at the end. Without a terminal, the run goes on without them.
     */
    private function checkDatabaseConnection(): bool
    {
        while (true) {
            try {
                $this->connect();
                return true;
            } catch (PDOException $exception) {
                $this->io->writeError(['', '<warning>' . $this->describeDatabaseError($exception) . '</warning>']);
            }

            if (!$this->io->isInteractive() || !$this->io->askConfirmation('<info>Enter other credentials?</info> [<comment>Y/n</comment>]: ')) {
                $this->io->write(['The migrations, the first user and the pages are skipped, see the next steps at the end.', '']);
                $this->databaseFailed = true;

                return false;
            }

            // Asked directly rather than through `ask()`, whose `INSTALL_*` variables would answer the same again.
            $this->dbHost = trim((string)$this->io->ask("<info>Database host</info> [<comment>$this->dbHost</comment>]: ", $this->dbHost));
            $this->dbUser = trim((string)$this->io->ask("<info>Database user</info> [<comment>$this->dbUser</comment>]: ", $this->dbUser));
            $this->dbPassword = (string)$this->io->askAndHideAnswer('<info>Database password</info>: ');
        }
    }

    private function connect(): PDO
    {
        [$host, $port] = array_pad(explode(':', $this->dbHost, 2), 2, null);

        return new PDO("mysql:host=$host" . ($port ? ";port=$port" : ''), $this->dbUser, $this->dbPassword, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_TIMEOUT => 5,
        ]);
    }

    /**
     * Says what the server's error means for whoever installs, with its own message last for a search.
     */
    private function describeDatabaseError(PDOException $exception): string
    {
        $message = $exception->getMessage();
        $code = (int)($exception->errorInfo[1] ?? 0) ?: (preg_match('/\[(\d{4})]/', $message, $matches) ? (int)$matches[1] : 0);

        $hint = match ($code) {
            1045 => "The database server refused the user \"$this->dbUser\": the user does not exist or the password is wrong.",
            // MariaDB answers 1698 for an unknown user too, and root signs in through unix_socket by default.
            1698 => "The database server refused the user \"$this->dbUser\". Either it does not exist, or it signs in through"
                . ' unix_socket, as root does on MariaDB, and only from the system account of the same name. Enter a user'
                . ' with a password, or create one with `CREATE USER ... IDENTIFIED BY ...` and'
                . ' `GRANT ALL PRIVILEGES ON *.* TO ...`.',
            1044, 1142 => "The user \"$this->dbUser\" may not create databases. Create \"$this->dbName\" and"
                . " \"{$this->dbName}_test\" yourself, or grant the user the CREATE privilege.",
            2002, 2003, 2005, 2006 => "No database server answers at \"$this->dbHost\". Is MySQL or MariaDB running, and is"
                . ' the host right?',
            default => 'The database server could not be reached.',
        };

        return "$hint\n($message)";
    }

    /**
     * The presets are the directories in `installer/presets`, each copied over the project as it is; "bare" adds
     * nothing and is always offered.
     */
    private function askPreset(): string
    {
        $presets = ['bare' => 'bare: the layout, two section types and no content model of its own'];

        foreach (glob("$this->root/installer/presets/*", GLOB_ONLYDIR) ?: [] as $directory) {
            $name = basename($directory);
            $description = is_file("$directory.txt") ? trim((string)file_get_contents("$directory.txt")) : '';
            $presets[$name] = $description ? "$name: $description" : $name;
        }

        $preset = getenv('INSTALL_PRESET');

        if ($preset !== false) {
            if (!isset($presets[$preset])) {
                throw new \RuntimeException("Unknown preset \"$preset\", expected one of: " . implode(', ', array_keys($presets)));
            }

            return $preset;
        }

        if (count($presets) === 1) {
            return 'bare';
        }

        $names = array_keys($presets);
        $index = $this->io->select('<info>Preset</info> [<comment>bare</comment>]:', array_values($presets), '0');

        return $names[(int)$index];
    }

    private function rename(): void
    {
        $testDatabase = "{$this->dbName}_test";

        $this->updateComposerJson(function (stdClass $json): void {
            $json->name = $this->package;
            $json->description = $this->name;
            $json->license = $this->license;
            unset($json->homepage);
        });

        $this->replace('config/prod.php', "'name' => '" . self::TEMPLATE_NAME . "',", "'name' => " . var_export($this->name, true) . ',');
        $this->replace('config/test.php', self::TEMPLATE_TEST_DATABASE, $testDatabase);
        $this->replace('.github/workflows/deploy.yml', self::TEMPLATE_TEST_DATABASE, $testDatabase);
        $this->replace('README.md', "Website\n=======\n", "$this->name\n" . str_repeat('=', mb_strlen($this->name)) . "\n");

        $documents = ['README.md', 'AGENTS.md', ...array_map(
            fn (string $path): string => substr($path, strlen($this->root) + 1),
            glob("$this->root/docs/*.md") ?: [],
        )];

        foreach ($documents as $document) {
            // How to create a project is the template's to explain, not the project's.
            $this->replacePattern($document, '/<!-- template -->.*?<!-- \\/template -->\\n+/s', '');
            $this->replace($document, self::TEMPLATE_TEST_DATABASE, $testDatabase);

            if ($this->remote !== '') {
                $this->replace($document, 'git@github.com:OWNER/REPOSITORY.git', $this->remote);
            }

            $this->replace($document, 'REPOSITORY', $this->slug);
        }

        $title = json_encode($this->name, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $this->replace('web/manifest.json', '"name": ""', "\"name\": $title");
        $this->replace('web/manifest.json', '"short_name": ""', "\"short_name\": $title");

        if (strcasecmp($this->license, 'MIT') !== 0) {
            @unlink("$this->root/LICENSE");
            $this->replace('README.md', "\n\n\nLicense\n-------\n\nReleased under the [MIT License](LICENSE).\n", "\n");
        }
    }

    private function writeConfig(): void
    {
        [$host, $port] = array_pad(explode(':', $this->dbHost, 2), 2, null);
        $dsn = "mysql:host=$host" . ($port ? ";port=$port" : '') . ";dbname=$this->dbName";

        $this->writeFile('config/db.php', self::phpArray([
            'dsn' => $dsn,
            'username' => $this->dbUser,
            'password' => $this->dbPassword,
        ]));

        if (!is_file("$this->root/config/local.php")) {
            $this->writeFile('config/local.php', self::LOCAL_CONFIG);
        }

        // Generates the cookie validation key and the password pepper into `config/params.php`. Its listing of
        // all parameters, secrets included, is only shown when it fails, which would also hide its confirmations.
        $this->yii(['params', '--interactive=0'], quiet: true);
    }

    private function createDatabases(): bool
    {
        if (!$this->migrate) {
            return false;
        }

        $this->step('Creating the databases');

        try {
            $pdo = $this->connect();

            foreach ([$this->dbName, "{$this->dbName}_test"] as $database) {
                $pdo->exec('CREATE DATABASE IF NOT EXISTS `' . str_replace('`', '``', $database) . '` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
                $this->io->write("  $database");
            }

            return true;
        } catch (PDOException $exception) {
            $this->io->writeError([
                '<warning>The databases could not be created. ' . $this->describeDatabaseError($exception) . '</warning>',
                '<warning>Fix `config/db.php`, then run the migrations by hand (see below).</warning>',
            ]);

            $this->databaseFailed = true;

            return false;
        }
    }

    /**
     * Last of all, so a run that failed earlier can be repeated with `composer run-script post-create-project-cmd`.
     */
    private function removeInstaller(): void
    {
        $this->updateComposerJson(static function (stdClass $json): void {
            unset($json->autoload->{'psr-4'}->{'Installer\\'}, $json->scripts->{'post-create-project-cmd'});
        });

        self::removeDirectory("$this->root/installer");
        @unlink("$this->root/.github/workflows/installer.yml");
        // Its reports are for the template's maintainer, not for whoever runs the project.
        @unlink("$this->root/SECURITY.md");

        $this->composer(['dump-autoload', '--quiet']);
    }

    private function initGit(): void
    {
        // An archive does not keep the executable bit the hook needs.
        @chmod("$this->root/.githooks/pre-push", 0755);

        $this->exec(['git', 'init', '--quiet', '--initial-branch=main']);
        $this->exec(['git', 'config', 'core.hooksPath', '.githooks']);
        $this->exec(['git', 'add', '--all']);
        $this->exec(['git', 'commit', '--quiet', '--message=Initial commit']);

        if ($this->remote !== '') {
            $this->exec(['git', 'remote', 'add', 'origin', $this->remote]);
        }
    }

    private function writeNextSteps(bool $hasDatabase): void
    {
        $steps = [];

        if (!$hasDatabase || !$this->migrate) {
            $steps[] = 'php yii migrate && YII_ENV=test php yii migrate --interactive=0';
        }

        // The container creates the first user and the pages when it starts.
        if (!getenv('INSTALL_DOCKER')) {
            if (!$hasDatabase || !$this->createUser) {
                $steps[] = 'php yii user/create';
            }

            if (!$this->hasPages) {
                $steps[] = 'php yii setup';
            }
        }

        if (!$this->npm) {
            // A preset's views and scripts are not in the committed `web/dist` yet.
            $steps[] = $this->preset === 'bare' ? 'npm install' : 'npm install && npm run build';
        }

        // `installer/docker.sh` runs it in the container, which starts the server itself.
        $steps[] = getenv('INSTALL_DOCKER') ? 'docker compose up' : 'YII_ENV=local php -S localhost:8080 -t web';

        if ($directory = $this->getProjectDirectory()) {
            array_unshift($steps, 'cd ' . (preg_match('#^[\w./-]+$#', $directory) ? $directory : escapeshellarg($directory)));
        }

        $this->io->write(['', "<info>$this->name is ready.</info> Next:", '']);

        if ($this->databaseFailed) {
            $this->io->write([
                "  The database is not set up yet: correct the credentials in config/db.php and create the databases",
                "  \"$this->dbName\" and \"{$this->dbName}_test\" if the user may not, then:",
                '',
            ]);
        }

        foreach ($steps as $step) {
            $this->io->write("  $step");
        }

        $this->io->write([
            '',
            'Then sign in at http://localhost:8080/admin.',
            'Claude Code in this folder edits the content through the "website" server of .mcp.json, see docs/mcp.md.',
            '',
        ]);
    }

    /**
     * Returns the path to change into before the next steps, relative to the folder `composer create-project` was run
     * in, or an empty string when that is the project folder. Composer changes into the project to run the scripts,
     * but `PWD` still names the shell's folder.
     */
    private function getProjectDirectory(): string
    {
        $cwd = getenv('PWD');

        if (!$cwd || ($cwd = realpath($cwd)) === false || ($root = realpath($this->root)) === false) {
            return $this->root;
        }

        if ($cwd === $root) {
            return '';
        }

        return str_starts_with($root, $cwd . DIRECTORY_SEPARATOR) ? substr($root, strlen($cwd) + 1) : $root;
    }

    private function ask(string $key, string $question, string $default): string
    {
        $answer = getenv("INSTALL_$key");

        if ($answer === false) {
            // An empty default shows no brackets, which would otherwise read as an empty list.
            $hint = $default !== '' ? " [<comment>$default</comment>]" : '';
            $answer = $this->io->ask("<info>$question</info>$hint: ", $default);
        }

        return trim((string)$answer);
    }

    private function askSecret(string $key, string $question): string
    {
        $answer = getenv("INSTALL_$key");

        if ($answer === false && $this->io->isInteractive()) {
            $answer = $this->io->askAndHideAnswer("<info>$question</info>: ");
        }

        return (string)$answer;
    }

    private function confirm(string $key, string $question, bool $default): bool
    {
        $answer = getenv("INSTALL_$key");

        if ($answer !== false) {
            return filter_var($answer, FILTER_VALIDATE_BOOL);
        }

        return $this->io->askConfirmation(sprintf('<info>%s</info> [<comment>%s</comment>]: ', $question, $default ? 'Y/n' : 'y/N'), $default);
    }

    private function step(string $title): void
    {
        $this->io->write(['', "<comment>==> $title</comment>"]);
    }

    /**
     * @param callable(stdClass): void $update
     */
    private function updateComposerJson(callable $update): void
    {
        $path = "$this->root/composer.json";
        $json = json_decode((string)file_get_contents($path), flags: JSON_THROW_ON_ERROR);

        $update($json);

        // Indented by two spaces, as the file is.
        $contents = json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
        $contents = preg_replace_callback('/^ +/m', static fn (array $match): string => str_repeat(' ', intdiv(strlen($match[0]), 2)), $contents) . "\n";

        file_put_contents($path, $contents);

        // The lock carries a hash of `composer.json`, and a stale one makes every later `composer install` warn.
        $lock = "$this->root/composer.lock";

        if (is_file($lock)) {
            $hash = Locker::getContentHash($contents);
            file_put_contents($lock, preg_replace('/"content-hash": "[0-9a-f]+"/', "\"content-hash\": \"$hash\"", (string)file_get_contents($lock), 1));
        }
    }

    private function replace(string $file, string $search, string $replace): void
    {
        $path = "$this->root/$file";

        if (is_file($path)) {
            file_put_contents($path, str_replace($search, $replace, (string)file_get_contents($path)));
        }
    }

    private function replacePattern(string $file, string $pattern, string $replace): void
    {
        $path = "$this->root/$file";

        if (is_file($path)) {
            file_put_contents($path, (string)preg_replace($pattern, $replace, (string)file_get_contents($path)));
        }
    }

    private function writeFile(string $file, string $contents): void
    {
        file_put_contents("$this->root/$file", $contents);
        $this->io->write("  $file");
    }

    /**
     * @param list<string> $arguments
     * @param array<string, string> $env
     */
    private function yii(array $arguments, array $env = [], bool $quiet = false): int
    {
        return $this->exec([PHP_BINARY, 'yii', ...$arguments], $env, $quiet);
    }

    /**
     * Runs the Composer that runs the installer.
     *
     * @param list<string> $arguments
     */
    private function composer(array $arguments): int
    {
        $composer = getenv('COMPOSER_BINARY') ?: 'composer';

        return $this->exec(str_ends_with($composer, '.phar') || is_file($composer)
            ? [PHP_BINARY, $composer, ...$arguments]
            : [$composer, ...$arguments]);
    }

    /**
     * Runs a command attached to the terminal, so a command that asks (`user/create`) can. A quiet command's
     * output is kept back and only shown when it fails.
     *
     * @param list<string> $command
     * @param array<string, string> $env
     */
    private function exec(array $command, array $env = [], bool $quiet = false): int
    {
        // A quiet command gets no input either: a question it asked could not be seen, so it would wait forever.
        $descriptors = $quiet ? [['pipe', 'r'], ['pipe', 'w'], STDERR] : [STDIN, STDOUT, STDERR];
        $process = proc_open($command, $descriptors, $pipes, $this->root, [...getenv(), ...$env]);

        if (!is_resource($process)) {
            return 1;
        }

        if ($quiet) {
            fclose($pipes[0]);
        }

        $output = $quiet ? (string)stream_get_contents($pipes[1]) : '';
        $exitCode = proc_close($process);

        if ($exitCode !== 0) {
            $this->io->write($output);
            $this->io->writeError("<warning>`" . implode(' ', $command) . "` failed with exit code $exitCode.</warning>");
        }

        return $exitCode;
    }

    private function copyDirectory(string $source, string $target): void
    {
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::SELF_FIRST);

        /** @var SplFileInfo $file */
        foreach ($files as $file) {
            $path = $target . substr($file->getPathname(), strlen($source));

            if ($file->isDir()) {
                is_dir($path) || mkdir($path, 0777, true);
            } else {
                copy($file->getPathname(), $path);
            }
        }
    }

    private static function removeDirectory(string $directory): void
    {
        if (!is_dir($directory)) {
            return;
        }

        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST);

        /** @var SplFileInfo $file */
        foreach ($files as $file) {
            $file->isDir() ? rmdir($file->getPathname()) : unlink($file->getPathname());
        }

        rmdir($directory);
    }

    /**
     * @param array<string, string> $values
     */
    private static function phpArray(array $values): string
    {
        $lines = array_map(
            static fn (string $key, string $value): string => '    ' . var_export($key, true) . ' => ' . var_export($value, true) . ',',
            array_keys($values),
            $values,
        );

        return "<?php\n\nreturn [\n" . implode("\n", $lines) . "\n];\n";
    }

    private static function slugify(string $name): string
    {
        return trim((string)preg_replace('/[^a-z0-9]+/', '-', strtolower($name)), '-') ?: 'project';
    }

    private static function commandExists(string $command): bool
    {
        return trim((string)shell_exec((PHP_OS_FAMILY === 'Windows' ? 'where ' : 'command -v ') . escapeshellarg($command) . ' 2>' . (PHP_OS_FAMILY === 'Windows' ? 'NUL' : '/dev/null'))) !== '';
    }
}
