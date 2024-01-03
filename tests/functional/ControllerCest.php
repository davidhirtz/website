<?php

/**
 * @noinspection PhpUnused
 */

namespace app\tests\functional;

use app\models\Entry;
use app\tests\support\FunctionalTester;

class ControllerCest
{
    public function checkHomepage(FunctionalTester $I): void
    {
        $I->amOnPage('/');
        $I->seeResponseCodeIs(Entry::find()->exists() ? 200 : 404);
    }

    public function checkApplicationHealth(FunctionalTester $I): void
    {
        $I->amOnPage('/application-health');
        $I->seeResponseCodeIsSuccessful();
    }

    public function checkSitemapXML(FunctionalTester $I): void
    {
        $I->amOnPage('/sitemap.xml');
        $I->seeResponseCodeIsSuccessful();
        $I->haveHttpHeader('Content-Type', 'text/html; charset=UTF-8"');
    }
}
