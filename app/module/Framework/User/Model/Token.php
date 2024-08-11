<?php declare(strict_types=1);

namespace Framework\User\Model;

use Framework\Database\Abstract\Model;

/**
 * @class Framework\User\Model\Token
 */
class Token extends Model
{
    /**
     * @param string $ip
     * @return bool
     */
    public function validate(string $ip): bool
    {
        if (!$this->get('token') || !$this->get('userId')) {
            return false;
        }

        if ($this->get('ip') !== $ip) {
            return false;
        }
        return true;
    }
}