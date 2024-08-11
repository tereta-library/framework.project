<?php declare(strict_types=1);

namespace Framework\Api;

use Exception;
use Framework\Api\Interface\Specification as SpecificationInterface;

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
 * @class Framework\Api\Factory
 * @package Framework\Api
 * @link https://tereta.dev
 * @since 2020-2024
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @author Tereta Alexander <tereta.alexander@gmail.com>
 * @copyright 2020-2024 Tereta Alexander
 */
class Factory
{
    private array $specifications = [
        'json' => 'Framework\Api\Specification\Json'
    ];

    /**
     * @param string $specification
     * @return SpecificationInterface
     * @throws Exception
     */
    public function create(string $specification): SpecificationInterface
    {
        if (!isset($this->specifications[$specification])) {
            throw new Exception("The {$specification} specification not found.");
        }
        $specification = $this->specifications[$specification];

        $model = new $specification;
        if (!$model instanceof SpecificationInterface) {
            throw new Exception('Specification must implement ' . SpecificationInterface::class . ' interface.');
        }

        return $model;
    }
}