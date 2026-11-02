<?php

class Database
{
    private $host     = 'localhost';
    private $db_name  = 'proyek_uts';
    private $username = 'root';
    private $password = '';

    private static $shared = null;

    public function connect()
    {
        if (self::$shared !== null) {
            return self::$shared;
        }
        if (!extension_loaded('pdo_mysql')) {
            http_response_code(500);
            exit('Ekstensi PHP <b>pdo_mysql</b> belum aktif. Buka php.ini, hapus tanda ; pada baris '
               . '<code>extension=pdo_mysql</code>, lalu restart Apache.');
        }

        $foundRows = defined('Pdo\\Mysql::ATTR_FOUND_ROWS')
            ? constant('Pdo\\Mysql::ATTR_FOUND_ROWS')
            : constant('PDO::MYSQL_ATTR_FOUND_ROWS');

        try {
            self::$shared = new PDO(
                "mysql:host={$this->host};dbname={$this->db_name};charset=utf8mb4",
                $this->username,
                $this->password,
                [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                    // rowCount() = jumlah baris yang cocok (bukan hanya yang berubah)
                    $foundRows                   => true,
                ]
            );
        } catch (PDOException $e) {
            http_response_code(500);
            exit('Koneksi database gagal: ' . htmlspecialchars($e->getMessage()));
        }
        return self::$shared;
    }
}