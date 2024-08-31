<?php declare(strict_types=1);

namespace Builder\Social\Api;

use Builder\Site\Api\Traits\Administrator as AdministratorTrait;
use Framework\Api\Interface\Api;
use Exception;
use Builder\Social\Model\Resource\Social as SocialResourceModel;
use Builder\Social\Model\Social as SocialModel;
use Framework\Application\Manager\Http\Parameter\Payload as PayloadParameter;

/**
 * @class Builder\Social\Api\Configuration
 */
class Configuration implements Api
{
    use AdministratorTrait;

    /**
     * @return null[]
     * @throws Exception
     * @api GET /^social\/configuration$/Usi
     */
    public function getConfiguration(): array
    {
        $siteId = $this->siteModel->get('id');

        SocialResourceModel::getInstance()->load($socialModel = new SocialModel, $siteId);

        return [
            'facebook' => $socialModel->get('facebook'),
            'instagram' => $socialModel->get('instagram'),
            'pinterest' => $socialModel->get('pinterest'),
            'linkedin' => $socialModel->get('linkedin'),
            'youtube' => $socialModel->get('youtube')
        ];
    }

    /**
     * @return null[]
     * @throws Exception
     * @api POST /^social\/configuration/Usi
     */
    public function saveConfiguration(PayloadParameter $payload): array
    {
        $siteId = $this->siteModel->get('id');

        $socialResourceModel = SocialResourceModel::getInstance();

        $socialResourceModel->load($socialModel = new SocialModel, $siteId);

        $socialModel->set('siteId', $siteId);
        $socialModel->set('facebook', $payload->get('facebook'));
        $socialModel->set('instagram', $payload->get('instagram'));
        $socialModel->set('pinterest', $payload->get('pinterest'));
        $socialModel->set('linkedin', $payload->get('linkedin'));
        $socialModel->set('youtube', $payload->get('youtube'));

        $socialResourceModel->save($socialModel);

        return $socialModel->getData();
    }
}