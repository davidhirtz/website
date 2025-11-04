<?php

declare(strict_types=1);

namespace app\modules\admin\widgets\forms;

use app\models\Asset;
use Override;

/**
 * @property Asset $model
 */
final class AssetActiveForm extends \davidhirtz\yii2\cms\modules\admin\widgets\forms\AssetActiveForm
{
    #[Override]
    public function init(): void
    {
        $this->fields = $this->model->getVisibleAttributes();
        parent::init();
    }
}
