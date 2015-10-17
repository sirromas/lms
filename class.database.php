<?php
/*
 * Mysql database class - only one connection alowed
 */
class DB
{

    private static $instance; // store the single instance of the database

    private function __construct()
    {
        $config_data = file_get_contents('db.xml');
        $config = new SimpleXMLElement($config_data);
        foreach ($config as $data_item) {
            
            $this->databaseName = $data_item->db_name;
            $this->host = $data_item->db_host;
            $this->user = $data_item->db_user;
            $this->password = $data_item->db_pwd;
        }
        
        // This will load only once regardless of how many times the class is called
        $connection = mysql_connect($this->host, $this->user, $this->password) or die(mysql_error());
        $db = mysql_select_db($this->databaseName, $connection) or die(mysql_error());
        echo 'DB initiated<br>';
    }
    
    // this function makes sure there's only 1 instance of the Database class
    public static function getInstance()
    {
        if (! self::$instance) {
            self::$instance = new DB();
        }
        return self::$instance;
    }

    public function connect()
    {
        // db connection
    }

    public function query($query)
    {
        // queries
        $sql = mysql_query($query) or die(mysql_error());
        return $sql;
    }

    public function numrows($query)
    {
        // count number of rows
        $sql = $this->query($query);
        return mysql_num_rows($sql);
    }
    
    /*
     * 
     * $db = DB::getInstance();
       query = "SELECT * FROM registrations";
       echo $db->numrows($query);
       $result = $db->query($query);
     * 
     * 
     */
    
    
    
}

?>
