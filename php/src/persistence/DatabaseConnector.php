<?php declare(strict_types=1);

final class DatabaseConnector
{

    private $dbConnection = null;

    public function __construct()
    {
        $host = getenv('DB_HOST');
        $port = getenv('DB_PORT');
        $db = getenv('DB_DATABASE');
        $user = getenv('DB_USERNAME');
        $pass = getenv('DB_PASSWORD');

        try {
            $this->dbConnection = new PDO("mysql:host=$host;port=$port;charset=utf8mb4;dbname=$db", $user, $pass);
            $this->dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // TODO move this to separate call (only for this sample, I keep it here)
            $this->executeMigrations();
        } catch (PDOException $e) {
            exit($e->getMessage());
        }
    }

    public function getConnection(): PDO
    {
        return $this->dbConnection;
    }

    private function executeMigrations(): void
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `comments` (
                   id INT AUTO_INCREMENT PRIMARY KEY,
                   text TEXT,
                   author VARCHAR(60) NOT NULL,
                   created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                   updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP);';
        $statement = $this->dbConnection->prepare($sql);
        if (!$statement->execute())
        {
            exit('Creation of comments table was not successful.');
        }
    }
}
