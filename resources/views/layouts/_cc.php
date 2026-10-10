<?php

declare(strict_types=1);

/**
 * @var View $this
 */

use App\Consent\Consent;
use App\Helpers\Html;
use Hirtz\Skeleton\Web\View;

$links = Yii::t('app', 'CC_LINKS', [
    'privacy' => Html::a(Yii::t('app', 'CC_PRIVACY_LINK'), Consent::PRIVACY_URL, ['class' => 'underline']),
    'imprint' => Html::a(Yii::t('app', 'CC_IMPRINT_LINK'), Consent::IMPRINT_URL, ['class' => 'underline']),
]);

?>
<cc-modal class="fixed w-full max-w-120 right-0 bottom-0 xs:p-4 z-40" role="dialog" aria-labelledby="cc-title" hidden>
    <div class="bg-black text-white p-4">
        <div data-id="cc-tab" class="hidden active:block active">
            <div class="grid gap-6">
                <div class="grid gap-2">
                    <div class="font-serif" id="cc-title"><?= Yii::t('app', 'CC_TITLE') ?></div>
                    <div class="text-sm grid gap-2">
                        <p><?= Yii::t('app', 'CC_TITLE_TEXT') ?></p>
                        <p><?= $links ?></p>
                    </div>
                </div>
                <div class="grid xs:grid-cols-3 gap-px">
                    <x-toggle class="btn" data-target-id="cc-tab" role="button"><?= Yii::t('app', 'CC_BUTTON_CUSTOMIZE') ?></x-toggle>
                    <cc-button class="btn" data-categories="required" role="button"><?= Yii::t('app', 'CC_BUTTON_REQUIRED') ?></cc-button>
                    <cc-button class="btn" role="button"><?= Yii::t('app', 'CC_BUTTON_ALLOW_ALL') ?></cc-button>
                </div>
            </div>
        </div>
        <form data-id="cc-tab" class="hidden active:block text-md">
            <div class="max-h-[80svh] flex flex-col gap-6">
                <div class="grid gap-6 overflow-y-scroll">
                    <div class="text-right">
                        <x-toggle class="cursor-pointer font-extralight text-xs tracking-widest uppercase" data-target-id="cc-tab" role="button"><?= Yii::t('app', 'CC_BUTTON_BACK') ?></x-toggle>
                    </div>
                    <div class="grid gap-4">
                        <?php foreach (Consent::getCategories() as $i => $category) {
                            if ($i) {
                                echo '<hr class="border-current">';
                            } ?>
                            <div class="grid gap-2 group">
                                <div class="flex justify-between gap-4">
                                    <x-toggle class="flex items-center gap-2 cursor-pointer" role="button">
                                        <svg class="w-5 aspect-square group-active:rotate-90 transition-transform" viewBox="0 0 50 50">
                                            <path class="stroke-cream stroke-2" d="m16 8 16 16-16 16"/>
                                        </svg>
                                        <div class="font-serif"><?= Html::encode($category->title); ?></div>
                                    </x-toggle>
                                    <label class="w-10">
                                        <span class="sr-only"><?= Html::encode($category->title); ?></span>
                                        <input type="checkbox" class="cc-checkbox flex w-full h-5 appearance-none border border-current rounded-full" data-categories="<?= $category->id ?>" name="cc-<?= $category->id ?>"<?= $category->required ? ' checked disabled' : ''; ?>>
                                    </label>
                                </div>
                                <div class="hidden group-active:grid gap-3 text-sm">
                                    <p><?= Html::encode($category->description); ?></p>
                                    <?php foreach ($category->services as $service) {
                                        ?>
                                        <dl class="grid grid-cols-[auto_1fr] gap-x-3 gap-y-1 border-t border-current/30 pt-2 text-xs">
                                            <dt class="font-bold"><?= Yii::t('app', 'CC_SERVICE_LABEL') ?></dt>
                                            <dd><?= Html::encode($service->name); ?></dd>
                                            <dt class="font-bold"><?= Yii::t('app', 'CC_PROVIDER_LABEL') ?></dt>
                                            <dd><?= Html::encode($service->provider); ?></dd>
                                            <dt class="font-bold"><?= Yii::t('app', 'CC_PURPOSE_LABEL') ?></dt>
                                            <dd><?= Html::encode($service->purpose); ?></dd>
                                            <?php if ($service->cookies) {
                                                ?>
                                                <dt class="font-bold"><?= Yii::t('app', 'CC_COOKIES_LABEL') ?></dt>
                                                <dd>
                                                    <?php foreach ($service->cookies as $cookie) {
                                                        ?>
                                                        <code><?= Html::encode($cookie->name); ?></code> (<?= Html::encode($cookie->duration); ?>)<br>
                                                        <?php
                                                    } ?>
                                                </dd>
                                                <?php
                                            } ?>
                                            <?php if ($service->privacyUrl) {
                                                ?>
                                                <dt class="font-bold"><?= Yii::t('app', 'CC_PRIVACY_LINK') ?></dt>
                                                <dd><?= Html::a(Html::encode($service->privacyUrl), $service->privacyUrl, [
                                                    'class' => 'underline break-all',
                                                    'target' => '_blank',
                                                    'rel' => 'noopener',
                                                ]); ?></dd>
                                                <?php
                                            } ?>
                                        </dl>
                                        <?php
                                    } ?>
                                </div>
                            </div>
                            <?php
                        } ?>
                    </div>
                </div>
                <div class="grid xs:grid-cols-2 gap-px">
                    <button type="submit" class="btn"><?= Yii::t('app', 'CC_BUTTON_ALLOW_SELECTION') ?></button>
                    <cc-button class="btn" role="button"><?= Yii::t('app', 'CC_BUTTON_ALLOW_ALL') ?></cc-button>
                </div>
            </div>
        </form>
    </div>
</cc-modal>
