<?php
/**
 * User Class - Handles all user-related operations (CRUD)
 * Uses OOP principles and PDO for database operations
 */
class User {
    private $conn;
    private $table = "users";

    // User properties
    public $id;
    public $name;
    public $email;
    public $password;
    public $role;
    public $blood_type;
    public $age;
    public $last_donation_date;
    public $photo;

    /**
     * Constructor
     */
    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Create new user (Admin function)
     */
    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                  (name, email, password, role, blood_type, age, last_donation_date, photo) 
                  VALUES (:name, :email, :password, :role, :blood_type, :age, :last_donation_date, :photo)";

        $stmt = $this->conn->prepare($query);

        // ⚠️ WARNING: Storing password without hashing is NOT SECURE!
        // For production, always use: password_hash($this->password, PASSWORD_DEFAULT)
        
        // Bind parameters
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password", $this->password); // Plain text password
        $stmt->bindParam(":role", $this->role);
        $stmt->bindParam(":blood_type", $this->blood_type);
        $stmt->bindParam(":age", $this->age);
        $stmt->bindParam(":last_donation_date", $this->last_donation_date);
        $stmt->bindParam(":photo", $this->photo);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    /**
     * Read all users (Admin function)
     */
    public function readAll() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Read single user by ID
     */
    public function readOne() {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);
        $stmt->execute();
        
        $row = $stmt->fetch();
        if($row) {
            $this->name = $row['name'];
            $this->email = $row['email'];
            $this->role = $row['role'];
            $this->blood_type = $row['blood_type'];
            $this->age = $row['age'];
            $this->last_donation_date = $row['last_donation_date'];
            $this->photo = $row['photo'];
            return true;
        }
        return false;
    }

    /**
     * Update user information
     */
    public function update() {
        $query = "UPDATE " . $this->table . " 
                  SET name = :name, 
                      email = :email, 
                      role = :role, 
                      blood_type = :blood_type, 
                      age = :age, 
                      last_donation_date = :last_donation_date, 
                      photo = :photo 
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":role", $this->role);
        $stmt->bindParam(":blood_type", $this->blood_type);
        $stmt->bindParam(":age", $this->age);
        $stmt->bindParam(":last_donation_date", $this->last_donation_date);
        $stmt->bindParam(":photo", $this->photo);
        $stmt->bindParam(":id", $this->id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    /**
     * Delete user (Admin function)
     */
    public function delete() {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    /**
     * Update password (Plain text - NOT SECURE!)
     */
    public function updatePassword($new_password) {
        $query = "UPDATE " . $this->table . " SET password = :password WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        
        // ⚠️ Storing plain text password (NOT RECOMMENDED!)
        $stmt->bindParam(":password", $new_password);
        $stmt->bindParam(":id", $this->id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    /**
     * Login - Authenticate user (WITHOUT PASSWORD HASHING)
     * ⚠️ WARNING: This method compares passwords as plain text
     * This is NOT SECURE and should only be used for development/testing
     */
    public function login($email, $password) {
        $query = "SELECT * FROM " . $this->table . " WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();

        $row = $stmt->fetch();
        
        // ✅ Changed: Direct password comparison instead of password_verify()
        if($row && $row['password'] === $password) {
            $this->id = $row['id'];
            $this->name = $row['name'];
            $this->email = $row['email'];
            $this->role = $row['role'];
            $this->blood_type = $row['blood_type'];
            $this->age = $row['age'];
            $this->last_donation_date = $row['last_donation_date'];
            $this->photo = $row['photo'];
            return true;
        }
        return false;
    }

    /**
     * Check if email exists (for validation)
     */
    public function emailExists($email) {
        $query = "SELECT id FROM " . $this->table . " WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();

        $row = $stmt->fetch();
        return $row !== false;
    }

    /**
     * Search users by name or email
     */
    public function search($keyword) {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE name LIKE :keyword OR email LIKE :keyword 
                  ORDER BY created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $keyword = "%{$keyword}%";
        $stmt->bindParam(":keyword", $keyword);
        $stmt->execute();
        
        return $stmt;
    }
}
?>