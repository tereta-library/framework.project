<?php declare(strict_types=1);

namespace Builder\Site\Model;

use Framework\Database\Abstract\Model;

/**
 * @class Builder\Site\Model\Domain
 */
class Domain extends Model
{
    /**
     * @return bool
     */
    public function isCurrent(): bool
    {
        if (!isset($_SERVER['HTTP_HOST'])) {
            return false;
        }
        return $this->get('domain') === $_SERVER['HTTP_HOST'];
    }

    /**
     * @param string $uri
     * @return string
     */
    public function getUrl(string $uri = ''): string
    {
        $http = $this->get('secure') ? 'https' : 'http';
        return "{$http}://{$this->get('domain')}/{$uri}";
    }
}