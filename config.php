<?php  // Moodle configuration file

unset($CFG);
global $CFG;
$CFG = new stdClass();

$CFG->dbtype    = 'mysqli';
$CFG->dblibrary = 'native';


$CFG->dbhost    = 'db696434591.db.1and1.com';
$CFG->dbname    = 'db696434591';
$CFG->dbuser    = 'dbo696434591';
$CFG->dbpass    = 'aK6SKymc*';


/*
$CFG->dbhost    = 'db598436755.db.1and1.com';
$CFG->dbname    = 'db598436755';
$CFG->dbuser    = 'dbo598436755';
$CFG->dbpass    = 'aK6SKymc';
*/

$CFG->prefix    = 'mdl_';
$CFG->dboptions = array (
  'dbpersist' => 0,
  'dbport' => '',
  'dbsocket' => '',
);

$CFG->wwwroot   = 'http://www.newsfactsandanalysis.com/lms';
$CFG->dataroot  = '/homepages/17/d212585247/htdocs/moodledata';
$CFG->admin     = 'admin';

$CFG->directorypermissions = 0777;

//$CFG->session_handler_class = '\core\session\database';
//$CFG->session_database_acquire_lock_timeout = 120;

require_once(dirname(__FILE__) . '/lib/setup.php');

// There is no php closing tag in this file,
// it is intentional because it prevents trailing whitespace problems!
