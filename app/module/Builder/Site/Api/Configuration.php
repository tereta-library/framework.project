<?php declare(strict_types=1);

namespace Builder\Site\Api;

use Builder\Site\Model\Resource\Site as EntityResourceModel;
use Framework\Api\Interface\Api;
use Exception;
use Framework\Application\Manager as ApplicationManager;
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
     * @var ApplicationManager $applicationManager
     */
    private ApplicationManager $applicationManager;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $this->applicationManager = ApplicationManager::getInstance();
        $this->siteResourceModel = new EntityResourceModel;
    }

    public function construct() {
        $this->constructTrait();

        $this->configurationRepository = ConfigurationRepository::getSiteInstance($this->siteModel->get('id'));
    }

    /**
     * @param string $additionalParams
     * @return array
     * @throws Exception
     * @api GET /^site\/configuration\/get\/(.*)$/Usi
     */
    public function getConfiguration(string $additionalConfigs): array
    {
        $additionalConfigs = $additionalConfigs ? explode(':', $additionalConfigs) : [];

        $additionalConfigValues = [];
        foreach($additionalConfigs as $configPath) {
            $additionalConfigValues[$configPath] = $this->configurationRepository->get($configPath);
        }

        // @todo need to append with values from configuration repository
        $return = $this->siteModel->getPublicData();

        $publicMediaUrl = $this->applicationManager->getConfig()->get('publicMediaUri');
        if (!$return['iconImage']) $return['iconImage'] = "{$publicMediaUrl}/default/upload.png";
        if (!$return['logoImage']) $return['logoImage'] = "{$publicMediaUrl}/default/upload.png";

        $return['additionalConfig'] = $this->addAdditionalConfig($additionalConfigValues);

        return $return;
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

            $additionalConfig = $payload->get('additionalConfig') ?? [];

            foreach ($additionalConfig as $key => &$value) {
                $this->configurationRepository->set($key, $value);
            }

            return array_merge($this->siteModel->getPublicData(), ['additionalConfig' => $this->addAdditionalConfig($additionalConfig)]);
        } catch (Exception $e) {
            $this->siteResourceModel->load($this->siteModel);
            return array_merge($this->siteModel->getPublicData(), ['error' => $e->getMessage()]);
        }
    }

    /**
     * @param array $config
     * @return array
     */
    private function addAdditionalConfig(array $config): array
    {
        $additionalConfig = [];
        $additionalConfigSettings = $this->applicationManager->getConfig()->get('module.additionalConfigurations');

        foreach($config as $configPath => $configValue) {
            if (!isset($additionalConfigSettings[$configPath])) {
                continue;
            }

            $additionalConfig[$configPath] = [
                'value' => $configValue,
                'namespace' => $additionalConfigSettings[$configPath]['namespace'] ?? 'Common',
                'label' => $additionalConfigSettings[$configPath]['label'],
            ];
        }

        return $additionalConfig;
    }
}