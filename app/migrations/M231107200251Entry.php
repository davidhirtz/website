<?php

namespace app\migrations;

use app\models\Entry;
use davidhirtz\yii2\cms\modules\ModuleTrait;
use davidhirtz\yii2\skeleton\db\traits\MigrationTrait;
use Yii;
use yii\db\Migration;

/**
 * @noinspection PhpUnused
 */

class M231107200251Entry extends Migration
{
    use MigrationTrait;
    use ModuleTrait;

    public function safeUp(): void
    {
        foreach ($this->getLanguages() as $language) {
            Yii::$app->language = $language;

            $this->addColumn(Entry::tableName(), 'show_in_menu', $this->tinyInteger(1)->unsigned()->notNull()->defaultValue(0)->after('publish_date'));
            $this->addColumn(Entry::tableName(), 'show_in_footer', $this->tinyInteger(1)->unsigned()->notNull()->defaultValue(0)->after('show_in_menu'));
            $this->createIndex('show_in_menu', Entry::tableName(), ['show_in_menu', 'show_in_footer', 'status', 'position']);
        }
    }

    public function safeDown(): void
    {
        foreach ($this->getLanguages() as $language) {
            Yii::$app->language = $language;

            $this->dropIndex('show_in_menu', Entry::tableName());

            $this->dropColumn(Entry::tableName(), 'show_in_menu');
            $this->dropColumn(Entry::tableName(), 'show_in_footer');
        }
    }

    /**
     * @return array
     */
    public function getLanguages(): array
    {
        return static::getModule()->enableI18nTables ? Yii::$app->getI18n()->getLanguages() : [Yii::$app->sourceLanguage];
    }
}