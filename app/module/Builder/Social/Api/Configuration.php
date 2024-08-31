<?php declare(strict_types=1);

namespace Builder\Social\Api;

use Builder\Site\Api\Traits\Administrator as AdministratorTrait;
use Framework\Api\Interface\Api;
use Exception;
use Builder\Social\Model\Resource\Social as SocialResourceModel;
use Builder\Social\Model\Social as SocialModel;

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
    public function saveConfiguration(): array
    {
        return [];
    }
}