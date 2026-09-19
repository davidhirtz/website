<?php

declare(strict_types=1);

namespace App\Modules\Admin\Controllers;

use Hirtz\Cms\Modules\ModuleTrait;
use Override;
use Yii;

/**
 * Sets up the default entries through the admin, reachable at `/admin/cms/setup` once the module maps it.
 *
 * @noinspection PhpUnused
 */
final class SetupController extends \Hirtz\Cms\Modules\Admin\Controllers\SetupController
{
    use ModuleTrait;

    /**
     * @return list<array<string, mixed>>
     */
    #[Override]
    public function getCategoryAttributes(): array
    {
        return [];
    }

    /**
     * @return list<array<string, mixed>>
     */
    #[Override]
    public function getEntryAttributes(): array
    {
        return [
            [
                'name' => 'Home',
                'slug' => self::getModule()->entryIndexSlug,
                'title' => Yii::$app->name,
            ],
        ];
    }
}
