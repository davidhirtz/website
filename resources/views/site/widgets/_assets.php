<?php
/**
 * @var View $this
 * @var Asset[] $assets
 */

use app\models\Asset;
use davidhirtz\yii2\cms\widgets\Canvas;
use davidhirtz\yii2\skeleton\web\View;

foreach ($assets as $asset) {
    ?>
    <div>
        <?= Canvas::widget(['asset' => $asset]) ?>
    </div>
    <?php
}
?>