<?php
class Database
{
    private string $host = 'localhost';
    private string $user = 'root';
    private string $db_name = 'lawga_blog';
    private string $password = '';
    private $conn;

    public function connect(): PDO
    {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                'mysql:host=' . $this->host . ';dbname=' . $this->db_name,
                $this->user,
                $this->password
            );
        } catch (PDOException $e) {
            echo 'Connection error ' . $e->getMessage();
        }

        return $this->conn;
    }
}
