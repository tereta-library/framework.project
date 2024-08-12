<?php declare(strict_types=1);

namespace Builder\Site\Model\Resource;

use Framework\Database\Abstract\Resource\Model as ResourceModel;
use Exception;

/**
 * @class Builder\Site\Model\Resource\Domain
 */
class Domain extends ResourceModel
{
    /**
     * @throws Exception
     */
    public function __construct()
    {
        parent::__construct('siteDomain');
    }
}
