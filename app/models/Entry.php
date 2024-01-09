<?php

namespace app\models;

use davidhirtz\yii2\cms\models\traits\FooterAttributeTrait;
use davidhirtz\yii2\cms\models\traits\MenuAttributeTrait;

/**
 * @property-read Section[] $sections {@see static::getSections()}
 */
class Entry extends \davidhirtz\yii2\cms\models\Entry
{
    use MenuAttributeTrait;
    use FooterAttributeTrait;
}
