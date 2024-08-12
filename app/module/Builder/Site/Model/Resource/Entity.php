<?php declare(strict_types=1);

namespace Builder\Site\Model\Resource;

use Framework\Database\Abstract\Resource\Model as ResourceModel;
use Exception;

/**
 * @class Builder\Site\Model\Resource\Entity
 */
class Entity extends ResourceModel
{
    /**
     * @throws Exception
     */
    public function __construct()
    {
        parent::__construct('site');
    }

    /**
     * @param string $domain
     * @param string $token
     * @return array
     */
    public function loadByToken(string $domain, string $token): array
    {
        return [];
    }
}