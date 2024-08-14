<?php declare(strict_types=1);

namespace Builder\Panel\Api;

use Framework\Api\Interface\Api;
use Framework\User\Model\User as UserModel;
use Exception;

class Login implements Api
{
    /**
     * @param array $data
     * @return array
     * @throws Exception
     * @api POST user/login
     */
    public function getToken(array $data): array
    {
        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;
        if (!$email || !$password) {
            throw new Exception('Email and password are required');
        }

        try {
            $userModel = (new UserModel())->loadByIdentifier($email);
            $userModel->validatePassword($password);
            $tokenModel = $userModel->createToken($_SERVER['REMOTE_ADDR'] ?? null);

            return ['token' => $tokenModel->get('token')];
        } catch (Exception $e) {
            throw new Exception('Invalid email or password');
        }
    }
}