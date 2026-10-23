<?php

declare(strict_types=1);

/**
 * The 404 at `/` while there is no homepage entry yet, linking to the admin. The bundle's own view is wrapped here, as
 * `viewPath` points the cms at `resources/views` and would not find it otherwise.
 *
 * @see SiteController::actionIndex()
 *
 * @var View $this
 */

use Hirtz\Cms\Controllers\SiteController;
use Hirtz\Skeleton\Web\View;

?>
<section class="p-md">
    <div class="prose">
        <?= $this->render('@cms/../resources/views/site/missing-index'); ?>
    </div>
</section>
