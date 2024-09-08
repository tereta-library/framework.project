<?php declare(strict_types=1);

namespace Framework\User\Model\Resource;

use Framework\Database\Abstract\Resource\Model;
use Framework\User\Model\Resource\Token as TokenResourceModel;
use \Framework\User\Model\User as UserModel;
use Exception;

/**
 * @class Framework\User\Model\Resource\User
 */
class User extends Model
{
    /**
     * @throws Exception
     */
    public function __construct()
    {
        parent::__construct('user');
    }

    /**
     * @param UserModel $model
     * @param string $token
     * @param string $ip
     * @return $this
     * @throws Exception
     */
    public function loadByToken(UserModel $model, string $token, string $ip): static
    {
        $tokenResourceModel = new TokenResourceModel;
        $tokenResourceModel->getSelect()->columns(['user.*'])
            ->innerJoin('user', 'user.id = main.userId');
        $tokenResourceModel->where('main.ip = ?', $ip);
        $tokenResourceModel->load($model, $token, 'main.token');
        return $this;
    }
}