<?php

declare(strict_types=1);

namespace App\Tests\Console;

use App\Models\Entry;
use App\Tests\TestCase;
use Hirtz\Skeleton\Console\Application;
use Hirtz\Skeleton\Test\Traits\UserFixtureTrait;
use yii\console\ExitCode;

final class SetupTest extends TestCase
{
    use UserFixtureTrait;

    protected string $applicationClass = Application::class;

    public function testSetupCreatesTheIndexEntry(): void
    {
        $controller = new SetupControllerMock('setup', Application::current());

        self::assertSame(ExitCode::OK, $controller->runAction('index'));
        // A preset's setup may create more pages than the index entry; the slug is not a column to query by.
        self::assertContains(Entry::getModule()->entryIndexSlug, array_map(static fn (Entry $entry): ?string => $entry->slug, Entry::find()->all()));
    }
}
