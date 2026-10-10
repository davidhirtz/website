<?php

declare(strict_types=1);

namespace App\Consent;

final readonly class Category
{
    /**
     * @param list<Service> $services
     */
    public function __construct(
        public string $id,
        public string $title,
        public string $description,
        public array $services = [],
        public bool $required = false,
    ) {
    }

    /**
     * @return list<string>
     */
    public function getFirstPartyCookieNames(): array
    {
        $names = [];

        foreach ($this->services as $service) {
            foreach ($service->cookies as $cookie) {
                if (!$cookie->thirdParty) {
                    $names[] = $cookie->name;
                }
            }
        }

        return $names;
    }
}
