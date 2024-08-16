<?php declare(strict_types=1);

namespace Builder\Site\Model;

use Framework\Database\Abstract\Model;

/**
 * @class Builder\Site\Model\Domain
 */
class Domain extends Model
{
    public function isCurrent(): bool
    {
        return $this->get('domain') === $_SERVER['HTTP_HOST'];
    }
}