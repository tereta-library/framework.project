<?php declare(strict_types=1);

namespace Builder\Site\Model;

use Framework\Database\Abstract\Model;
use Framework\User\Model\User as UserModel;

/**
 * @class Builder\Site\Model\Entity
 */
class Entity extends Model
{
    /**
     * @var UserModel|null $userModel
     */
    private ?UserModel $userModel = null;

    /**
     * @param UserModel $userModel
     * @return $this
     */
    public function setUserModel(UserModel $userModel): static
    {
        $this->userModel = $userModel;
        return $this;
    }

    /**
     * @return User
     */
    public function getUserModel(): UserModel
    {
        return $this->userModel;
    }
}