<?php namespace Betasyntax\Orm;

use StdClass;
use Exception;
use Betasyntax\Db\DbFactory;
use Betasyntax\Db\DatabaseConfig;
use Betasyntax\Logger\Logger;

/**
 * 
 */
class BaseModel  
{
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

  /**
   * [$belongs_to description]
   * @var [type]
   */
  public static $belongs_to;

  /**
   * [$has_one description]
   * @var [type]
   */
  public static $has_one;

  /**
   * [$has_one description]
   * @var [type]
   */
  public static $has_many;

  /**
   * [$has_one description]
   * @var [type]
   */
  public static $has_many_through;

  /**
   * [$has_one description]
   * @var [type]
   */
  public static $has_one_through;

  /**
   * [$has_one description]
   * @var [type]
   */
  public static $has_and_belongs_to_many;

  /**
   * [$has_one description]
   * @var [type]
   */
  public static $select_as;

  /**
   * [$last_insert_id description]
   * @var [type]
   */
  protected static $last_insert_id;

  // protected static $tetimg;
  protected static $c;

  /**
   * [$d description]
   * @var [type]
   */
  protected static $d;

  /**
   * [$d description]
   * @var [type]
   */
  protected static $properties = array();

  /* Static instances */
  /**
   * Multiton instances
   * @var array
   */
  protected static $instance  = array();

  /**
   * [$arguments description]
   * @var array
   */
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

  /**
   * [$statement description]
   * @var array
   */
  protected static $statement = array();

  /**
   * [$columns description]
   * @var array
   */
  protected static $columns = array();

  /**
   * Tables shema information cache
   * @var array
   */
  protected static $table = array();

  /**
   * [$id description]
   * @var [type]
   */
  protected static $id;

  /**
   * Primary keys information cache
   * @var array
   */
  protected static $key = array();

  public function __construct ($config = false) 
  {
    $app = app();
    if (!$config){
      $dbtype = config('db','default');
      $dbconfig = config('db',$dbtype);
      $config = new DatabaseConfig;
      $config->driver = $dbconfig['driver'];
      $config->host = $dbconfig['host'];
      $config->user = $dbconfig['user'];
      $config->password = $dbconfig['pass'];
      $config->dbscheme = $dbconfig['schema'];
    }
    self::$db = DbFactory::connect($config);
    if ( ! self::$db) {
        $debugbar = app()->debugbar;
        $debugbar::$debugbar['exceptions']->addException(new Exception('Error connecting to database. Please check your settings'));
      flash()->error('Error connecting to database. Please check your settings');
    }
    restore_exception_handler();
    self::$info = (object) array(self::$arguments);
    unset(self::$info->password);
  }

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

  public static function safe_exception (Exception $exception) 
  {
    die('Uncaught exception: '.$exception->getMessage());
  }

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

  public static function raw($sql) 
  {
    self::instance();
    return self::_getResult($sql);
  }

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
    // set the instance
    self::instance();
    //loop through find by and get the where statments
    if (isset($args) && is_array($args)) {
      try {
        // set default where string
        $sql_where = '';
        $sql_where2 = '';
        // quote string
        $quote = '';
        // total arguments provided
        $total_args = count($args);
        // count for the array
        $cnt = 1;
        // defult string for the AND clause
        $and = '';
        $where2 = '';
        $values = [];
        // if the array is an associative array
        if (isAssoc($args)) {
          //loop through the data provided to the find_by function
          foreach ($args as $key => $value) {
            // check if value is a string if so we need to add a " remove for prepared statements
            if (is_string($value))
              $quote = '"';
            // set $and var if there are more args to come
            if ($cnt < $total_args)
              $and = 'AND ';
            // if the value is an array lets loop through it and build the actual sql clause
            if(is_array($value)) {
              // values begin here
              $where = self::table_name().'.'.$key.' IN (';
              $where2 = self::table_name().'.? IN (';
              $values[] = $key;
              // loop through array to get the values
              for($i=0;$i<count($value);$i++) {
                // check if string
                if(is_string($value[0])) {
                  if($i+1!=count($value)) {
                    $where .= '"'.$value[$i].'", ';
                    $where2 .= '?, ';
                    $values[] = $value[$i];
                  } else {
                    $where .= '"'.$value[$i].'")';
                    $where2 .= '?)';
                    $values[] = $value[$i];
                  }
                //
                } else {
                  if($i+1!=count($value)) {
                    $where .= $value[$i].', ';
                    $where2 .= '?, ';
                    $values[] = $value[$i];
                  } else {
                    $where .= $value[$i].')';
                    $where2 .= '?)';
                    $values[] = $value[$i];
                  }
                }
              }
              $sql_where .= $where;
              $sql_where2 .= $where2;
              if($cnt!=count($args)) {
                $sql_where .= ' OR ';
                $sql_where2 .= ' OR ';
              }
            } else {
              $sql_where .= self::table_name().'.'.$key.' = '.$quote.$value.$quote.' '.$and;
              $sql_where2 .= self::table_name().'.? = ? '.$and;
              $values[] = $key;              
              $values[] = $value;
            }
            $cnt++;
          }
        // Array is not associative
        } else {
          $where = '';
          $where2 = '';
          for($i=0;$i<count($args);$i++) {
            if($i+1!=count($args)) {
              $where .= $args[$i].', ';
              $where2 .= '?, ';
              $values[] = $args[$i];
            } else {
              $where .= $args[$i].')';
              $where2 .= '?)';
              $values[] = $args[$i];
            }
          }
          $sql_where .= self::table_name().'.id IN ('.$where;
          $sql_where2 .= self::table_name().'.id IN ('.$where2;
        }
      } catch (Exception $e) {
        $debugbar = app()->debugbar;
        $debugbar::$debugbar['exceptions']->addException($e);
      }
    } else {
      // if the just provided a single numeric value send it to find to handle
      return static::find($args,$join_type,$foreign_table);
    }
    $sql = self::_getSql($join_type,$foreign_table,$sql_where,$limit);   
    $x = self::_getResult($sql[0],$sql[1]);

    return $x;
  }

  public static function find($id,$join_type='',$foreign_table='') 
  { 
    // now uses prepared statements
    self::instance();
    if(is_array($id)) {
      //we have an array lets use find by instead
      return static::find_by($id,'',$join_type,$foreign_table);
    } elseif(is_numeric($id)) {
      $where = self::table_name().'.id = ?';
      $sql = self::_getSql($join_type,$foreign_table,$where);
      return self::_getResult($sql[0],$id);
    } else {
      return null;
    }
  }

  public static function where($result)
  {
    return $result;
  }

  private static function _getResult($sql,$data=null)
  {
    self::$result = self::$db->fetch($sql,$data);
    self::$c = self::$result;
    if (count(self::$result)==1) {
      return (object) self::$result[0];
    } else {
      return self::$result;
    }
  }

  private static function _getSql($join_type='',$foreign_table='',$where='',$limit='')
  {
    // holds the join string
    $join_sql = '';
    $join_sql2 = '';
    $where2 = $where;
    // base select statement
    $select = '';
    // the model class name
    $has_one_where = '';
    // holds the values so we can build a prepared statement
    $values = [];
    // set the limit
    if ($limit !='') {
      $limit = ' LIMIT '.$limit.';';
    }
    // Build the with has_many, has_one and belongs_to sql joins strings and values
    if ($join_type!='') {
      // set the default limit if the limit = '' assuming has_one
      if ($limit =='') {
        $limit = ' LIMIT 1';
      }
      // set the limit to nothing if we have has_many and $where is an array so limit is useless
      if (($join_type=='has_many') || (is_array($where))) {
        $limit = '';
      }
      //build the joins sql string
      if (in_array($join_type,['has_one','belongs_to','has_many'])) {
        //start building the 
        $select .= ','.$foreign_table.'.id as '.$foreign_table.'_id';
        switch ($join_type) {
          case 'has_one':
            // dd('has_one');
            $join_sql .= ' LEFT OUTER JOIN '.$foreign_table.' ON '.self::table_name().'.id='.$foreign_table.'.'.self::table_name().'_id';
            $where = self::table_name().'.id = '.$where;
            $where2 = self::table_name().'.id = ?';
            break;
          case 'belongs_to':
            $join_sql .= ' LEFT OUTER JOIN '.$foreign_table.' ON '.self::table_name().'.'.$foreign_table.'_id='.$foreign_table.'.id';
            $has_one_where = self::table_name().'.';
            break;
          case 'has_many':
            $join_sql .= ' LEFT OUTER JOIN '.$foreign_table.' ON '.self::table_name().'.id='.$foreign_table.'.'.self::table_name().'_id';
          // case 'has_many_through':
          //   $join_sql .= ' LEFT OUTER JOIN '.$foreign_table.' ON '.self::table_name().'.id='.$foreign_table.'.'.self::table_name().'_id';
          //   $where = self::table_name().'.id = '.$where;
          default:  
            # code...
            break;
        }       
      }
    }
    // find() function
    if (is_array($where)) {
      $c = count($where);
      $idin = $has_one_where.'id IN (';
      $idin2 = $has_one_where.'id IN (';
      for ($i=0;$i<count($where);$i++) {
        /*
        change to prepared statement
         */
        $idin .= $where[$i];
        $idin2 .= '?';
        $values[] = $where[$i];
        if($i<count($where)-1) {
          $idin .= ',';
        }
      }
      $idin .= ')';
      $where = $idin;
      $where2 = $idin;
    }
    // dd($where2);
    // dd($values);
    return array('SELECT *'.$select.' FROM '.self::table_name().$join_sql.' WHERE '.$where2.$limit.';',$where2);
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