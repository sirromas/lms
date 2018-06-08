<?php  // Moodle configuration file

unset($CFG);
global $CFG;
$CFG = new stdClass();

$CFG->dbtype    = 'mysqli';
$CFG->dblibrary = 'native';
$CFG->dbhost    = 'db740428974.db.1and1.com';
$CFG->dbname    = 'db740428974';
$CFG->dbuser    = 'dbo740428974';
$CFG->dbpass    = 'v#@UEE9a';
$CFG->prefix    = 'mdl_';
$CFG->dboptions = array (
  'dbpersist' => 0,
  'dbport' => '',
  'dbsocket' => '',
);

//$CFG->wwwroot   = 'https://www.newsfactsandanalysis.com/lms';
$CFG->wwwroot   = 'http://www.newsfactsandanalysis.com/lms';
$CFG->dataroot  = '/homepages/17/d212585247/htdocs/moodledata';
$CFG->admin     = 'admin';

$CFG->directorypermissions = 0777;

require_once(dirname(__FILE__) . '/lib/setup.php');

// There is no php closing tag in this file,
// it is intentional because it prevents trailing whitespace problems!
