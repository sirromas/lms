<?php

/**
 * Description of class
 *
 * @author sirromas
 */
class pdo_db {

    private $databaseName;
    private $host;
    private $user;
    private $password;
    private $db;

    function __construct() {
        $config_data = file_get_contents('/homepages/17/d212585247/htdocs/newsfactsandanalysis/lms/db.xml');
        $config = new SimpleXMLElement($config_data);
        $this->databaseName = $config->db_name;
        $this->host = $config->db_host;
        $this->user = $config->db_user;
        $this->password = $config->db_pwd;
        $dsn = "mysql:dbname=$this->databaseName;host=$this->host";
        try {
            $db = new PDO($dsn, $this->user, $this->password);
            $this->db = $db;
        } catch (PDOException $e) {
            echo 'Connection failed: ' . $e->getMessage();
        }
    }

    public function numrows($query) {
        //echo "Num Query: ".$query."<br/>";
        $result = $this->db->query($query);
        return $result->rowCount();
    }

    public function query($query) {
        //echo "Execute Query: ".$query."<br/>";
        return $this->db->query($query);
    }

}
