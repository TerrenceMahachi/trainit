<?php
namespace App\Models;
use PDO;

class Database
{
    protected $connection;

    /** One shared connection per request — SQLite works best with a single handle. */
    protected static $shared = null;

    public function __construct($dbPath = null)
    {
        if ($dbPath === null) {
            $this->connection = self::sharedPdo();
            return;
        }

        // Explicit path: open a dedicated connection (used by tools/tests).
        $this->connection = self::open($dbPath);
    }

    /** The application-wide shared PDO handle. */
    public static function sharedPdo(): PDO
    {
        if (self::$shared === null) {
            $dbPath = defined('_DB_PATH') ? _DB_PATH : __DIR__ . '/../../database/app.db';
            self::$shared = self::open($dbPath);
        }
        return self::$shared;
    }

    protected static function open($dbPath): PDO
    {
        $pdo = new PDO("sqlite:$dbPath");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        // WAL allows concurrent readers during a write and plays nicer with
        // php-fpm than the default rollback journal. busy_timeout retries
        // instead of instantly throwing "database is locked".
        $pdo->exec("PRAGMA journal_mode = WAL;");
        $pdo->exec("PRAGMA busy_timeout = 5000;");
        $pdo->exec("PRAGMA foreign_keys = ON;");
        $pdo->exec("PRAGMA cache_size = 10000;");
        $pdo->exec("PRAGMA temp_store = MEMORY;");

        return $pdo;
    }

    public function query($sql, $params = [])
    {
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
    public function escape($value)
    {
        return $this->connection->quote($value);
    }

    public function getPDO(){
        return $this->connection;
    }
}
