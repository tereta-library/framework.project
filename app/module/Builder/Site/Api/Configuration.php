<?php declare(strict_types=1);

namespace Builder\Site\Api;

use Framework\Api\Interface\Api;
use Exception;
use Builder\Site\Api\Abstract\Admin;
use Framework\Application\Manager\Http\Parameter\Post as PostParameter;

/**
 * @class Builder\Site\Api\Configuration
 */
class Configuration extends Admin implements Api
{
    /**
     * @return array
     * @throws Exception
     * @api GET /^site\/configuration$/Usi
     */
    public function getConfiguration(): array
    {
        return $this->entityModel->getPublicData();
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
        $data['id'] = $this->entityModel->get('id');
        $data['identifier'] = $this->entityModel->get('identifier');
        $this->entityModel->setData($data);
        $this->entityModel->setFiles($_FILES);
        $this->entityResourceModel->save($this->entityModel);
        $this->entityResourceModel->load($this->entityModel);
        return $this->entityModel->getPublicData();
    }
}