<?php

declare(strict_types=1);

namespace app\modules\admin\widgets\forms;

use davidhirtz\yii2\skeleton\widgets\forms\DynamicRangeDropdown;

class SectionActiveForm extends \davidhirtz\yii2\cms\modules\admin\widgets\forms\SectionActiveForm
{
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
