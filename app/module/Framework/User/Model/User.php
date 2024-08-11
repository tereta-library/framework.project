<?php declare(strict_types=1);

namespace Framework\User\Model;

use Framework\Database\Abstract\Model;
use Framework\User\Model\Resource\User as UserResourceModel;
use Exception;
use Framework\User\Model\Token as TokenModel;
use Framework\User\Model\Resource\Token as TokenResourceModel;

/**
 * @class Framework\User\Model\User
 */
class User extends Model
{
    private UserResourceModel $resourceModel;

    public function __construct(array $data = [])
    {
        $this->resourceModel = new UserResourceModel();

        parent::__construct($data);
    }

    /**
     * @param string $password
     * @return bool
     * @throws Exception
     */
    public function validatePassword(string $password): bool
    {
        if (!$this->get('id') || !$this->get('password')) {
            throw new Exception('Invalid user model');
        }

        $password = hash('sha256', $password);

        if ($this->get('password') !== $password || !$password) {
            throw new Exception('Invalid password');
        }
        return true;
    }

    /**
     * @param string $identifier
     * @return $this
     * @throws Exception
     */
    public function loadByIdentifier(string $identifier): static
    {
        $this->resourceModel->load($this, $identifier, 'identifier');
        return $this;
    }

    public function createToken(string $ip): TokenModel
    {
        $data = $this->getData();
        unset($data['password']);
        $tokenModel = (new TokenModel)->set(
            'token', hash('sha256', implode(':', $data) . ':' . rand(0, 99999) . ':' . time())
        )->set('userId', $this->get('id'))->set('ip', $ip);

        (new TokenResourceModel)->save($tokenModel);

        return $tokenModel;
    }
}