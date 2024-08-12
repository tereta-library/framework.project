<?php declare(strict_types=1);

namespace Builder\Site\Helper;

use Framework\Http\Header as HttpHeader;

/**
 * @class Builder\Site\Helper\Header
 */
class Header
{
    /**
     * @return string|null
     */
    public static function getToken(): ?string
    {
        preg_match('/^Bearer (\w+)$/Usi', HttpHeader::get('authorization'), $matches);
        return $matches[1] ?? null;
    }
}