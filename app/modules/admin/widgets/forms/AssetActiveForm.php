<?php

namespace app\modules\admin\widgets\forms;

use app\models\Asset;

/**
 * @property Asset $model
 */
class AssetActiveForm extends \davidhirtz\yii2\cms\modules\admin\widgets\forms\AssetActiveForm
{
    /**
     * @see self::animationField()
     */
    public function init(): void
    {
        $this->fields = $this->model->getVisibleAttributes();
        parent::init();
    }
}