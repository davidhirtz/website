<?php
/**
 * @var View $this
 * @var string $content
 */

use davidhirtz\yii2\skeleton\helpers\Html;
use davidhirtz\yii2\skeleton\web\View;
use davidhirtz\yii2\skeleton\widgets\AdminButton;
use yii\helpers\Url;

?>
<?php $this->beginPage() ?>
    <!DOCTYPE html>
    <html lang="<?= $this->getHtmlLangAttribute(); ?>">
    <head>
        <meta charset="<?= Yii::$app->charset; ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?= Html::csrfMetaTags(); ?>
        <title><?= Html::encode($this->getDocumentTitle()); ?></title>
        <link rel="canonical" href="<?= Url::current([], true); ?>">
        <link rel="stylesheet" href="<?= $this->getFilenameWithVersion('css/site.css'); ?>">
        <link rel="apple-touch-icon" sizes="180x180" href="/images/favicons/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="/images/favicons/favicon-32x32.png">
        <link rel="manifest" href="/site.webmanifest">
        <meta name="msapplication-config" content="/browserconfig.xml">
        <?php $this->head(); ?>
    </head>
    <body>
    <?php $this->beginBody(); ?>
    <main>
        <?= $content; ?>
    </main>
    <script src="<?= $this->getFilenameWithVersion('js/site.js'); ?>"></script>
    <?php if (Yii::$app->getUser()->can('entryUpdate')) {
        echo AdminButton::widget();
    } ?>
    <?php $this->endBody(); ?>
    </body>
    </html>
<?php $this->endPage(); ?>