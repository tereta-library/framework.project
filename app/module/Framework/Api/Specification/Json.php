<?php declare(strict_types=1);

namespace Framework\Api\Specification;

use Framework\Api\Interface\Specification;

/**
 * ···························WWW.TERETA.DEV······························
 * ·······································································
 * : _____                        _                     _                :
 * :|_   _|   ___   _ __    ___  | |_    __ _        __| |   ___  __   __:
 * :  | |    / _ \ | '__|  / _ \ | __|  / _` |      / _` |  / _ \ \ \ / /:
 * :  | |   |  __/ | |    |  __/ | |_  | (_| |  _  | (_| | |  __/  \ V / :
 * :  |_|    \___| |_|     \___|  \__|  \__,_| (_)  \__,_|  \___|   \_/  :
 * ·······································································
 * ·······································································
 *
 * @class Framework\Api\Builder\Specification\Json
 * @package Framework\Api\Builder\Specification
 * @link https://tereta.dev
 * @author Tereta Alexander <tereta.alexander@gmail.com>
 */
class Json implements Specification
{
    const string SPECIFICATION = 'json';

    /**
     * @param string $payload
     * @return array
     */
    public function decode(string $payload): array
    {
        $return = json_decode($payload, true);
        return $return ? $return : [];
    }

    /**
     * @param mixed $data
     * @return string
     */
    public function encode(mixed $data): string
    {
        return json_encode($data, JSON_PRETTY_PRINT);
    }
}