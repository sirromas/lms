<?php
/*
 * Mysql database class - only one connection alowed
 */
class DB
{

    private static $instance; // store the single instance of the database

    private $databaseName;

    private $host;

    private $user;

    private $password;

    private function __construct()
    {
        $config_data = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/lms/moodle/db.xml');
        $config = new SimpleXMLElement($config_data);
        
        $this->databaseName = $config->db_name;
        $this->host = $config->db_host;
        $this->user = $config->db_user;
        $this->password = $config->db_pwd;
        
        // This will load only once regardless of how many times the class is called
        $connection = mysql_connect($this->host, $this->user, $this->password) or die(mysql_error());
        $db = mysql_select_db($this->databaseName, $connection) or die(mysql_error());
        $this->db = $db;
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
        return $this->db;
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
     * query = "SELECT * FROM registrations";
     * echo $db->numrows($query);
     * $result = $db->query($query);
     *
     *
     */
}

?>
