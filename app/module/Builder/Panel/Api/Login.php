<?php declare(strict_types=1);

namespace Builder\Panel\Api;

use Framework\Api\Interface\Api;
use Framework\User\Model\User as UserModel;
use Exception;
use Framework\Application\Manager\Http\Parameter\Payload as PayloadParameter;

class Login implements Api
{
    /**
     * @param PayloadParameter $payload
     * @return array
     * @throws Exception
     * @api POST /^user\/login$/Usi
     */
    public function getToken(PayloadParameter $payload): array
    {
        $data = $payload->getData();
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