<?php declare(strict_types=1);

namespace Builder\Site\Api\Traits;

use Builder\Site\Model\Repository as SiteRepository;
use Builder\Site\Model\Site as SiteModel;
use Exception;
use Builder\Site\Helper\Header as HelperHeader;

/**
 * @package Builder\Site\Api\Traits
 * @trait Builder\Site\Api\Traits\Administrator
 */
trait Administrator
{
    /**
     * @var SiteModel
     */
    protected SiteModel $siteModel;

    /**
     * @throws Exception
     */
    public function construct()
    {
        $this->siteModel = SiteRepository::getInstance()->getByToken(
            $_SERVER['HTTP_HOST'],
            HelperHeader::getToken(),
            $_SERVER['REMOTE_ADDR']
        );
    }
}