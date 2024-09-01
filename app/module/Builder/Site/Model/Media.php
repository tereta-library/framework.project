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
     * @param Site $siteModel
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
        $siteUrl = $this->siteUri;
        if (str_starts_with($siteUrl, '/') === false) {
            $siteUrl = '/' . $siteUrl;
        }
        return ($domainModel->get('secure') ? 'https' : 'http') . "://{$domainModel->get('domain')}{$this->siteUri}/" . ltrim($target, '/');
    }

    /**
     * @param string $target
     * @return string
     * @throws Exception
     */
    public function getPath(string $target): string
    {
        $path = $this->sitePath . '/' . ltrim($target, '/');

        if (str_starts_with(realpath($this->sitePath), $this->sitePath) === false) {
            throw new Exception('Invalid path');
        }

        return $path;
    }
}