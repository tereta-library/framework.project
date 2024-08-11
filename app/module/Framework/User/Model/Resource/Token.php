<?php declare(strict_types=1);

namespace Framework\User\Model\Resource;

use Framework\Database\Abstract\Resource\Model;

/**
 * @class Framework\User\Model\Resource\Token
 */
class Token extends Model
{
    public function __construct()
    {
        parent::__construct('userToken');
    }
}