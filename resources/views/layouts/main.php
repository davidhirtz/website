<?php

declare(strict_types=1);

/**
 * @var View $this
 * @var string $content
 */

use app\helpers\Html;
use app\widgets\NavItems;
use davidhirtz\yii2\skeleton\web\View;
use davidhirtz\yii2\skeleton\widgets\AdminButton;
use yii\helpers\Json;
use yii\helpers\Url;

$url = Yii::$app->get('vite')->getScriptUrl('/resources/js/app.ts');

$arguments = implode(',', [
    Json::htmlEncode(Yii::$app->params['cookieDomain'] ?? null),
    Json::htmlEncode(Yii::$app->params['gtagId'] ?? null),
]);

$js = "import i from \"$url\"; i($arguments);";
$this->registerJs($js, $this::POS_MODULE, 'main');
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
        <link rel="icon" sizes="any" type="image/svg+xml" href="/images/favicons/favicon.svg">
        <link rel="icon" type="image/png" sizes="32x32" href="/images/favicons/favicon-32x32.png">
        <link rel="apple-touch-icon" sizes="180x180" href="/images/favicons/apple-touch-icon.png">
        <link rel="manifest" href="/manifest.json">
        <?php $this->head(); ?>
    </head>
    <body class="flex flex-col min-h-svh">
    <?php $this->beginBody(); ?>
    <header>
        <nav class="w-full p-sm">
            <ul class="grid gap-2">
                <?php foreach (NavItems::getMainMenuItems() as $entry) {
                    ?>
                    <li>
                        <?= Html::link($entry, [
                            'class' => 'nav-link active:underline',
                        ]); ?>
                        <?php if ($children = NavItems::getSubmenuItems($entry)) {
                            ?>
                            <ul class="grid gap-2 p-4">
                                <?php foreach ($children as $child) {
                                    ?>
                                    <li>
                                        <?= Html::link($child, [
                                            'class' => 'nav-link active:underline',
                                        ]); ?>
                                    </li>
                                    <?php
                                }
                            ?>
                            </ul>
                            <?php
                        } ?>
                    </li>
                    <?php
                } ?>
            </ul>
        </nav>
    </header>
    <main class="grow p-sm">
        <?= $content; ?>
    </main>
    <footer class="p-sm">
        <nav>
            <ul class="flex flex-wrap gap-2">
                <?php foreach (NavItems::getFooterItems() as $entry) {
                    ?>
                    <li>
                        <?= Html::link($entry, [
                            'class' => 'nav-link active:underline',
                        ]); ?>
                    </li>
                    <?php
                } ?>
                <li class="has-hidden:hidden">
                    <cc-reset class="cursor-pointer"><?= Yii::t('app', 'Cookies') ?></cc-reset>
                </li>
            </ul>
        </nav>
    </footer>
    <?= $this->render('_cc'); ?>
    <?php if (Yii::$app->getUser()->can('entryUpdate')) {
        echo AdminButton::widget();
    } ?>
    <?php $this->endBody(); ?>
    </body>
    </html>
<?php $this->endPage(); ?>