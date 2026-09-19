<?php

declare(strict_types=1);

namespace App\Tests;

use Override;

/**
 * The cms base class loads the tenant fixture every entry needs and reads the bundle's `config/test.php`; a
 * project's tests run against the project's, which merges the application configuration with the test database.
 */
abstract class TestCase extends \Hirtz\Cms\Test\TestCase
{
    #[Override]
    protected function setUp(): void
    {
        $this->config ??= require dirname(__DIR__) . '/config/test.php';
        parent::setUp();
    }
}
