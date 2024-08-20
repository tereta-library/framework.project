<?php declare(strict_types=1);

namespace Builder\Site\Model;

use Builder\Site\Model\Entity as EntityModel;
use Builder\Site\Model\Resource\Domain\Collection as DomainCollection;
use Builder\Site\Model\Resource\Entity as EntityResourceModel;
use Exception;
use Framework\Database\Abstract\Repository as AbstractRepository;
use Framework\User\Model\Resource\User as UserResourceModel;

/**
 * @class Builder\Site\Model\Repository
 */
class Repository extends AbstractRepository
{
    /**
     * @var array
     */
    private array $id = [];

    /**
     * @var array
     */
    private array $token = [];

    /**
     * @var EntityResourceModel $entityResourceModel
     */
    private EntityResourceModel $entityResourceModel;

    /**
     * @var UserResourceModel $userResourceModel
     */
    private UserResourceModel $userResourceModel;

    /**
     * @var array $registeredKeys
     */
    protected array $registeredKeys = ['id', ['domain', 'token', 'ip']];

    /**
     * @var EntityResourceModel $entityResourceModel
     */
    private function __construct()
    {
        $this->userResourceModel = new UserResourceModel;
        $this->entityResourceModel = new EntityResourceModel();
    }

    /**
     * @param string $domain
     * @param string $token
     * @param string $ip
     * @return Entity
     * @throws Exception
     */
    public function getByToken(string $domain, string $token, string $ip): Entity
    {
        if ($registeredModel = $this->getRegisterModel([
            'domain' => $domain,
            'token' => $token,
            'ip' => $ip
        ])) {
            return $registeredModel;
        }

        $this->entityResourceModel->loadByToken(
            $entityModel = new EntityModel(),
            $domain,
            $token,
            $ip
        );

        $this->loadDependencies($entityModel);

        return $this->registerModel($entityModel);
    }

    /**
     * @param Entity $entityModel
     * @return void
     * @throws Exception
     */
    private function loadDependencies(EntityModel $entityModel): void
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