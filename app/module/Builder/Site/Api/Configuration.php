<?php declare(strict_types=1);

namespace Builder\Site\Api;
use Framework\Api\Interface\Api;
use Exception;
use Builder\Site\Model\Resource\Entity as EntityResourceModel;
use Builder\Site\Model\Entity as EntityModel;
use Builder\Site\Helper\Header as HelperHeader;
use Builder\Site\Model\Repository as SiteRepository;

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
        $this->entityModel = SiteRepository::getInstance()->getByToken(
            $_SERVER['HTTP_HOST'],
            HelperHeader::getToken(),
            $_SERVER['REMOTE_ADDR']
        );
    }

    /**
     * @return array
     * @throws Exception
     * @api GET site/configuration
     */
    public function getConfiguration(): array
    {
        return $this->entityModel->getPublicData();
    }

    /**
     * @param array $data
     * @return array
     * @throws Exception
     * @api POST site/configuration
     */
    public function setConfiguration(array $data): array
    {
        $data['id'] = $this->entityModel->get('id');
        $data['identifier'] = $this->entityModel->get('identifier');
        $this->entityModel->setData($data);
        $this->entityModel->setFiles($_FILES);
        $this->entityResourceModel->save($this->entityModel);
        $this->entityResourceModel->load($this->entityModel);
        return $this->entityModel->getPublicData();
    }
}