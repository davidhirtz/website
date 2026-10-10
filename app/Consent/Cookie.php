<?php

declare(strict_types=1);

namespace App\Consent;

final readonly class Cookie
{
    /**
     * @param string $name a `*` matches any characters (`_ga_*`)
     * @param bool $thirdParty set on the provider's domain, which the script can neither see nor delete
     */
    public function __construct(
        public string $name,
        public string $duration,
        public bool $thirdParty = false,
    ) {
    }
}
