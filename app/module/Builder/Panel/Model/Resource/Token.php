<?php declare(strict_types=1);

namespace Builder\Panel\Model\Resource;

use Framework\Database\Abstract\Resource\Model as ResourceModel;

/**
 * @class Builder\Panel\Model\Resource\Token
 */
class Token extends ResourceModel
{
    public function __construct()
    {
        parent::__construct('userToken', 'id');
    }
}