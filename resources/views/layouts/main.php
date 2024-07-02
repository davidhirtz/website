<?php
/**
 * @var View $this
 * @var string $content
 */

use app\helpers\Html;
use davidhirtz\yii2\skeleton\web\View;
use davidhirtz\yii2\skeleton\widgets\AdminButton;
use yii\helpers\Url;

Yii::$app->get('vite')->register('/resources/js/app.ts');
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
        <link rel="apple-touch-icon" sizes="180x180" href="/images/favicons/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="/images/favicons/favicon-32x32.png">
        <link rel="manifest" href="/manifest.json">
        <?php $this->head(); ?>
    </head>
    <body>
    <?php $this->beginBody(); ?>
    <main>
        <?= $content; ?>
    </main>
    <div id="cc" class="fixed p-3 sm:p-5 block-active">
        <div class="cc-content block-active active">
            <p>
                Wir benötigen Ihre Zustimmung, um Cookies und Inhalte von Drittanbietern (ggf. auch aus dem EU-Ausland)
                einzubinden. Weitere Informationen in unserer <a href="/datenschutz">Datenschutzerklärung</a>.
            </p>
            <div class="cc-buttons flex">
                <button class="cc-button cc-secondary cc-hover toggle strong" data-target=".cc-content">
                    Anpassen
                </button>
                <button class="cc-button cc-secondary cc-hover cc-confirm strong" data-consent="none">
                    Alle ablehnen
                </button>
                <button class="cc-button cc-hover cc-confirm strong" data-consent="analytics,external">
                    Alle zustimmen
                </button>
            </div>
        </div>
        <div class="cc-content block-active">
            <div class="cc-wrap flex flex-col">
                <div class="overflow-y-scroll">
                    <div class="text-right">
                        <button class="strong small toggle" data-target=".cc-content">zurück</button>
                    </div>
                    <details class="cc-detail">
                        <summary class="flex items-center justify-between">
                            <div class="cc-title flex items-center strong">
                                Technisch notwendige Cookies
                            </div>
                            <label class="cc-label">
                                <input type="checkbox" class="cc-checkbox relative" disabled data-consent="none" checked>
                            </label>
                        </summary>
                        <div class="cc-description">
                            <p>
                                Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor
                                incididunt ut labore
                            </p>
                        </div>
                    </details>
                    <details class="cc-detail">
                        <summary class="flex items-center justify-between">
                            <div class="cc-title flex items-center strong">
                                Inhalte von Drittanbietern
                            </div>
                            <label class="cc-label">
                                <input type="checkbox" class="cc-checkbox cc-hover relative" data-consent="external">
                            </label>
                        </summary>
                        <div class="cc-description">
                            <p>
                                Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor
                                incididunt ut labore
                            </p>
                        </div>
                    </details>
                    <details id="cc-analytics" class="cc-detail">
                        <summary class="flex items-center justify-between">
                            <dic class="cc-title flex items-center strong" >
                                Statistiken
                            </dic>
                            <label class="cc-label">
                                <input type="checkbox" class="cc-checkbox cc-hover relative" data-consent="analytics">
                            </label>
                        </summary>
                        <div class="cc-description">
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
                    </details>
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
    </body>
    </html>
<?php $this->endPage(); ?>