<?php

declare(strict_types=1);

use App\Helpers\Html;

$categories = [
    'external' => [
        Yii::t('app', 'CC_CATEGORY_EXTERNAL_TITLE'),
        Yii::t('app', 'CC_CATEGORY_EXTERNAL_TEXT'),
    ],
    'analytics' => [
        Yii::t('app', 'CC_CATEGORY_ANALYTICS_TITLE'),
        Yii::t('app', 'CC_CATEGORY_ANALYTICS_TEXT'),
    ],
];

?>
<cc-modal class="fixed w-full max-w-120 right-0 bottom-0 xs:p-4 z-40" hidden>
    <div class="bg-black text-white p-4">
        <div data-id="cc-tab" class="hidden active:block active">
            <div class="grid gap-6">
                <div class="grid gap-2">
                    <div class="font-serif"><?= Yii::t('app', 'CC_TITLE') ?></div>
                    <div class="text-sm">
                        <p><?= Yii::t('app', 'CC_TITLE_TEXT') ?></p>
                    </div>
                </div>
                <div class="grid xs:grid-cols-3 gap-px">
                    <x-toggle class="btn" data-target-id="cc-tab" role="button"><?= Yii::t('app', 'CC_BUTTON_CUSTOMZIE') ?></x-toggle>
                    <cc-button class="btn" data-categories="required" role="button"><?= Yii::t('app', 'CC_BUTTON_REQUIRED') ?></cc-button>
                    <cc-button class="btn" role="button"><?= Yii::t('app', 'CC_BUTTON_ALLOW_ALL') ?></cc-button>
                </div>
            </div>
        </div>
        <form data-id="cc-tab" class="hidden active:block text-md">
            <div class="max-h-[80svh] flex flex-col gap-6">
                <div class="grid gap-6 overflow-y-scroll">
                    <div class="text-right">
                        <x-toggle class="cursor-pointer font-extralight text-xs tracking-widest  uppercase" data-target-id="cc-tab" role="button"><?= Yii::t('app', 'CC_BUTTON_BACK') ?></x-toggle>
                    </div>
                    <div class="grid gap-4">
                        <div class="grid gap-2 group active">
                            <div class="flex justify-between gap-4">
                                <x-toggle class="flex items-center gap-2 cursor-pointer" role="button">
                                    <svg class="w-5 aspect-square group-active:rotate-90 transition-transform" viewBox="0 0 50 50">
                                        <path class="stroke-cream stroke-2" d="m16 8 16 16-16 16"/>
                                    </svg>
                                    <div class="font-serif"><?= Yii::t('app', 'CC_CATEGORY_REQUIRED_TITLE') ?></div>
                                </x-toggle>
                                <label class="w-10">
                                    <input type="checkbox" class="cc-checkbox flex w-full h-5 appearance-none border border-current rounded-full" data-categories="required" id="cc-required" checked disabled>
                                </label>
                            </div>
                            <div class="hidden group-active:block text-sm">
                                <p><?= Yii::t('app', 'CC_CATEGORY_REQUIRED_TEXT') ?></p>
                            </div>
                        </div>
                        <?php foreach ($categories as $category => $lines) {
                            ?>
                            <hr class="border-current">
                            <div class="grid gap-2 group">
                                <div class="flex justify-between gap-4">
                                    <x-toggle class="flex items-center gap-2 cursor-pointer" role="button">
                                        <svg class="w-5 aspect-square group-active:rotate-90 transition-transform" viewBox="0 0 50 50">
                                            <path class="stroke-cream stroke-2" d="m16 8 16 16-16 16"/>
                                        </svg>
                                        <div class="font-serif"><?= Html::encode(array_shift($lines)); ?></div>
                                    </x-toggle>
                                    <label class="w-10">
                                        <input type="checkbox" class="cc-checkbox flex w-full h-5 appearance-none border border-current rounded-full" data-categories="<?= $category ?>" name="cc-<?= $category ?>">
                                    </label>
                                </div>
                                <div class="hidden group-active:block text-sm">
                                    <?php foreach ($lines as $line) {
                                        ?>
                                        <p><?= $line; ?></p>
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