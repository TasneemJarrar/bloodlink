<?php
/**
 * Database Connection Class
 * Uses PDO for secure database operations
 */
class Database {
    public $host = "localhost";
    public $db_name = "bloodlink";
    public $username = "root";
    public $password = "";
    public $conn;

    /**
     * Get database connection
     * @return PDO|null
     */
    public function getConnection() {
        $this->conn = null;

        try {
          $this->conn = new PDO("mysql:host=" . $this->host . "; dbname=" . $this->db_name, $this->username, $this->password);
          // set the PDO error mode to exception
          $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
          echo "Connected successfully";
        } catch (PDOException $e){
          echo "Connection failed: " . $e->getMessage();
        }

        return $this->conn;
    }
}
?>