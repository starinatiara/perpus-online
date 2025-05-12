<?php
require_once __DIR__ . '/../config/database.php';
class Borrowing {
    private $db;
    private $conn;

    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->getConnection();
    }

    public function borrowBook($user_id, $book_id) {
        // Cek apakah buku tersedia
        $stmt = $this->conn->prepare("SELECT status FROM books WHERE id = ?");
        $stmt->bind_param("i", $book_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $book = $result->fetch_assoc();
    
        if (!$book || $book['status'] === 'borrowed') {
            return false; // Buku tidak ditemukan atau sudah dipinjam
        }

        $query = "SELECT MAX(id) AS max_id FROM borrowings";
        $result = $this->conn->query($query);
        $row = $result->fetch_assoc();
        $newId = $row['max_id'] + 1;

        // Catat peminjaman
        $borrow_date = date('Y-m-d');
        $return_date = date('Y-m-d', strtotime('+7 days'));
    
        $stmt = $this->conn->prepare("INSERT INTO borrowings (id,user_id, book_id, borrow_date, return_date) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iiiss", $newId ,$user_id, $book_id, $borrow_date, $return_date);

        if ($stmt->execute()) {
            // Update status buku jadi 'borrowed'
            $updateStmt = $this->conn->prepare("UPDATE books SET status = 'borrowed' WHERE id = ?");
            $updateStmt->bind_param("i", $book_id);
            $updateStmt->execute();
            return true;
        }
    
        return false;
    }

    public function returnBook($borrowing_id) {
        // Ambil ID buku dari peminjaman
        $stmt = $this->conn->prepare("SELECT book_id FROM borrowings WHERE id = ?");
        $stmt->bind_param("i", $borrowing_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
    
        if (!$data) return false;
    
        $book_id = $data['book_id'];
    
        // Ubah status peminjaman jadi 'dikembalikan'
        $stmt = $this->conn->prepare("UPDATE borrowings SET status = 'dikembalikan', return_date = NOW() WHERE id = ?");
        $stmt->bind_param("i", $borrowing_id);
    
        if ($stmt->execute()) {
            // Ubah status buku jadi 'available'
            $updateStmt = $this->conn->prepare("UPDATE books SET status = 'available' WHERE id = ?");
            $updateStmt->bind_param("i", $book_id);
            return $updateStmt->execute();
        }
    
        return false;
    }
    
    
    private function calculateLateFee($return_deadline) {
        $current_date = date("Y-m-d");
        if ($current_date > $return_deadline) {
            $late_days = (strtotime($current_date) - strtotime($return_deadline)) / (60 * 60 * 24);
            return 500 * $late_days;
        }
        return 0;
    }

    public function getBorrowingHistory($user_id) {
        try {
            if (!is_numeric($user_id) || $user_id <= 0) {
                throw new Exception("ID pengguna tidak valid.");
            }
    
            $stmt = $this->conn->prepare("
                SELECT br.id, br.book_id, b.title, br.borrow_date, br.return_deadline, br.return_date, br.status 
                FROM borrowings br
                JOIN books b ON br.book_id = b.id
                WHERE br.user_id = ?
            ");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
    
            $history = [];
            while ($row = $result->fetch_assoc()) {
                $history[] = $row;
            }
    
            return [
                'success' => true,
                'history' => $history
            ];
    
        } catch (Exception $e) {
            error_log("Error in getBorrowingHistory(): " . $e->getMessage());
            return [
                'success' => false,
                'message' => "Gagal mendapatkan riwayat peminjaman: " . $e->getMessage()
            ];
        }
    }
}
?>