<?php declare(strict_types=1);

namespace Framework\User\Model\Resource;

use Framework\Database\Abstract\Resource\Model;
use Framework\User\Model\User as UserModel;

/**
 * @class Framework\User\Model\Resource\User
 */
class User extends Model
{
    public function __construct()
    {
        parent::__construct('user');
    }
}