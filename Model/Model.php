<?php
class Model
{
    protected $conn;
    protected $table;
    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->connect();
    }
    public function all()
    {
        $stmt = $this->conn->query("SELECT * FROM {$this->table}");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}