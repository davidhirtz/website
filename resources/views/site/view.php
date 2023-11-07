<?php
/**
 * @see SiteController::actionView()
 *
 * @var View $this
 * @var Entry $entry
 */

use app\controllers\SiteController;
use app\models\Asset;
use app\models\Entry;
use davidhirtz\yii2\cms\widgets\MetaTags;
use davidhirtz\yii2\cms\widgets\Sections;
use davidhirtz\yii2\skeleton\web\View;

MetaTags::widget([
    'model' => $entry,
    'assetType' => Asset::TYPE_SEO,
    'transformationName' => 'md',
]);

echo Sections::widget([
    'entry' => $entry,
]);

dump(Yii::getAlias('@app'));