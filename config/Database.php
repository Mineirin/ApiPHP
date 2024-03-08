<?php

class Database{
    private $host = 'localhost';
    private $dbname = 'seu_banco';
    private $username = 'nome_banco';
    private $password = 'sua_senha';
    private $connection = null;

    public function connect(){
        try {
            $this->connection = new PDO('mysql:host='.$this->host.';dbname='.$this->dbname,
                $this->username,
                $this->password,
            );
        }
        catch (PDOException $e){
            echo $e->getMessage();
        }

        return $this->connection;
    }
}