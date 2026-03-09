<?php

declare(strict_types=1);

/**
 * @see ErrorAction
 *
 * @var View $this
 * @var string $email
 * @var Exception $exception
 * @var string $message
 * @var string $name
 */

use davidhirtz\yii2\skeleton\web\ErrorAction;
use davidhirtz\yii2\skeleton\web\View;

?>
<section class="p-md">
    <div class="prose">
        <?= $this->render('@skeleton/views/error', [
            'email' => $email,
            'exception' => $exception,
            'message' => $message,
            'name' => $name,
        ]); ?>
    </div>
