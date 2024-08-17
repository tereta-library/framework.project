<?php declare(strict_types=1);

namespace Builder\Site\Model;

use Builder\Site\Helper\Header as HelperHeader;
use Builder\Site\Model\Entity as EntityModel;
use Builder\Site\Model\Resource\Domain\Collection as DomainCollection;
use Builder\Site\Model\Resource\Entity as EntityResourceModel;
use Exception;
use Framework\User\Model\Resource\User as UserResourceModel;

/**
 * @class Builder\Site\Model\Repository
 */
class Repository
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
     * @var Repository|null $instance
     */
    private static ?self $instance = null;

    /**
     * @var EntityResourceModel $entityResourceModel
     */
    private EntityResourceModel $entityResourceModel;

    /**
     * @var UserResourceModel $userResourceModel
     */
    private UserResourceModel $userResourceModel;

    /**
     * @var EntityResourceModel $entityResourceModel
     */
    private function __construct()
    {
        $this->userResourceModel = new UserResourceModel;
        $this->entityResourceModel = new EntityResourceModel();
    }

    /**
     * @return static
     */
    public static function getInstance(): static
    {
        if (static::$instance === null) {
            static::$instance = new static();
        }
        return static::$instance;
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
        if (isset($this->token[$this->getKey($domain, $token, $ip)])) {
            return $this->token[$this->getKey($domain, $token, $ip)];
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

    /**
     * @param Entity $entityModel
     * @return Entity
     * @throws Exception
     */
    private function registerModel(EntityModel $entityModel): EntityModel
    {
        if (!$entityModel->get('id')) {
            throw new Exception('Site not found');
        }

        $this->id[$entityModel->get('id')] = $entityModel;
        $this->token[
            $this->getKey($entityModel->get('domain'), $entityModel->get('token'), $entityModel->get('ip'))
        ] = $entityModel;

        return $entityModel;
    }

    /**
     * @param ...$params
     * @return int
     */
    private function getKey(...$params): int
    {
        return crc32(implode(':', $params));
    }
}