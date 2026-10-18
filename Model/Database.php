<?php

class Database
{
    private $host     = 'localhost';
    private $db_name  = 'proyek_uts';
    private $username = 'root';
    private $password = '';

    private $conn;
    public function connect()
    {
        $this->conn = null;
        try {
            $this->conn = new PDO(
                "mysql:host={$this->host};dbname={$this->db_name};charset=utf8mb4",
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo 'Koneksi database gagal: ' . $e->getMessage();
        }
        return $this->conn;
    }
}