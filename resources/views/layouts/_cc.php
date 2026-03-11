<?php

declare(strict_types=1);

use app\helpers\Html;

$categories = [
    'external' => [
        Yii::t('app', 'External media'),
        Yii::t('app', 'Content from video platforms or mapping services is disabled by default. If cookies from external media sources are accepted, access to this content no longer requires manual consent.'),
    ],
    'analytics' => [
        Yii::t('app', 'Analytics'),
        Yii::t('app', 'Cookies set by our third-party partner Google Analytics are used to help us to understand how our visitors interact with the website.'),
    ],
];

?>
<cc-modal class="fixed w-full xs:max-w-100 right-0 bottom-0 xs:p-4 z-40" hidden>
    <div class="bg-black text-white p-4">
        <div data-id="cc-tab" class="hidden active:block active">
            <div class="grid gap-4 text-xs">
                <div class="grid gap-3">
                    <p><?= Yii::t('app', 'We use cookies to personalise content and ads, to provide social media features and to analyse our traffic. We also share information about your use of our site with our social media, advertising and analytics partners who may combine it with other information that you\'ve provided to them or that they\'ve collected from your use of their services.') ?></p>
                </div>
                <div class="grid xs:flex gap-[1px] bg-white border border-current text-center">
                    <x-toggle class="grid items-center p-2 w-full bg-black text-white/80 hover:bg-white hover:text-black transition-colors cursor-pointer" data-target-id="cc-tab" role="button">
                        <?= Yii::t('app', 'Customize') ?>
                    </x-toggle>
                    <cc-button class="grid items-center bg-black p-2 w-full text-white/80 hover:bg-white hover:text-black transition-colors cursor-pointer" data-categories="required" role="button">
                        <?= Yii::t('app', 'Essential only') ?>
                    </cc-button>
                    <cc-button class="grid items-center bg-black p-2 w-full font-bold hover:bg-white hover:text-black transition-colors cursor-pointer" role="button">
                        <?= Yii::t('app', 'Allow all') ?>
                    </cc-button>
                </div>
            </div>
        </div>
        <form data-id="cc-tab" class="hidden active:block text-sm">
            <div class="max-h-[80svh] flex flex-col gap-4">
                <div class="grid gap-4 overflow-y-scroll">
                    <div class="flex justify-between items-center">
                        <div class="font-bold"><?= Yii::t('app', 'Customize cookies') ?></div>
                        <x-toggle class="cursor-pointer" data-target-id="cc-tab" role="button">
                            <?= Yii::t('app', 'back') ?>
                        </x-toggle>
                    </div>
                    <div class="grid gap-4">
                        <div class="grid gap-2 group active">
                            <div class="flex justify-between gap-4">
                                <x-toggle class="flex items-center gap-2 cursor-pointer" role="button">
                                    <svg class="w-5 aspect-square group-active:rotate-90 transition-transform" viewBox="0 0 50 50">
                                        <g id="cc-arrow">
                                            <path class="stroke-white stroke-2" d="m16 8 16 16-16 16"/>
                                        </g>
                                    </svg>
                                    <div class="leading-none"><?= Yii::t('app', 'Necessary') ?></div>
                                </x-toggle>
                                <label class="w-10 ">
                                    <input type="checkbox" class="cc-checkbox flex w-full h-5 appearance-none border border-current rounded-full" data-categories="required" id="cc-required" checked disabled>
                                </label>
                            </div>
                            <div class="hidden group-active:block text-xs">
                                <p><?= Yii::t('app', 'Necessary cookies enable basic functionality and are required for the website to function properly.') ?></p>
                            </div>
                        </div>
                        <?php foreach ($categories as $category => $lines) {
                            ?>
                            <hr class="border-current">
                            <div class="grid gap-2 group">
                                <div class="flex justify-between gap-4">
                                    <x-toggle class="flex items-center gap-2 cursor-pointer" role="button">
                                        <svg class="w-5 aspect-square group-active:rotate-90 transition-transform" viewBox="0 0 50 50">
                                            <g id="cc-arrow">
                                                <path class="stroke-white stroke-2" d="m16 8 16 16-16 16"/>
                                            </g>
                                        </svg>
                                        <div class="leading-none"><?= Html::encode(array_shift($lines)); ?></div>
                                    </x-toggle>
                                    <label class="w-10">
                                        <input type="checkbox" class="cc-checkbox flex w-full h-5 appearance-none border border-current rounded-full" data-categories="<?= $category ?>" name="cc-<?= $category ?>">
                                    </label>
                                </div>
                                <div class="hidden group-active:block text-xs">
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
                <div class="flex gap-[1px] bg-white border border-current text-center text-sm">
                    <button type="submit" class="grid items-center bg-black p-2 w-full text-white/80 hover:bg-white hover:text-black transition-colors cursor-pointer">
                        <?= Yii::t('app', 'Allow selection') ?>
                    </button>
                    <cc-button class="grid items-center bg-black p-2 w-full font-bold hover:bg-white hover:text-black transition-colors cursor-pointer" role="button">
                        <?= Yii::t('app', 'Allow all') ?>
                    </cc-button>
                </div>
            </div>
        </form>
    </div>
</cc-modal>