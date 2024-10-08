<?php declare(strict_types=1);

namespace Builder\Site\Model\Resource\Site\Configuration;

use Framework\Database\Abstract\Resource\Model as AbstractResourceModel;
use Exception;

/**
 * Generated by www.Tereta.dev on 2024-09-08 12:41:26
 *
 * @class Builder\Site\Model\Resource\Site\Configuration\Value
 * @package Builder\Site\Model\Resource\Site\Configuration
 */
class Value extends AbstractResourceModel 
{
    /**
     * @throws Exception
     */
    public function __construct()
    {
        parent::__construct('siteConfigurationValue');
    }
}
