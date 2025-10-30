<?php

declare(strict_types=1);

namespace app\modules\admin\widgets\forms;

use app\models\Section;

/**
 * @property Section $model
 */
class SectionActiveForm extends \davidhirtz\yii2\cms\modules\admin\widgets\forms\SectionActiveForm
{
    #[\Override]
    public function init(): void
    {
        $this->fields ??= [
            'status',
            'type',
            '-',
            'name',
            'content',
            '-',
            'slug',
        ];

        parent::init();
    }
}
