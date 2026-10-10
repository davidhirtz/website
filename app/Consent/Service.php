<?php

declare(strict_types=1);

namespace App\Consent;

final readonly class Service
{
    /**
     * @param list<Cookie> $cookies
     */
    public function __construct(
        public string $name,
        public string $provider,
        public string $purpose,
        public ?string $privacyUrl = null,
        public array $cookies = [],
    ) {
    }
}
