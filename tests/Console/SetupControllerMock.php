<?php

declare(strict_types=1);

namespace App\Tests\Console;

use Hirtz\Cms\Console\Controllers\SetupController;
use Hirtz\Skeleton\Test\Traits\StdOutBufferControllerTrait;

/**
 * Buffers the command's output, so a test can run it without writing to the terminal.
 */
final class SetupControllerMock extends SetupController
{
    use StdOutBufferControllerTrait;
}
