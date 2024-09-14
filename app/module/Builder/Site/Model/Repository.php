<?php declare(strict_types=1);

namespace Builder\Site\Model;

use Builder\Site\Model\Site as SiteModel;
use Builder\Site\Model\Resource\Domain\Collection as DomainCollection;
use Builder\Site\Model\Resource\Domain as DomainResourceModel;
use Builder\Site\Model\Domain as DomainModel;
use Builder\Site\Model\Resource\Site as EntityResourceModel;
use Exception;
use Framework\Database\Abstract\Repository as AbstractRepository;
use Framework\User\Model\Resource\User as UserResourceModel;
use Builder\Site\Model\Domain\Exception as DomainException;

/**
 * @class Builder\Site\Model\Repository
 */
class Repository extends AbstractRepository
{
    /**
     * @var EntityResourceModel $entityResourceModel
     */
    private EntityResourceModel $entityResourceModel;

    /**
     * @var UserResourceModel $userResourceModel
     */
    private UserResourceModel $userResourceModel;

    /**
     * @var DomainResourceModel $domainResourceModel
     */
    private DomainResourceModel $domainResourceModel;

    /**
     * @var array $registeredKeys
     */
    protected array $registeredKeys = ['id', 'domain', ['domain', 'token', 'ip']];

    /**
     * @var EntityResourceModel $entityResourceModel
     */
    protected function __construct()
    {
        $this->userResourceModel = new UserResourceModel;
        $this->entityResourceModel = new EntityResourceModel();
        $this->domainResourceModel = new DomainResourceModel();
    }

    /**
     * @param string $domain
     * @return SiteModel
     * @throws Exception
     */
    public function getByDomain(string $domain): SiteModel
    {
        if ($registeredModel = $this->getRegisterModel(['domain' => $domain])) {
            return $registeredModel;
        }

        $this->domainResourceModel->load($domainModel = new DomainModel(), $domain, 'domain');
        if (!$domainModel->get('siteId')) {
            throw new DomainException("Site not found for domain {$domain}");
        }
        $this->entityResourceModel->load($entityModel = new SiteModel(), $domainModel->get('siteId'));
        $entityModel->setDomainModel($domainModel);

        return $this->setRegisterModel($entityModel);
    }

    /**
     * @param string $domain
     * @param string $token
     * @param string $ip
     * @return SiteModel
     * @throws Exception
     */
    public function getByToken(string $domain, string $token, string $ip): SiteModel
    {
        if ($registeredModel = $this->getRegisterModel([
            'domain' => $domain,
            'token' => $token,
            'ip' => $ip
        ])) {
            return $registeredModel;
        }

        $this->entityResourceModel->loadByToken(
            $entityModel = new SiteModel(),
            $domain,
            $token,
            $ip
        );

        $this->loadDependencies($entityModel);

        return $this->setRegisterModel($entityModel);
    }

    /**
     * @param string $identifier
     * @return Site
     * @throws Exception
     */
    public function getByIdentifier(string $identifier): SiteModel
    {
        if ($siteModel = $this->getRegisterModel(['identifier' => $identifier])) {
            return $siteModel;
        }

        $this->entityResourceModel->load($siteModel = new SiteModel(), $identifier, 'identifier');

        $this->loadDependencies($siteModel);

        return $this->setRegisterModel($siteModel);
    }

    /**
     * @param SiteModel $entityModel
     * @return void
     * @throws Exception
     */
    private function loadDependencies(SiteModel $entityModel): void
    {
        // Load domain model
        $domainModel = null;
        $domainCollection = (new DomainCollection)->load(
            $entityModel->get('id'),
            'siteId',
        );

        $primaryDomainModel = null;
        foreach ($domainCollection as $domainModelItem) {
            if ($domainModelItem->get('primaryDomain')) {
                $primaryDomainModel = $domainModelItem;
            }

            if ($domainModelItem->isCurrent()) {
                $domainModel = $domainModelItem;
                break;
            }
        }

        if (!$domainModel) {
            $domainModel = $primaryDomainModel;
        }

        if (!$domainModel) {
            throw new Exception("Domain model not found for site {$entityModel->get('id')}");
        }

        $entityModel->setDomainModel($domainModel);
    }
}