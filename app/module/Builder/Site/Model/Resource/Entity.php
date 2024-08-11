<?php declare(strict_types=1);

namespace Builder\Site\Model\Resource;

use Framework\Database\Abstract\Resource\Model as ResourceModel;

/**
 * @class Builder\Site\Model\Resource\Entity
 */
class Entity extends ResourceModel
{
    public function __construct()
    {
        parent::__construct('site');
    }
}