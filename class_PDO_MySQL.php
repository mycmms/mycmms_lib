<?php
/** 
* PDO connection to MySQL
* 
* @author  Werner Huysmans <werner.huysmans@skynet.be>
* @changed:   2012-05
* @access  public
* @version: V4.1
* @package mycmms40
* @subpackage library
* @filesource
*/
/**
* Class DBC extends PDO
*/
class DBC extends PDO {
    private static $dbh;
    public static $host;
    public static $database;
    public static $dsn;
    public static $uid;
    public static $pwd;
    public static $driverOpts=array(
        PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'",
        PDO::ATTR_EMULATE_PREPARES => true,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION);
    private static $counter;
    public $sql;
    public $array;

    // No cloning or instantiating allowed
    public function __construct() {
        $this->get();
    }
/** get
* The get function checks whether a connection is available, if not it creates one.
* 
* @returns PDO PDO-database handle                  
*/
    public static function get() {
        // Connect if not already connected
        if (is_null(self::$dbh)) {
            self::$counter++;
            self::setDatabase();
            try {
                self::$dbh=new PDO(self::$dsn,self::$uid,self::$pwd,self::$driverOpts);
                // self::$dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES,TRUE);
                // self::$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                // self::$dbh->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''))
            } catch (PDOException $e) {
                echo 'Connection failed: '.$e->getMessage();
            }
        }
        // Return the connection
        return self::$dbh;
    } // End get
/**
* Execute INSERT, UPDATE, ... query
*     
* @param mixed $sql     SELECT statement with placeholders
* @param mixed $array   array with data for placeholders
* @return bool
*/
    public function execute($sql,$array) {
        $st=self::$dbh->prepare($sql);
        return $st->execute($array);
    } // End execute
/**
* Execute SELECT statement for 1 element
*     
* @param mixed $sql
* @param mixed $column
* @return string
*/
    public function fetchcolumn($sql,$column) {
        $res=self::$dbh->query($sql);
        $value=$res->fetchColumn($column);
        return $value;
    }
/**
* Execute SELECT statement for more elements
*     
* @param mixed $sql
* @return mixed
*/
    public function fetchcolumns($sql) {
        $res=self::$dbh->query($sql);
        $values=$res->fetch(PDO::FETCH_NUM);
        return $values;
    }
/** numrows
* Returns the number of records
*     
* @param mixed $sql
* @return string
*/
    public function numrows($sql) {
        $st=self::$dbh->query($sql);
        $all=$st->fetchAll(PDO::FETCH_COLUMN,1);
        return count($all);
    } // End numrows
/** PDOError
* Puts a bow with a database warning on the screen.
*     
* @param mixed $error
*/
    public function setDatabase() {
        $ini_array = parse_ini_file("databases.inc", true);
        self::$host=$ini_array[CMMS_DB]["host"];
        self::$database=$ini_array[CMMS_DB]["DB"];
        self::$uid=$ini_array[CMMS_DB]["uid"];
        self::$pwd=$ini_array[CMMS_DB]["pwd"];
        self::$dsn="mysql:host=".self::$host.";dbname=".self::$database; 
    }
    public function getDatabase() {
        return self::$host."::".self::$database." (reconnects: ".self::$counter.")";
    }
}
?>
