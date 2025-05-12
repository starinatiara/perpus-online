<?php
class Database {
    // private $host = "localhost";
    // private $db_name = "db_perpustakaan";
    // private $username = "root";
    // private $password = "";
    // public $conn;
    // private $host = "mysql://root:jcWxmmkoDCRkKfGFotTKwapoNbdsukCX@caboose.proxy.rlwy.net:20404/railway";
    // private $db_name = "railway";
    // private $username = "root";
    // private $password = "jcWxmmkoDCRkKfGFotTKwapoNbdsukCX";
    // public $conn;

    // public function getConnection() {
    //     $this->conn = new mysqli($this->host, $this->username, $this->password, $this->db_name);
    //     if ($this->conn->connect_error) {
    //         die("Koneksi gagal: " . $this->conn->connect_error);
    //     }
    //     return $this->conn;
    // }

    // public function closeConnection() {
    //     if ($this->conn) {
    //         $this->conn->close();
    //     }
    // }


    private $host = "caboose.proxy.rlwy.net";
    private $port = 20404;
    private $db_name = "railway";
    private $username = "root";
    private $password = "jcWxmmkoDCRkKfGFotTKwapoNbdsukCX";
    public $conn;

    public function getConnection() {
        // Gunakan host:port saat menyambung
        $this->conn = new mysqli($this->host, $this->username, $this->password, $this->db_name, $this->port);
        
        if ($this->conn->connect_error) {
            die("Koneksi gagal: " . $this->conn->connect_error);
        }

        return $this->conn;
    }

    public function closeConnection() {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}
?>