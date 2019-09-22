<?php

class Database
{
    public $isConnect;
    protected $datab;

    // Connect to DB
    public function __construct($username = "root", $password = "", $host = "localhost", $dbname = "DBNAME", $options = [])
    {
        $this->isConnect = TRUE;
        try
        {
            $this->datab = new PDO("mysql:host={$host};dbname={$dbname}", $username, $password, $options);
            $this->datab->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->datab->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        }
        catch (PDOException $e)
        {
            throw new Exception($e->getMessage());
        }
    }

    // Disconnect from DB
    public function Disconnect()
    {
        $this->datab = NULL;
        $this->isConnect = FALSE;
    }
    // Get Row
    public function getRow($query, $params = [])
    {
        try
        {
            $stmt = $this->datab->prepare($query);
            $stmt->execute($params);
            return $stmt->fetch();
        }
        catch (PDOException $e)
        {
            throw new Exception($e->getMessage());
        }
    }
    // Get Rows
    public function getRows($query, $params = [])
    {
        try
        {
            $stmt = $this->datab->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll();
        }
        catch (PDOException $e)
        {
            throw new Exception($e->getMessage());
        }
    }
    // Insert Row
    public function insertRow($query, $params = [])
    {
        try
        {
            $stmt = $this->datab->prepare($query);
            $stmt->execute($params);
            return TRUE;
        }
        catch (PDOException $e)
        {
            throw new Exception($e->getMessage());
        }
    }
    // Update Row
    public function updateRow($query, $params = [])
    {
        $this->insertRow($query, $params);
    }
    // Delete Row
    public function deleteRow($query, $params = [])
    {
        $this->insertRow($query, $params);
    }
}


?>