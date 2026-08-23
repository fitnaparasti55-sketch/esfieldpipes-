<?php
/**
 * Database Connection & Initialization Singleton
 * Esfield Pipe Platform
 */

if (!defined('DB_HOST')) {
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root');
    define('DB_PASS', '');
    define('DB_NAME', 'esfield_pipe');
    define('DB_PORT', 3306);
}

class Database {
    private static ?PDO $instance = null;

    public static function getConnection(): PDO {
        if (self::$instance === null) {
            try {
                $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ];
                self::$instance = new PDO($dsn, DB_USER, DB_PASS, $options);
            } catch (PDOException $e) {
                // If database doesn't exist yet, try to create it
                if ($e->getCode() == 1049) {
                    try {
                        $tempPdo = new PDO("mysql:host=" . DB_HOST . ";port=" . DB_PORT, DB_USER, DB_PASS);
                        $tempPdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                        
                        // Now connect to newly created database
                        $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
                        self::$instance = new PDO($dsn, DB_USER, DB_PASS, [
                            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                            PDO::ATTR_EMULATE_PREPARES => false,
                        ]);

                        // Auto-run schema if available
                        $schemaFile = __DIR__ . '/schema.sql';
                        if (file_exists($schemaFile)) {
                            $sql = file_get_contents($schemaFile);
                            self::$instance->exec($sql);
                        }
                    } catch (PDOException $ex) {
                        die("<div style='font-family:sans-serif;padding:2rem;color:#b91c1c;background:#fef2f2;border:1px solid #f87171;border-radius:8px;'>
                            <h3>Database Connection Failed</h3>
                            <p>" . htmlspecialchars($ex->getMessage()) . "</p>
                        </div>");
                    }
                } else {
                    die("<div style='font-family:sans-serif;padding:2rem;color:#b91c1c;background:#fef2f2;border:1px solid #f87171;border-radius:8px;'>
                        <h3>Database Connection Error</h3>
                        <p>" . htmlspecialchars($e->getMessage()) . "</p>
                    </div>");
                }
            }
        }
        return self::$instance;
    }
}

/**
 * Global helper function to get PDO instance
 */
function get_db(): PDO {
    return Database::getConnection();
}
