<?php declare(strict_types=1);

namespace Builder\Site\Block;

use Builder\Site\Model\Site as SiteModel;
use Framework\Application\Manager as ApplicationManager;
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
        $this->assign('siteModel', $this->siteModel);

        $applicationConfig = ApplicationManager::getInstance()->getConfig();

        $this->assign('siteLogoImage', $this->siteModel->getLogoImageUrl() ?? $applicationConfig->get('publicMediaUri') . '/default/logo.png');
        $this->assign('siteIconImage', $this->siteModel->getIconImageUrl() ?? $applicationConfig->get('publicMediaUri') . '/default/icon.png');

        parent::construct();
    }

    /**
     * @param string $phoneNumber
     * @return string
     */
    public function phoneNumber(string $phoneNumber): string
    {
        return preg_replace('/[^+0-9]/', '', $phoneNumber);
    }
}