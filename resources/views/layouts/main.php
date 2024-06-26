<?php
/**
 * @var View $this
 * @var string $content
 */

use app\helpers\Html;
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
        <link rel="manifest" href="/manifest.json">
        <?php $this->head(); ?>
    </head>
    <body>
    <?php $this->beginBody(); ?>
    <main class="inline-table">
        <?= $content; ?>
    </main>
    <div id="cc" class="block-active">
        <div class="cc-content block-active active">
            <p>
                Wir benötigen Ihre Zustimmung, um Cookies und Inhalte von Drittanbietern (ggf. auch aus dem EU-Ausland)
                einzubinden. Weitere Informationen in unserer <a href="/datenschutz">Datenschutzerklärung</a>.
            </p>
            <div class="cc-buttons flex">
                <button class="cc-button cc-secondary cc-hover toggle strong"
                        data-target=".cc-content">
                    Anpassen
                </button>
                <button class="cc-button cc-secondary cc-hover cc-confirm strong"
                        data-consent="none">
                    Alle ablehnen
                </button>
                <button class="cc-button cc-hover cc-confirm strong"
                        data-consent="analytics,external">
                    Alle zustimmen
                </button>
            </div>
        </div>
        <div class="cc-content block-active">
            <div class="cc-wrap flex flex-col">
                <div class="cc-scrollable">
                    <div class="text-right">
                        <button class="strong small toggle" data-target=".cc-content">zurück</button>
                    </div>
                    <div id="cc-default" class="cc-detail">
                        <div class="cc-summary flex items-center justify-between">
                            <button class="cc-title flex items-center strong toggle"
                                    data-target="#cc-default">
                                Technisch notwendige Cookies
                            </button>
                            <label class="cc-label">
                                <input type="checkbox" class="cc-checkbox" disabled data-consent="none" checked>
                            </label>
                        </div>
                        <div class="cc-description hidden">
                            <p>
                                Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor
                                incididunt ut labore
                            </p>
                        </div>
                    </div>
                    <div id="cc-external" class="cc-detail">
                        <div class="cc-summary flex items-center justify-between">
                            <button class="cc-title flex items-center strong toggle"
                                    data-target="#cc-external">
                                Inhalte von Drittanbietern
                            </button>
                            <label class="cc-label">
                                <input type="checkbox" class="cc-checkbox cc-hover" data-consent="external">
                            </label>
                        </div>
                        <div class="cc-description hidden">
                            <p>
                                Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor
                                incididunt ut labore
                            </p>
                        </div>
                    </div>
                    <div id="cc-analytics" class="cc-detail">
                        <div class="cc-summary flex items-center justify-between">
                            <button class="cc-title flex items-center strong toggle"
                                    data-target="#cc-analytics">
                                Statistiken
                            </button>
                            <label class="cc-label">
                                <input type="checkbox" class="cc-checkbox cc-hover" data-consent="analytics">
                            </label>
                        </div>
                        <div class="cc-description hidden">
                            <p>
                                Statistik-Cookies helfen Webseiten-Besitzern zu verstehen, wie Besucher mit Webseiten
                                interagieren, indem Informationen anonym gesammelt und gemeldet werden.
                                Wenn Sie in die Statistik-Cookies einwilligen, werden ggf. personenbezogene Daten (z.B.
                                Ihre IP-Adresse) an Dritte außerhalb der EU weitergegeben. Sie willigen in diesem Fall
                                ausdrücklich ein.
                                Weitere Informationen, auch zu den Risiken und zur Widerrufsmöglichkeit, finden Sie in
                                unserer
                                <a href="/datenschutz">Datenschutzerklärung</a> hier.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="cc-buttons flex">
                    <button class="cc-button cc-hover cc-confirm strong">
                        Ausgewählte zustimmen
                    </button>
                    <button class="cc-button cc-hover cc-confirm strong"
                            data-consent="analytics,external">
                        Alle zustimmen
                    </button>
                </div>
            </div>
        </div>
    </div>
    <?php if (Yii::$app->getUser()->can('entryUpdate')) {
        echo AdminButton::widget();
    } ?>
    <?php $this->endBody(); ?>
    <script src="<?= $this->getFilenameWithVersion('js/site.js'); ?>"></script>
    </body>
    </html>
<?php $this->endPage(); ?>