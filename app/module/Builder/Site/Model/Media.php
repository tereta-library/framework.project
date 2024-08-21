<?php declare(strict_types=1);

namespace Builder\Site\Model;

use Builder\Site\Model\Site as SiteModel;
use Builder\Site\Model\Resource\Domain\Collection as DomainCollection;
use Exception;

/**
 * @class Builder\Site\Model\Media
 */
class Media
{
    /**
     * @var string $sitePath
     */
    private string $sitePath;

    /**
     * @param int $siteId
     * @param string $path
     * @param string $uri
     */
    public function __construct(private SiteModel $siteModel, private string $path, private string $uri)
    {
        $this->sitePath = $this->path . '/site/' . $siteModel->get('id');
        $this->siteUri = '/media/site/' . $siteModel->get('id');
    }

    /**
     * @param string $target
     * @return string
     * @throws Exception
     */
    public function getUrl(string $target): string
    {
        $domainModel = $this->siteModel->getDomainModel();
        return ($domainModel->get('secure') ? 'https' : 'http') . "://{$domainModel->get('domain')}/{$this->siteUri}/" . ltrim($target, '/');
    }

    /**
     * @param string $target
     * @return string
     */
    public function getPath(string $target): string
    {
        return $this->sitePath . '/' . ltrim($target, '/');
    }
}