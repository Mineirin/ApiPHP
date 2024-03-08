<?php

class Database{
    private $host = 'dbethos.mysql.dbaas.com.br';
    private $dbname = 'dbethos';
    private $username = 'dbethos';
    private $password = 'Ethos2023@';
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