<?php declare(strict_types=1);

namespace Builder\Site\Block;

use Builder\Site\Model\Repository as SiteRepository;
use Framework\Database\Exception\Db\Repository as RepositoryException;
use Framework\View\Php\Template;
use Exception;
use Builder\Site\Model\Site\Configuration\Repository as ConfigurationRepository;

/**
 * @class Builder\Site\Block\Config
 */
class Config extends Template
{
    /**
     * @return void
     * @throws Exception
     */
    protected function construct(): void
    {
        $siteModel = SiteRepository::getInstance()->getByDomain($_SERVER['HTTP_HOST'] ?? new Exception('Domain not found'));
        $this->assign('siteModel', $siteModel);
        $configurationRepository = ConfigurationRepository::getSiteInstance($siteModel->get('id'));

        try {
            $this->assign('value', $configurationRepository->get($this->get('identifier')));
        } catch (RepositoryException $e) {
            $configurationRepository->register($this->get('identifier'));
            $this->assign('value', $configurationRepository->get($this->get('identifier')) ?? '');
        }
        //$this->assign('value', 'Дзвоніть з понеділка по неділю, з 9:00 до 19:00, за номером телефона +380687445501.');
    }

    /**
     * @return string
     * @throws Exception
     */
    public function render(): string
    {
        return '<!-- @dataAdmin ' . json_encode([
            'site' => ['identifier' => $this->get('identifier'), 'label' => $this->get('label'), 'namespace' => 'config']
            ]) . ' -->' .
            parent::render();
    }
}