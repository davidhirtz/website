<?php

declare(strict_types=1);

namespace app\models;

use davidhirtz\yii2\cms\models\traits\FooterAttributeTrait;
use davidhirtz\yii2\cms\models\traits\MenuAttributeTrait;

/**
 * @property-read Asset[] $assets
 * @property-read Section[] $sections
 */
final class Entry extends \davidhirtz\yii2\cms\models\Entry
{
    use MenuAttributeTrait;
    use FooterAttributeTrait;
}
