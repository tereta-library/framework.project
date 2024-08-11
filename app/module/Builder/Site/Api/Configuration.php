<?php declare(strict_types=1);

namespace Builder\Site\Api;
use Framework\Api\Interface\Api;
use Exception;
use Builder\Site\Model\Resource\Entity as EntityResourceModel;
use Builder\Site\Model\Entity as EntityModel;

class Configuration implements Api
{
    /**
     * @param array $data
     * @return array
     * @throws Exception
     * @api site/configuration
     */
    public function getConfiguration(array $data): array
    {
        (new EntityResourceModel())->load($entityModel = new EntityModel(), 1);
        return $entityModel->getData();
    }
}