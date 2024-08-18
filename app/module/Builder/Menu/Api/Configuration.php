<?php declare(strict_types=1);

namespace Builder\Menu\Api;

use Framework\Api\Interface\Api;
use Builder\Site\Api\Abstract\Admin as AdminAbstract;
use Framework\Application\Manager\Http\Parameter\Post as ParameterPost;
use Framework\Application\Manager\Http\Parameter\Payload as ParameterPayload;

/**
 * @class Builder\Menu\Api\Configuration
 */
class Configuration extends AdminAbstract implements Api
{
    /**
     * @param string $identifier
     * @return array
     * @api GET /^menu\/configuration\/([a-zA-Z0-9_-]+)$/Usi
     */
    public function getConfiguration(string $identifier): array
    {
        return [];
    }

    /**
     * @param ParameterPost $payload
     * @return array
     * @api POST /^menu\/configuration$/
     */
    public function setConfiguration(ParameterPost $payload): array
    {
        return [];
    }
}