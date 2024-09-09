<?php declare(strict_types=1);

namespace Builder\Site\Api;

use Builder\Site\Model\Resource\Site as EntityResourceModel;
use Framework\Api\Interface\Api;
use Exception;
use Framework\Application\Manager\Http\Parameter\Post as PostParameter;
use Builder\Site\Api\Traits\Administrator as AdministratorTrait;
use Builder\Site\Model\Site\Configuration\Repository as ConfigurationRepository;

/**
 * @class Builder\Site\Api\Configuration
 */
class Configuration implements Api
{
    use AdministratorTrait { construct as private constructTrait; }

    /**
     * @var EntityResourceModel $siteResourceModel
     */
    private EntityResourceModel $siteResourceModel;

    /**
     * @var ConfigurationRepository $configurationRepository
     */
    private ConfigurationRepository $configurationRepository;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $this->siteResourceModel = new EntityResourceModel;
    }

    public function construct() {
        $this->constructTrait();

        $this->configurationRepository = ConfigurationRepository::getSiteInstance($this->siteModel->get('id'));
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
        try {
            $this->siteModel->setData($data);
            $this->siteModel->setFiles($_FILES);
            $this->siteResourceModel->save($this->siteModel);
            $this->siteResourceModel->load($this->siteModel);

            foreach ($payload->extendedVariables as $key => $value) {
                $this->configurationRepository->set($key, $value);
            }

            return $this->siteModel->getPublicData();
        } catch (Exception $e) {
            $this->siteResourceModel->load($this->siteModel);
            return array_merge($this->siteModel->getPublicData(), ['error' => $e->getMessage()]);
        }
    }
}