<?php

namespace app\modules\admin\controllers;

use davidhirtz\yii2\cms\modules\ModuleTrait;
use Yii;

/**
 * Setup default entries via the backend.
 * @noinspection PhpUnused
 */
class SetupController extends \davidhirtz\yii2\cms\modules\admin\controllers\SetupController
{
    use ModuleTrait;

    public function getCategoryAttributes(): array
    {
        return [];
    }

    public function getEntryAttributes(): array
    {
        return [
            [
                'name' => 'Home',
                'slug' => static::getModule()->entryIndexSlug,
                'title' => Yii::$app->name,
            ],
        ];
    }
}
