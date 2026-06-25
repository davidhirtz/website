<?php

declare(strict_types=1);

namespace app\modules\media;

use app\models\Entry;
use app\models\Section;
use Override;

class Module extends \davidhirtz\yii2\media\Module
{
    #[Override]
    public function init(): void
    {
        $this->addTransformationsFromTypeOptions(Entry::getTypes());
        $this->addTransformationsFromTypeOptions(Section::getTypes());

        parent::init();
    }
}
