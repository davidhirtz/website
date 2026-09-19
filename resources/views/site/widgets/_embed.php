<?php

declare(strict_types=1);

/**
 * @see Artwork::renderEmbed()
 *
 * @var View $this
 * @var Asset $asset
 */

use Hirtz\Cms\Widgets\Artwork;
use Hirtz\Media\Models\Asset;
use Hirtz\Skeleton\Web\View;
use yii\helpers\Html;

?>
<button class="embed:hidden group place-content-center cursor-pointer" data-embed-src="<?= $asset->getFormattedEmbedUrl(); ?>">
    <span class="embed-play absolute w-12 aspect-square rounded-full bg-white group-hover:bg-brand"></span>
</button>
<div data-embed-cc class="embed:block hidden place-content-center align-center p-4">
    <div class="text-center small prose">
        <p class="font-bold"><?= Yii::t('app', 'EMBED_TITLE') ?></p>
        <p>
            <?= Yii::t('app', 'EMBED_TEXT', [
                'policy' => Html::a(Yii::t('app', 'EMBED_PRIVACY_LINK'), '/privacy', [
                    'target' => '_blank',
                    'rel' => 'nofollow',
                ]),
            ]); ?>
        </p>
        <p>
            <cc-button class="btn cursor-pointer" data-categories="required,external" role="button"><?= Yii::t('app', 'EMBED_BUTTON_ACCEPT') ?></cc-button>
        </p>
    </div>
</div>
<iframe allow="autoplay; encrypted-media" allowfullscreen hidden></iframe>
