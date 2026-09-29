<?php

declare(strict_types=1);

namespace App\Tests;

use Hirtz\Skeleton\Validators\HtmlValidator;
use Yii;

/**
 * What `config/prod.php` and the message files promise, checked without rendering anything.
 */
final class ConfigurationTest extends TestCase
{
    public function testTheEditorAllowsOnlyTheProjectsHtml(): void
    {
        $validator = Yii::$container->get(HtmlValidator::class);

        self::assertInstanceOf(HtmlValidator::class, $validator);
        // The project's tags are added to the skeleton's defaults.
        foreach (['h1', 'h2', 'h3'] as $tag) {
            self::assertContains($tag, $validator->allowedHtmlTags);
        }

        self::assertSame(['a' => ['btn']], $validator->allowedClasses);
    }

    /**
     * `./yii message` keeps the files in sync with the code; this catches a hand edit to one language only.
     */
    public function testEveryLanguageTranslatesTheSameKeys(): void
    {
        $root = dirname(__DIR__) . '/messages';
        $source = require "$root/en-US/app.php";

        self::assertIsArray($source);
        self::assertNotEmpty($source);

        foreach (['de', 'en-US'] as $language) {
            $messages = require "$root/$language/app.php";

            self::assertIsArray($messages);
            self::assertSame(array_keys($source), array_keys($messages), $language);
            self::assertNotContains('', $messages, $language);
        }
    }
}
