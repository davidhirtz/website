<?php
/**
 * @var string $link
 */

use yii\helpers\Html;

?>
<button class="video overlay" data-iframe-src="<?= $link; ?>">
    <span class="video-btn"></span>
</button>
<div class="video-cc align-center hidden">
    <div class="video-cc-text box text-center small">
        <div class="text">
            <p class="strong"><?= Yii::t('app', 'External content') ?></p>
            <p>
                <?= Yii::t('app', 'Please accept cookies to load this external video. More information can be found in our {policy}.', [
                    'policy' => Html::a(Yii::t('app', 'privacy policy'), '/privacy', [
                        'target' => '_blank',
                        'rel' => 'nofollow',
                    ]),
                ]); ?>
            </p>
            <p>
                <button class="btn video cc-button" data-consent="marketing" data-iframe-src="<?= $link; ?>"><?= Yii::t('app', 'Accept cookies') ?></button>
            </p>
        </div>
    </div>
</div>

<iframe class="video-iframe overlay" frameborder="0" allow="autoplay; encrypted-media" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
