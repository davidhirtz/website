<?php

namespace app\modules\admin\widgets\forms;

use app\models\Entry;
use Yii;
use yii\widgets\ActiveField;

/**
 * @property Entry $model
 */
class EntryActiveForm extends \davidhirtz\yii2\cms\modules\admin\widgets\forms\EntryActiveForm
{
    /**
     * @uses static::showInMenuField()
     */
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
            ['show_in_footer', 'checkbox'],
        ];

        parent::init();
    }

    public function showInMenuField(): string|ActiveField
    {
        if (!$this->model->hasShowInMenuEnabled() && !$this->model->isMenuItem()) {
            return '';
        }

        $options = [];

        if ($this->model->parent_id) {
            foreach ($this->model->ancestors as $ancestor) {
                if (!$ancestor->isMenuItem()) {
                    $options['options']['class'] = 'form-group row disabled';
                    $options['options']['title'] = Yii::t('app', "Parent entry \"{entry}\" is not a menu item", [
                        'entry' => $ancestor->getI18nAttribute('name'),
                    ]);
                    break;
                }
            }
        }

        return $this->field($this->model, 'show_in_menu', $options)->checkbox();
    }
}
