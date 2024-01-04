<?php

namespace app\models;

use davidhirtz\yii2\cms\models\traits\FooterAttributeTrait;
use davidhirtz\yii2\cms\models\traits\MenuAttributeTrait;

class Entry extends \davidhirtz\yii2\cms\models\Entry
{
    use MenuAttributeTrait;
    use FooterAttributeTrait;
}
