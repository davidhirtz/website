<?php

declare(strict_types=1);

namespace App\Models;

use Hirtz\Cms\Models\Types\SectionType;
use Hirtz\Media\Helpers\Size;
use Override;
use Yii;

final class Section extends \Hirtz\Cms\Models\Section
{
    public const int TYPE_COLUMN = self::TYPE_DEFAULT;
    public const int TYPE_FULL_WIDTH = 2;

    /**
     * @return list<SectionType>
     */
    #[Override]
    public function getTypes(): array
    {
        return [
            SectionType::make(self::TYPE_COLUMN)
                ->name(Yii::t('app', 'SECTION_TYPE_COLUMN'))
                ->cssClass('grid gap-sm w-full sm:w-1/2')
                ->wrapper('flex flex-wrap justify-center')
                ->sizes(Size::breakpoint('sm', '100vw'), '50vw'),
            SectionType::make(self::TYPE_FULL_WIDTH)
                ->name(Yii::t('app', 'SECTION_TYPE_FULL_WIDTH'))
                ->cssClass('grid gap-sm w-full')
                ->wrapper('flex flex-wrap justify-center')
                ->sizes('100vw'),
        ];
    }
}
