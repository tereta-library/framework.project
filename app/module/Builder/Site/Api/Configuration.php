<?php declare(strict_types=1);

namespace Builder\Site\Api;

use Builder\Site\Model\Resource\Site as EntityResourceModel;
use Framework\Api\Interface\Api;
use Exception;
use Framework\Application\Manager\Http\Parameter\Post as PostParameter;
use Builder\Site\Api\Traits\Administrator as AdministratorTrait;

/**
 * @class Builder\Site\Api\Configuration
 */
class Configuration implements Api
{
    use AdministratorTrait;

    /**
     * @var EntityResourceModel $siteResourceModel
     */
    private EntityResourceModel $siteResourceModel;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $this->siteResourceModel = new EntityResourceModel();
    }

    /**
     * @return array
     * @throws Exception
     * @api GET /^site\/configuration$/Usi
     */
    public function getConfiguration(): array
    {
        return $this->siteModel->getPublicData();
    }

    /**
     * @param PostParameter $payload
     * @return array
     * @throws Exception
     * @api POST /^site\/configuration$/Usi
     */
    public function setConfiguration(PostParameter $payload): array
    {
        $data = $payload->getData();
        $data['id'] = $this->siteModel->get('id');
        $data['identifier'] = $this->siteModel->get('identifier');
        $this->siteModel->setData($data);
        $this->siteModel->setFiles($_FILES);
        $this->siteResourceModel->save($this->siteModel);
        $this->siteResourceModel->load($this->siteModel);
        return $this->siteModel->getPublicData();
    }
}