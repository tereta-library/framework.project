<?php declare(strict_types=1);

namespace Builder\Site\Model\Resource;

use Builder\Site\Model\User as SiteUserModel;
use Framework\Database\Abstract\Resource\Model;
use Exception;

/**
 * @class Builder\Site\Model\Resource\User
 */
class User extends Model
{
    /**
     * @throws Exception
     */
    public function __construct()
    {
        parent::__construct('siteUser');
    }

    /**
     * @param SiteUserModel $model
     * @param int $siteId
     * @param int $userId
     * @return $this
     * @throws Exception
     */
    public function loadByBound(SiteUserModel $model, int $siteId, int $userId): static
    {
        $this->getSelect()->columns('main.*');
        $this->where('main.siteId = ?', $siteId);
        $this->where('main.userId = ?', $userId);
        $this->load($model);
        return $this;
    }
}
