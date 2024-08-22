<?php declare(strict_types=1);

namespace Builder\Site\Helper;

use Framework\Http\Header as HttpHeader;

/**
 * @class Builder\Site\Helper\Header
 */
class Header
{
    /**
     * @return string
     */
    public static function getToken(): string
    {
        preg_match('/^([a-z0-9:]+)$/Usi', (string) HttpHeader::get('api-token'), $matches);
        return $matches[1] ?? '';
    }
}