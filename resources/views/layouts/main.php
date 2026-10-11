<?php

declare(strict_types=1);

/**
 * @var View $this
 * @var string $content
 */

use App\Helpers\Html;
use App\Models\Menus\SiteMenu;
use Hirtz\Cms\Models\Collections\MenuCollection;
use Hirtz\Cms\Models\Entry;
use Hirtz\Skeleton\Web\View;
use Hirtz\Skeleton\Widgets\Buttons\AdminButton;

?>
<?php $this->beginPage() ?>
    <!DOCTYPE html>
    <html lang="<?= $this->getHtmlLangAttribute(); ?>">
    <head>
        <meta charset="<?= Yii::$app->charset; ?>">
        <meta name="viewport" content="width=device-width,height=device-height,initial-scale=1">
        <?= Html::csrfMetaTags(); ?>
        <title><?= Html::encode($this->getDocumentTitle()); ?></title>
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
                <?php foreach (MenuCollection::getRootItems(SiteMenu::Main) as $entry) {
                    ?>
                    <li>
                        <?= Html::link($entry, [
                            'class' => 'nav-link active:underline',
                        ]); ?>
                        <?php if ($children = MenuCollection::getSubmenuItems($entry, SiteMenu::Main)) {
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
                <?php foreach (MenuCollection::getItems(SiteMenu::Footer) as $entry) {
                    ?>
                    <li>
                        <?= Html::link($entry, [
                            'class' => 'nav-link active:underline',
                        ]); ?>
                    </li>
                    <?php
                } ?>
                <li class="has-hidden:hidden">
                    <cc-settings hidden><?= Yii::t('app', 'CC_SETTINGS_BUTTON') ?></cc-settings>
                </li>
            </ul>
        </nav>
    </footer>
    <?= $this->render('_cc'); ?>
    <?= AdminButton::make()->roles([Entry::AUTH_ENTRY]); ?>
    <?php $this->endBody(); ?>
    </body>
    </html>
<?php $this->endPage(); ?>
