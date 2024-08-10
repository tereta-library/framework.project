<?php declare(strict_types=1);

namespace Framework\Database;

use PDO;

/**
 * @class Framework\Database\Facade
 */
class Facade
{
    /**
     * @param PDO $connection
     * @param string $table
     * @return array
     */
    public static function describeTable(PDO $connection, string $table): array
    {
        $smt = $connection->prepare("DESCRIBE {$table}");
        $smt->execute();

        return $smt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @return string
     */
    public static function dropTable(): string
    {
        return 'DROP TABLE ?';
    }

    /**
     * @param PDO $connection
     * @param string|null $like
     * @return array
     */
    public static function showTables(PDO $connection, ?string $like = null): array
    {
        if ($like) {
            $smt = $connection->prepare('SHOW TABLES LIKE :like');
            $smt->bindParam(':like', $like);
        } else {
            $smt = $connection->prepare('SHOW TABLES');
        }
        $smt->execute();

        return $smt->fetchAll(PDO::FETCH_COLUMN);
    }
}