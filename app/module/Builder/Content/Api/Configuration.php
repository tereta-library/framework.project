<?php declare(strict_types=1);

namespace Builder\Content\Api;

use Builder\Site\Api\Traits\Administrator as AdministratorTrait;
use Framework\Api\Interface\Api;

/**
 * @class Builder\Content\Api\Configuration
 */
class Configuration implements Api
{
    use AdministratorTrait;

    /**
     * @api GET /^content\/get\/([a-zA-Z0-9_-]+)?$/Usi
     * @param string|null $identifier
     * @return array
     */
    public function getContent(?string $identifier = null): array
    {
        return [
            'id'          => null,
            'identifier'  => null,
            'seoUri'      => null,
            'seoTitle'    => null,
            'title'       => null,
            'description' => null,
            'content'     => null,
        ];
    }
}