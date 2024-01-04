<?php

namespace app\migrations;

use davidhirtz\yii2\cms\migrations\traits\FooterColumnTrait;
use davidhirtz\yii2\cms\migrations\traits\MenuColumnTrait;
use davidhirtz\yii2\skeleton\db\traits\MigrationTrait;
use yii\db\Migration;

/**
 * @noinspection PhpUnused
 */

class M231107200251Entry extends Migration
{
    use MigrationTrait;
    use MenuColumnTrait;
    use FooterColumnTrait;

    public function safeUp(): void
    {
        $this->addShowInMenuColumn();
        $this->addShowInFooterColumn();
    }

    public function safeDown(): void
    {
        $this->dropShowInFooterColumn();
        $this->dropShowInMenuColumn();
    }
}
