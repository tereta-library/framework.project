<?php declare(strict_types=1);

namespace Builder\Site\Model\Resource;

use Builder\Site\Model\Site as EntityModel;
use Framework\Database\Abstract\Resource\Model as ResourceModel;
use Exception;
use Framework\User\Model\Resource\User as UserResourceModel;
use Framework\User\Model\User as UserModel;
use Builder\Site\Model\User as SiteUserModel;
use Builder\Site\Model\Resource\User as SiteUserResourceModel;

/**
 * @class Builder\Site\Model\Resource\Site
 */
class Site extends ResourceModel
{
    const EXCEPTION_CODE_TOKEN = 401;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        parent::__construct('site');
    }

    /**
     * @param EntityModel $entityModel
     * @param string $domain
     * @param string $token
     * @param string $ip
     * @return Entity
     * @throws Exception
     */
    public function loadByToken(EntityModel $entityModel, string $domain, string $token, string $ip): static
    {
        if (!$token) {
            throw new Exception('Empty token', static::EXCEPTION_CODE_TOKEN);
        }

        $userResourceModel = new UserResourceModel;
        $userResourceModel->loadByToken($userModel = new UserModel, $token, $ip);
        $userId = $userModel->get('id');

        if (!$userId) {
            throw new Exception('Token not found', static::EXCEPTION_CODE_TOKEN);
        }

        $this->getSelect()->columns(['site.*'])
            ->innerJoin('siteDomain', 'siteDomain.siteId = site.id');
        $this->where('siteDomain.domain = ?', $domain);
        $this->load($entityModel);
        $siteId = $entityModel->get('id');

        if (!$siteId) {
            throw new Exception('Site not found');
        }

        $siteUserResourceModel = new SiteUserResourceModel;
        $siteUserResourceModel->loadByBound($siteUserModel = new SiteUserModel, $siteId, $userId);
        $aclId = $siteUserModel->get('acl');
        if (!$aclId) {
            $entityModel->setData([]);
            throw new Exception('Site not found');
        }

        $entityModel->setUserModel($userModel);

        return $this;
    }
}