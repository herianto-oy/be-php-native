<?php
class Database {
    private $host = 'localhost';
    private $port = '3306';
    private $dbName = 'be';
    private $dbUsername = 'root';
    private $dbPassword = '';
    private $conn = null;

    public function connect(){
        if (!$this->conn){
            try {
                $this->conn = new PDO('mysql:host='.$this->host.';dbname='. $this->dbName.';port='. $this->port, $this->dbUsername, $this->dbPassword);
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                // echo $e->getMessage();
            }
        }

        return $this->conn;
    }

}