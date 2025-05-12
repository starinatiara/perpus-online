<?php
class Users {
    private $conn;
    private $table_name = "users";

    public function __construct($conn) {
        $this->conn = $conn;
        if (!$this->conn instanceof mysqli) {
            die("Gagal membuat koneksi database.");
        }
    }

    public function register($username, $password, $role = 'user', $id) {
        if ($this->userExists($username)) {
            return false;
        }

        $query = "INSERT INTO " . $this->table_name . " (username, password, role, id) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("sssi", $username, $password, $role , $id);
    
        return $stmt->execute(); 
    }

    public function userExists($username) {
        $query = "SELECT id FROM " . $this->table_name . " WHERE username=? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }

    public function getUserByUsername($username) {
        $query = "SELECT id, username, password, role FROM " . $this->table_name . " WHERE username=? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function getUserIdIncrement() {
        $query = "SELECT max(id) + 1 as id FROM users ";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    
}
?>