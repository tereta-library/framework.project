<?php declare(strict_types=1);

namespace Builder\Site\Block;

use Builder\Site\Model\Site as SiteModel;
use Framework\View\Php\Template;
use Builder\Site\Model\Repository as SiteRepository;
use Exception;

/**
 * @class Builder\Site\Block\Data
 */
class Data extends Template
{
    /**
     * @var SiteModel $siteModel
     */
    private SiteModel $siteModel;

    /**
     * @return void
     * @throws Exception
     */
    protected function construct(): void
    {
        $this->siteModel = SiteRepository::getInstance()->getByDomain($_SERVER['HTTP_HOST'] ?? new Exception('Domain not found'));
        $this->assign('site', $this->siteModel->getData());
    }

    public function phoneNumber($test): string
    {
        return $test;
    }

    public function concat($prefix, $test): string
    {
        return $prefix . $test;
    }

}