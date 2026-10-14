<?php

declare(strict_types=1);

namespace Installer;

use Composer\IO\IOInterface;
use Composer\Package\Locker;
use Composer\Script\Event;
use FilesystemIterator;
use PDO;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;
use stdClass;
use Throwable;

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
    private bool $migrate;
    private bool $createUser;
    private bool $npm;
    private bool $git;
    private string $remote;

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

            if ($this->createUser) {
                $this->step('Creating the first user');
                $this->yii(['user/create']);
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

        $this->io->write(['', 'The database, created when it does not exist yet. The tests use a second one with the suffix "_test".']);

        $this->dbHost = $this->ask('DB_HOST', 'Database host', '127.0.0.1');
        $this->dbName = $this->ask('DB_NAME', 'Database name', str_replace('-', '_', $this->slug));
        $this->dbUser = $this->ask('DB_USER', 'Database user', 'root');
        $this->dbPassword = $this->askSecret('DB_PASSWORD', 'Database password');

        $this->io->write('');

        $this->migrate = $this->confirm('MIGRATE', 'Run the migrations?', true);
        $this->createUser = $this->migrate && $this->confirm('USER', 'Create the first user?', $this->io->isInteractive());
        $this->npm = $this->confirm('NPM', 'Install the Node dependencies?', self::commandExists('npm'));
        $this->git = !is_dir("$this->root/.git") && $this->confirm('GIT', 'Create a git repository with an "Initial commit"?', true);
        $this->remote = $this->git ? $this->ask('GIT_REMOTE', 'Git remote "origin", empty for none', '') : '';
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
        $this->replace('README.md', self::TEMPLATE_TEST_DATABASE, $testDatabase);
        $this->replace('README.md', "README\n============================\n", "$this->name\n" . str_repeat('=', mb_strlen($this->name)) . "\n");
        $this->replace('README.md', 'REPOSITORY', $this->slug);

        if ($this->remote !== '') {
            $this->replace('README.md', "git@github.com:OWNER/$this->slug.git", $this->remote);
        }

        // How to create a project is the template's to explain, not the project's.
        $this->replacePattern('README.md', '/<!-- template -->.*?<!-- \\/template -->\\n+/s', '');

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
        // all parameters, secrets included, is only shown when it fails.
        $this->yii(['params'], quiet: true);
    }

    private function createDatabases(): bool
    {
        if (!$this->migrate) {
            return false;
        }

        $this->step('Creating the databases');

        [$host, $port] = array_pad(explode(':', $this->dbHost, 2), 2, null);

        try {
            $pdo = new PDO("mysql:host=$host" . ($port ? ";port=$port" : ''), $this->dbUser, $this->dbPassword, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ]);

            foreach ([$this->dbName, "{$this->dbName}_test"] as $database) {
                $pdo->exec('CREATE DATABASE IF NOT EXISTS `' . str_replace('`', '``', $database) . '` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
                $this->io->write("  $database");
            }

            return true;
        } catch (Throwable $exception) {
            $this->io->writeError([
                "<warning>The databases could not be created: {$exception->getMessage()}</warning>",
                '<warning>Fix `config/db.php`, then run the migrations by hand (see below).</warning>',
            ]);

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

        $composer = getenv('COMPOSER_BINARY') ?: 'composer';
        $this->exec(str_ends_with($composer, '.phar') || is_file($composer)
            ? [PHP_BINARY, $composer, 'dump-autoload', '--quiet']
            : [$composer, 'dump-autoload', '--quiet']);
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

        if (!$hasDatabase || !$this->createUser) {
            $steps[] = 'php yii user/create';
        }

        if (!$this->npm) {
            $steps[] = 'npm install';
        }

        $steps[] = 'YII_ENV=local php -S localhost:8080 -t web';

        $this->io->write(['', "<info>$this->name is ready.</info> Next:", '']);

        foreach ($steps as $step) {
            $this->io->write("  $step");
        }

        $this->io->write([
            '',
            'Then sign in at http://localhost:8080/admin and open /admin/cms/setup once to create the home page.',
            '',
        ]);
    }

    private function ask(string $key, string $question, string $default): string
    {
        $answer = getenv("INSTALL_$key");

        if ($answer === false) {
            $answer = $this->io->ask(sprintf('<info>%s</info> [<comment>%s</comment>]: ', $question, $default), $default);
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
     * Runs a command attached to the terminal, so a command that asks (`user/create`) can. A quiet command's
     * output is kept back and only shown when it fails.
     *
     * @param list<string> $command
     * @param array<string, string> $env
     */
    private function exec(array $command, array $env = [], bool $quiet = false): int
    {
        $process = proc_open($command, [STDIN, $quiet ? ['pipe', 'w'] : STDOUT, STDERR], $pipes, $this->root, [...getenv(), ...$env]);

        if (!is_resource($process)) {
            return 1;
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
