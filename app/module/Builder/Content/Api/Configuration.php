<?php declare(strict_types=1);

namespace Builder\Content\Api;

use Builder\Site\Api\Traits\Administrator as AdministratorTrait;
use Framework\Api\Interface\Api;
use Framework\Application\Manager\Http\Parameter\Post as ParameterPost;
use Framework\Application\Manager\Http\Parameter\Payload;

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

    /**
     * @api POST /^content\/save$/Usi
     * @param Payload $payload
     * @return array
     */
    public function saveContent(Payload $payload): array
    {
        return [];
    }
}