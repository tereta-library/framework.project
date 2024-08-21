<?php declare(strict_types=1);

namespace Builder\Site\Api\Abstract;

use Builder\Site\Helper\Header as HelperHeader;
use Builder\Site\Model\Site as EntityModel;
use Builder\Site\Model\Repository as SiteRepository;

/**
 * @package Builder\Site\Api\Abstract
 * @class Builder\Site\Api\Abstract\Admin
 */
abstract class Admin
{
    /**
     * @var EntityModel
     */
    protected EntityModel $entityModel;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $this->entityModel = SiteRepository::getInstance()->getByToken(
            $_SERVER['HTTP_HOST'],
            HelperHeader::getToken(),
            $_SERVER['REMOTE_ADDR']
        );
    }
}