# Cookie consent

[← Documentation](README.md)

The banner (`layouts/_cc.php`) and its script render what the skeleton's `consent` component describes. The
categories and services are configured in `config/prod.php`; the [yii2-skeleton](https://github.com/davidhirtz/yii2-skeleton) documentation lists the
services it ships (Google Analytics, Tag Manager, Ads, Maps, YouTube, Vimeo) and shows how to add any other. Per
project:

- Set `gtagId` in `config/params.php` to a Google Tag Manager container (`GTM-…`) or a Google Analytics stream (`G-…`).
  The "Analytics" category is offered only then; nothing of Google's loads before a visitor allows it, and Consent Mode
  v2 signals tell the tags what was allowed. A container sees the choice as the `cc_update` event and its
  `cc_categories` variable.
- List every service the site embeds (booking forms, podcasts, maps) under `ExternalCategory` and every tag a Tag
  Manager container adds under `AnalyticsCategory` or `MarketingCategory`, and adjust the banner texts in `messages/`.
- A changed service or cookie asks every visitor again on its own: the stored and logged version includes a
  fingerprint of them. Raise the component's `version` when only the wording changed but visitors should be asked
  again.
- With `YII_DEBUG`, the browser console warns about every first-party cookie the list does not declare. Click through
  the site with everything allowed before going live and after adding a tag. Cookies an iframe sets on its provider's
  domain are invisible to the script, so they are listed from the provider's documentation (`thirdParty()`).

Every decision is logged with a random ID, the version and the categories, without an IP address, and listed in the
admin under "System" → "Cookie consents" (`enableConsentLog` on the admin module). Keep the log as long as the
consent plus the limitation period and clear it from a cron job:

```php
'modules' => [
    'admin' => [
        'consentLifetime' => 4 * 365 * 86400,
    ],
],
```

```bash
php yii consent/clear
```
