<?php
/**
 * Database Connection Class
 * Uses PDO for secure database operations
 */
class Database {
    private $host = "localhost";
    private $db_name = "bloodlink_db";
    private $username = "root";
    private $password = "";
    public $conn;

    /**
     * Get database connection
     * @return PDO|null
     */
    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            
            // Set the PDO error mode to exception
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Set charset to UTF8 (important for Arabic text)
            $this->conn->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, "SET NAMES utf8");
            
            // ✅ Remove echo - connection successful
            
        } catch (PDOException $e) {
            // ✅ Log error securely instead of displaying it
            error_log("Database Connection Error: " . $e->getMessage());
            
            // ✅ Show generic message to users
            die("Database connection error. Please contact system administrator.");
        }

        return $this->conn;
    }
}
?>