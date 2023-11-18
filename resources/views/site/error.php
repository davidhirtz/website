<?php
/**
 * @see \app\controllers\SiteController::actionError()
 * @var View $this
 */

use davidhirtz\yii2\skeleton\web\View;
use yii\helpers\Html;

$exception = Yii::$app->getErrorHandler()->exception;
$response = Yii::$app->getResponse();

$this->setTitle($response->getIsNotFound() ? Yii::t('app', 'Page not found') : Yii::t('app', 'Error'));
?>
<div class="wrap">
    <div class="section box">
        <div class="prose">
            <?php if ($response->getIsNotFound()) {
                ?>
                <h1><?= Yii::t('app', 'The requested page was not found'); ?></h1>
                <?php
            } elseif ($response->getIsForbidden()) {
                ?>
                <h1><?= Yii::t('app', 'Permission denied'); ?></h1>
                <?php
            } else {
                ?>
                <h1><?= Yii::t('app', 'Internal Server Error'); ?></h1>
                <p>
                    <?= Yii::t('app', 'Please get in touch with {email}', [
                        'email' => Html::a(Yii::$app->params['email'], 'mailto:' . Yii::$app->params['email']),
                    ]); ?>
                </p>
                <?php if (Yii::$app->getUser()->can('admin')) {
                    ?>
                    <p></p>
                    <h2><?= $exception->getMessage() ?: 'Unknown Error'; ?></h2>
                    <p>Code: <?= $exception->getCode(); ?></p>
                    <p></p>
                    <p><?= $exception->getTraceAsString() ?></p>
                    <?php
                } ?>
                <?php
            } ?>
        </div>
    </div>
</div>
