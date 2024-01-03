<?php

namespace app\models;

use Yii;

/**
 * @property bool $show_in_menu
 * @property bool $show_in_footer
 */
class Entry extends \davidhirtz\yii2\cms\models\Entry
{
    public function rules(): array
    {
        return [
            ...parent::rules(), [
                ['show_in_menu', 'show_in_footer'],
                'boolean',
            ]];
    }

    public function hasShowInMenuEnabled(): bool
    {
        return $this->getTypeOptions()['hasShowInMenuEnabled'] ?? true;
    }

    public function isMenuItem(): bool
    {
        return $this->show_in_menu;
    }

    public function attributeLabels(): array
    {
        return [
            ...parent::attributeLabels(),
            'show_in_menu' => Yii::t('app', 'Show in menu'),
            'show_in_footer' => Yii::t('app', 'Show in footer'),
        ];
    }
}
