<?php

namespace app\modules\admin\widgets\forms;

use app\models\Entry;
use davidhirtz\yii2\cms\modules\admin\widgets\forms\traits\FooterFieldTrait;
use davidhirtz\yii2\cms\modules\admin\widgets\forms\traits\MenuFieldTrait;

/**
 * @property Entry $model
 */
class EntryActiveForm extends \davidhirtz\yii2\cms\modules\admin\widgets\forms\EntryActiveForm
{
    use MenuFieldTrait;
    use FooterFieldTrait;

    public function init(): void
    {
        $this->fields = [
            'status',
            'type',
            'parent_id',
            'name',
            'content',
            'publish_date',
            '-',
            'title',
            'description',
            'slug',
            '-',
            'show_in_menu',
            'show_in_footer',
        ];

        parent::init();
    }
}
