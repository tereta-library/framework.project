<?php declare(strict_types=1);

namespace Builder\Site\Api;
use Framework\Api\Interface\Api;
use Exception;
use Builder\Site\Model\Resource\Entity as EntityResourceModel;
use Builder\Site\Model\Entity as EntityModel;
use Builder\Site\Helper\Header as HelperHeader;

class Configuration implements Api
{
    /**
     * @var EntityModel
     */
    private EntityModel $entityModel;

    /**
     * @var EntityResourceModel
     */
    private EntityResourceModel $entityResourceModel;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        ($this->entityResourceModel = new EntityResourceModel())->loadByToken(
            $this->entityModel = new EntityModel(),
            $_SERVER['HTTP_HOST'],
            HelperHeader::getToken(),
            $_SERVER['REMOTE_ADDR']
        );
    }

    /**
     * @param array $data
     * @return array
     * @throws Exception
     * @api GET site/configuration
     */
    public function getConfiguration(array $data): array
    {
        return $this->entityModel->getData();
    }

    /**
     * @param array $data
     * @return array
     * @api POST site/configuration
     */
    public function setConfiguration(array $data): array
    {
        $data['id'] = $this->entityModel->get('id');
        $data['identifier'] = $this->entityModel->get('identifier');
        $this->entityModel->setData($data);
        $this->entityResourceModel->save($this->entityModel);
        return $this->entityModel->getData();
    }
}