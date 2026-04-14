<?php

declare(strict_types=1);

/**
 * @var Asset $asset
 */

use app\models\Asset;
use yii\helpers\Html;

?>
<button class="embed:hidden group place-content-center cursor-pointer" data-embed-src="<?= $asset->getFormattedEmbedUrl(); ?>">
    <span class="embed-play absolute w-12 aspect-square rounded-full bg-white group-hover:bg-brand"></span>
</button>
<div data-embed-cc class="embed:block hidden place-content-center align-center p-4">
    <div class="text-center small prose">
        <p class="font-bold"><?= Yii::t('app', 'External content') ?></p>
        <p>
            <?= Yii::t('app', 'Please accept cookies to load this external video. More information can be found in our {policy}.', [
                'policy' => Html::a(Yii::t('app', 'privacy policy'), '/privacy', [
                    'target' => '_blank',
                    'rel' => 'nofollow',
                ]),
            ]); ?>
        </p>
        <p>
            <cc-button class="btn cursor-pointer" data-categories="required,external" role="button"><?= Yii::t('app', 'Accept cookies') ?></cc-button>
        </p>
    </div>
</div>
<iframe allow="autoplay; encrypted-media" allowfullscreen hidden></iframe>
