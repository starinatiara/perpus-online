<?php
class Book {
    private $conn;
    private $table_name = "books";

    public $id;
    public $title;
    public $author;
    public $publisher;
    public $year;
    public $status;
    public $cover_path; 

    public function __construct($db) {
        $this->conn = $db;
        if (!$this->conn instanceof mysqli) {
            throw new Exception("Gagal membuat koneksi database.");
        }
    }
    
    public function getBookById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public static function getAllBooks($conn) { 
        $query = "SELECT * FROM books"; 
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getAvailableBooks() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE status = 'available'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function addBook($title, $author, $status, $coverPath) {
        // Ambil id tertinggi saat ini
        $query = "SELECT MAX(id) AS max_id FROM books";
        $result = $this->conn->query($query);
        $row = $result->fetch_assoc();
        $newId = $row['max_id'] + 1;
    
        // Masukkan data buku baru
        $stmt = $this->conn->prepare("INSERT INTO books (id, title, authors, status, cover_path) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $newId, $title, $author, $status, $coverPath);
    
        return $stmt->execute();
    }
    

    public function updateBook($id, $title, $author, $status) {
        $query = "UPDATE " . $this->table_name . " SET title = ?, authors = ?, status = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("sssi", $title, $author, $status, $id);
        return $stmt->execute(); 
    }

    public function deleteBookById($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
?>