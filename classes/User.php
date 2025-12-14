<?php
class User {
    private $conn;
    private $table = "users";

    public $id;
    public $name;
    public $email;
    public $password;
    public $role;
    public $blood_type;
    public $age;
    public $last_donation_date;
    public $photo;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                  (name, email, password, role, blood_type, age, last_donation_date, photo) 
                  VALUES (:name, :email, :password, :role, :blood_type, :age, :last_donation_date, :photo)";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password", $this->password);
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

    public function readAll() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

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

    public function delete() {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function updatePassword($new_password) {
        $query = "UPDATE " . $this->table . " SET password = :password WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":password", $new_password);
        $stmt->bindParam(":id", $this->id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function login($email, $password) {
        $query = "SELECT * FROM " . $this->table . " WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();

        $row = $stmt->fetch();        
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

    public function emailExists($email) {
        $query = "SELECT id FROM " . $this->table . " WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();

        $row = $stmt->fetch();
        return $row !== false;
    }

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