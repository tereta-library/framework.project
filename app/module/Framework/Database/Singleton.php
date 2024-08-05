<?php declare(strict_types=1);

namespace Framework\Database;

use PDO;
use Exception;

/**
 * @class Framework\Database\Singleton
 */
class Singleton
{
    /**
     * @var array $connectionInstances
     */
    private static array $connectionInstances = [];

    /**
     * @var array $connectionConfigs
     */
    private static array $connectionConfigs = [];

    /**
     * @param string $host
     * @param string $user
     * @param string $password
     * @param string $name
     * @return void
     */
    public static function createConnection(string $host, string $user, string $password, string $name = 'default'): void
    {
        static::$connectionConfigs[$name] = [
            'host' => $host,
            'user' => $user,
            'password' => $password
        ];
    }

    /**
     * @param string $name
     * @return void
     * @throws Exception
     */
    private static function initConnection(string $name): void
    {
        if (!isset(static::$connectionConfigs[$name])) {
            throw new Exception("Connection {$name} not found");
        }

        $config = static::$connectionConfigs[$name];

        static::$connectionInstances[$name] = new PDO(
            "mysql:dbname={$name};host={$config['host']}",
            $config['user'],
            $config['password']
        );
    }

    /**
     * @param string $name
     * @return PDO
     * @throws Exception
     */
    public static function getConnection(string $name = 'default'): PDO
    {
        if (!isset(static::$connectionInstances[$name])) {
            static::initConnection($name);
        }

        return static::$connectionInstances[$name];
    }
}