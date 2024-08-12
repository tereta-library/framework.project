<?php declare(strict_types=1);

namespace Builder\Site\Model\Resource;

use Framework\Database\Abstract\Resource\Model;
use Exception;

class User extends Model
{
    /**
     * @throws Exception
     */
    public function __construct()
    {
        parent::__construct('siteUser');
    }
}
