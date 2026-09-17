<?php
// config/database.php

// Load environment variables
require_once __DIR__ . '/Env.php';


define('DB_HOST', Env::get('DB_HOST'));
define('DB_PORT', Env::get('DB_PORT'));
define('DB_NAME', Env::required('DB_NAME')); 
define('DB_USER', Env::get('DB_USER'));
define('DB_PASS', Env::get('DB_PASS'));
define('DB_CHARSET', Env::get('DB_CHARSET'));
define('DB_CONNECT_TIMEOUT', max(1, (int) Env::get('DB_CONNECT_TIMEOUT', 2)));


/**
 * Database singleton connection class.
 * Keeps a single centralized PDO connection for the whole application.
 */
class Database
{
    // Single class instance (Singleton pattern)
    private static $instance = null;
    
    // Active PDO database connection
    private $connection;

    private static function assertDatabaseEndpointReachable(): void
    {
        $target = sprintf('tcp://%s:%s', DB_HOST, DB_PORT);
        $errorCode = 0;
        $errorMessage = '';
        $socket = @stream_socket_client($target, $errorCode, $errorMessage, DB_CONNECT_TIMEOUT, STREAM_CLIENT_CONNECT);

        if (!is_resource($socket)) {
            throw new PDOException($errorMessage !== ''
                ? 'Database endpoint unreachable: ' . $errorMessage
                : 'Database endpoint unreachable: ' . $target);
        }

        stream_set_timeout($socket, DB_CONNECT_TIMEOUT);
        $greeting = fread($socket, 4);
        $meta = stream_get_meta_data($socket);
        fclose($socket);

        if (($meta['timed_out'] ?? false) || $greeting === false || $greeting === '') {
            throw new PDOException('Database endpoint reachable but MySQL handshake timed out');
        }
    }

    private static function createPdoConnection(): PDO
    {
        self::assertDatabaseEndpointReachable();

        $dsn = sprintf(
            "mysql:host=%s;port=%s;dbname=%s;charset=%s",
            DB_HOST,
            DB_PORT,
            DB_NAME,
            DB_CHARSET
        );

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_TIMEOUT => DB_CONNECT_TIMEOUT,
            //PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET
        ];

        return new PDO($dsn, DB_USER, DB_PASS, $options);
    }
    
    /**
     * Private constructor (Singleton pattern).
     * Opens the database connection with the configured settings.
     */
    private function __construct()
    {
        try {
            // Create the PDO instance with the recommended options
            $this->connection = self::createPdoConnection();
        } catch (PDOException $e) {
            // Keep a user-friendly message in production.
            // In development, the full error could be logged instead.
            error_log("Erreur de connexion BDD : " . $e->getMessage());
            die("Erreur de connexion à la base de données. Veuillez réessayer plus tard.");
        }
    }
    
    /**
     * Returns the single Database instance.
     * 
     * @return Database Single database instance
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }
    
    /**
        * Returns the active PDO connection.
        * 
        * @return PDO Active database connection
     */
    public function getConnection()
    {
        return $this->connection;
    }

    public static function connectSafely(): ?PDO
    {
        try {
            return self::createPdoConnection();
        } catch (PDOException $e) {
            error_log('Database connection error: ' . $e->getMessage());
            return null;
        }
    }
}

/**
 * Helper function to quickly retrieve the database connection.
 * 
 * @return PDO Active database connection
 * 
 * Note: kept for compatibility even if it is not directly used in the current project.
 */
function getDB()
{
    return Database::getInstance()->getConnection();
}

function tryGetDB(): ?PDO
{
    return Database::connectSafely();
}
?>