<?php namespace Betasyntax;

use Betasyntax\Db\DbFactory;
use Betasyntax\Database;
use Exception;
use StdClass;
use Betasyntax\Logger\Logger;

class BaseModel  
{
  /* Configuration */
  /**
   * Configuration storage
   * @var array
   */
  protected static $config = array(
    'driver' => 'mysql',
    'host'   => 'localhost',
    'port'   => 3307,
    'fetch'  => 'stdClass'
  );

  public static $belongs_to;
  public static $has_one;
  public static $has_many;
  public static $has_many_through;
  public static $has_one_through;
  public static $has_and_belongs_to_many;
  public static $select_as;

  protected static $last_insert_id;

  // protected static $tetimg;
  protected static $c;
  protected static $d;
  protected static $properties = array();

  /* Static instances */
  /**
   * Multiton instances
   * @var array
   */
  protected static $instance  = array();

  protected static $arguments = array( 'driver', 'host', 'database', 'user', 'password' );

  /* Constructor */
  /**
   * Database connection
   * @var PDO
   */
  protected static $db;

  /**
   * Latest query statement
   * @var PDOStatement
   */
  protected static $result;

  /**
   * Database information
   * @var stdClass
   */
  protected static $info;

  /**
   * Statements cache
   * @var array
   */
  protected static $table_name = '';
  protected static $statement = array();
  protected static $columns = array();
  /**
   * Tables shema information cache
   * @var array
   */
  protected static $table = array();

  protected static $id;

  /**
   * Primary keys information cache
   * @var array
   */
  protected static $key = array();

  /**
   * Constructor
   * @uses  PDO
   * @throw PDOException
   * @param string $driver   Database driver
   * @param string $host     Database host
   * @param string $database Database name
   * @param string $user     User name
   * @param string $pass     [Optional] User password
   * @see   http://php.net/manual/fr/pdo.construct.php
   * @todo  Support port/socket within DSN?
   */
  public function __construct ($config = false) 
  {
    $app = app();
    if (!$config){
      $dbtype = config('default_db');
      $dbconfig = config($dbtype);
      $config = new Database;
      $config->driver = $dbconfig['driver'];
      $config->host = $dbconfig['host'];
      $config->user = $dbconfig['user'];
      $config->password = $dbconfig['pass'];
      $config->dbscheme = $dbconfig['schema'];
    }
    self::$db = DbFactory::connect($config);
    if ( ! self::$db) {
      flash()->error('Error connecting to database. Please check your settings');
    }
    restore_exception_handler();
    self::$info = (object) array(self::$arguments);
    unset(self::$info->password);
  }

  /**
   * Get singleton instance
   * @uses   static::config
   * @uses   static::__construct
   * @param string $driver   [Optional] Database driver
   * @param string $host     [Optional] Database host
   * @param string $database [Optional] Database name
   * @param string $user     [Optional] User name
   * @param string $pass     [Optional] User password
   * @return Db Singleton instance
   */

  static public function __callStatic ($name, $config) 
  {
    if (isset(static::$instance[$name])) {
      return static::$instance[$name];
    }
    $config = array_merge(
      static::config(),
      array_filter( 
        array_combine( 
          static::$arguments, 
          $config + array_fill( 
            0, 
            count(static::$arguments), 
            null
          )
        )
      )
    );
    return static::$instance[$name] = new static();
  }

  public function __get($key) { 
    self::instance();
    return self::$properties[$key];
  }
  
  public function __set($key, $value) { 
    self::instance();
    return self::$properties[$key] = $value;
  }

  /**
   * Get and set default Db configurations
   * @uses   static::config
   * @param  string|array $key   [Optional] Name of configuration or hash array of configurations names / values
   * @param  mixed        $value [Optional] Value of the configuration
   * @return mixed        Configuration value(s), get all configurations when called without arguments
   */
  static public function config ($key = null, $value = null) 
  {
    if (!isset($key)) {
      return static::$config;
    }
    if (isset($value)) {
      return static::$config[(string) $key] = $value;
    }
    if (is_array($key)) {
      return array_map('static::config', array_keys((array) $key), array_values((array) $key));
    }
    if (isset(static::$config[$key])) {
      return static::$config[$key];
    }
  }

  /**
   * Avoid exposing exception informations
   * @param Exception $exception [Optional] User password
   */
  public static function safe_exception (Exception $exception) 
  {
    die('Uncaught exception: '.$exception->getMessage());
  }

  /* SQL query */
  /**
   * Get latest SQL query
   * @return string Latest SQL query
   */
  public function __toString () 
  {
    return self::$result ? self::$result->queryString : null;
  }  


  public static function table_name() 
  {
    $class = get_called_class();
    $vowels = array('a','e','i','o');
    if ($class[strlen($class)-1]=='y' ) {
      $class = str_replace('y', 'ies', $class);
    } else {
      $class =  $class.'s';
    }
    $class = preg_replace('/\B([A-Z])/', '_$1', $class);
    $class = explode('\\', strtolower($class));
    return end($class);
  }

  /* Query methods */
  /*** Execute raw SQL query
   * @uses   PDO::query
   * @throw  PDOException
   * @param  string $sql Plain SQL query
   * @return Db     Self instance
   * @todo   ? detect USE query to update dbname ?
   */
  public static function raw($sql) 
  {
    self::instance();
    return self::_getResult($sql);
  }
  //same as above?!
  // public static function query($sql) 
  // {
  //   self::instance();
  //   return self::_getResult($sql);
  // }

  public static function exec($sql) 
  {
    self::instance();
    self::$result = self::$db->query($sql);
    return self::$result;
  }

  public static function all($extra_unsafe_sql = false) 
  { 
    self::instance();
    $sql = "SELECT * FROM ". self::table_name();
    if ($extra_unsafe_sql) { 
      $sql .= " ".$extra;
    }
    $sql .= ";";
    return self::_getResult($sql);
  }

  public static function delete($id) {
    self::instance();
    $sql = 'DELETE FROM ' . self::table_name() . ' WHERE id = ' . $id;
    $q = self::$db->execute($sql);
    if ($q) {
      return true;
    } else {
      return false;
    }
  }

  public static function find_by($args,$limit='',$join_type='',$foreign_table='') 
  { 
    self::instance();
    //loop through find by and get the where statments
    if (isset($args)) {
      $sql_where = '';
      $quote = '';
      $total_args = count($args);
      $cnt = 1;
      foreach ($args as $key => $value) {
        if (is_string($value))
          $quote = '"';
        $and = '';
        if ($cnt < $total_args)
          $and = 'AND ';
        $cnt++;
        $sql_where .= self::table_name().'.'.$key.' = '.$quote.$value.$quote.' '.$and;
      }
    }
    $sql = self::_getSql($join_type,$foreign_table,$sql_where,$limit);    
    // echo $sql;
    return self::_getResult($sql);
  }

  public static function find($id,$join_type='',$foreign_table='') 
  { 
    self::instance();
    $sql = self::_getSql($join_type,$foreign_table,$id);
    return self::_getResult($sql);
  }

  private static function _getResult($sql)
  {
    self::$result = self::$db->fetch($sql);
    self::$c = self::$result;
    if (count(self::$result)==1) {
      return (object) self::$result[0];
    } else {
      return self::$result;
    }
  }

  private static function _getSql($join_type='',$foreign_table='',$where='',$limit='')
  {
    $join_sql = '';
    $select = '';
    $has_one_where = '';
    if ($limit !='') {
      $limit = ' LIMIT '.$limit.';';
    }
    if ($join_type!='') {
      if ($limit =='') {
        $limit = ' LIMIT 1';
      }
      if (($join_type=='has_many') || (is_array($where))) {
        $limit = '';
      }
      if (in_array($join_type,['has_one','belongs_to','has_many'])) {
        $select .= ','.$foreign_table.'.id as '.$foreign_table.'_id';
        switch ($join_type) {
          case 'has_one':
            $join_sql .= ' LEFT OUTER JOIN '.$foreign_table.' ON '.self::table_name().'.id='.$foreign_table.'.'.self::table_name().'_id';
            $where = self::table_name().'.id = '.$where;
            $select= '';
            break;
          case 'belongs_to':
            $join_sql .= ' LEFT OUTER JOIN '.$foreign_table.' ON '.self::table_name().'.'.$foreign_table.'_id='.$foreign_table.'.id';
            $has_one_where = self::table_name().'.';
            break;
          case 'has_many':
            $join_sql .= ' LEFT OUTER JOIN '.$foreign_table.' ON '.self::table_name().'.id='.$foreign_table.'.'.self::table_name().'_id';
            $where = self::table_name().'.id = '.$where;
          // case 'has_many_through':
          //   $join_sql .= ' LEFT OUTER JOIN '.$foreign_table.' ON '.self::table_name().'.id='.$foreign_table.'.'.self::table_name().'_id';
          //   $where = self::table_name().'.id = '.$where;
          default:  
            # code...
            break;
        }       
      }
    }
    // var_dump('join_type ='.$join_type.'<br/> foreign_table = '.$foreign_table.'<br/>limit = '.$limit.'<br/>');
    
    if (!is_array($where)) {
      if(($join_type==''&&$foreign_table==''&&$limit=='')) {
        if (ctype_digit($where)) {
          $where = 'id = '.$where;
        }
      }
    } else {
      $c = count($where);
      // echo $c;
      $idin = $has_one_where.'id IN (';
      for ($i=0;$i<count($where);$i++) {
        $idin .= $where[$i];
        if($i<count($where)-1) {
          $idin .= ',';
        }
      }
      $idin .= ')';
      $where = $idin;
    }
    return 'SELECT *'.$select.' FROM '.self::table_name().$join_sql.' WHERE '.$where.$limit;
  }

  public static function search($column,$operator,$value,$limit = null) 
  { 
    self::instance();
    if ($limit!=null) {
      $limit1 = ' LIMIT '.$limit;
    } else {
      $limit1 = '';
    }
    $sql = "SELECT * FROM ".self::table_name()." WHERE ".$column." ".$operator." '".$value."'".$limit1.";";
    return self::_getResult($sql);
  }

  # Placeholder; Override this within individual models!
  public static function validate() 
  { 
    self::instance();
    return true;
  }

  public static function exists() 
  { 
    self::instance();
    if (self::$id!='') {
      $sql = "SELECT * FROM ".self::table_name()." WHERE id = ".self::$id." LIMIT 1";
      if (self::$db->fetch($sql)) {
        return true;
      } else {
        return false;
      }
    } else {
      return false;
    }
  }
  protected static function loadPropertiesFromDatabase() 
  { 
    self::instance();
    $sql = "SHOW COLUMNS FROM ".self::table_name()." WHERE EXTRA NOT LIKE '%auto_increment%'";
    $rs = self::$db->fetch($sql);
    return $rs;
  }
        
  public static function create() 
  {
    self::instance();
    $sql = "SHOW COLUMNS FROM ".self::table_name()." WHERE EXTRA NOT LIKE '%auto_increment%'";
    self::$c = new StdClass;
    self::$result = self::$db->fetch( $sql );
    for ($i=0;$i<count(self::$result);$i++) {
      $d = (string) self::$result[$i]->Field;
      self::$c->{$d} = null;
    }
    return self::$c;
  }

  public static function save() 
  { 
    self::instance();
    if (self::validate() === false) {
      return false;
    }
    # Table Name && Created/Updated Fields
    $table_name = self::table_name();

    $data = self::$c;
    $time = date('Y-m-d H:i:s');
    if (is_array(self::$c)) {
      //existing
      $data = self::$c[0];
      $data->updated_at = $time;
      self::$id = $data->id;
    } else {
      //new record
      $data = self::$c;
      $data->created_at = $time;
      $data->updated_at = '0000-00-00 00:00:00';
    }

    $properties = self::loadPropertiesFromDatabase();
    # Create SQL Query
    $sql_set_string = '';
    $total_properties_count = count($properties);
    $x = 0;

    foreach ($properties as $k=> $v) { 
      $qt='';
      $val = $v->Field;
      $type = $v->Type;
      $column_quote = ['tinyint','smallint','mediumint','int','bigint','float','double','decimal'];
      if (!self::strpos_array($type,$column_quote))
        $qt='"';
      $sql_set_string .= $val.'='.$qt.addslashes($data->$val).$qt;
      $x++;
      if ($x != $total_properties_count) { 
        $sql_set_string .= ', '; 
      }
    }

    # Final SQL Statement
    $sql = $table_name." SET ".$sql_set_string;
    if (self::exists()) { 
      $final_sql = 'UPDATE '.$sql.' WHERE id='.$data->id.';';
    } else { 
      $final_sql = "INSERT INTO ".$sql.';';
    }
    # Bind Vars
    foreach ($properties as $k => $v) { 
      $bind_vars[($k)] = $v->Field;
    }
    // var_dump($final_sql);
    $q = self::$db->execute($final_sql);
    if ($q) {
      // self::$last_insert_id = $q->id;
      return true;
    } else {
      return false;
    }
  }

  private static function strpos_array($haystack, $needles) 
  {
    if (is_array($needles)) {
      foreach ($needles as $str) {
        if (is_array($str)) {
          $pos = strpos_array($haystack, $str);
        } else {
          $pos = strpos($haystack, $str);
        }
        if ($pos !== FALSE) {
          return TRUE;
        }
      }
    } else {
      return strpos($haystack, $needles);
    }
  }
} 