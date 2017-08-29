<?php namespace Betasyntax\Orm;

use StdClass;
use Exception;
use Betasyntax\Db\DbFactory;
use Betasyntax\Logger\Logger;
use Betasyntax\Db\DatabaseConfig;
use Betasyntax\Orm\PdoValueBinder;
/**
 * 
 */
class BaseModel  
{
  /**
   * Configuration storage
   * @var array
   */
  protected  $config = array(
    'driver' => 'mysql',
    'host'   => 'localhost',
    'port'   => 3307,
    'fetch'  => 'stdClass'
  );

  protected $select;
  protected $where;
  protected $data;
  /**
   * [$belongs_to description]
   * @var [type]
   */
  public $belongs_to;
  public $table_name;
  public $lastId;
  /**
   * [$has_one description]
   * @var [type]
   */
  public $has_one;

  /**
   * [$has_one description]
   * @var [type]
   */
  public $has_many;

  /**
   * [$has_one description]
   * @var [type]
   */
  public $has_many_through;

  /**
   * [$has_one description]
   * @var [type]
   */
  public $has_one_through;

  /**
   * [$has_one description]
   * @var [type]
   */
  public $has_and_belongs_to_many;

  /**
   * [$has_one description]
   * @var [type]
   */
  public $select_as;

  /**
   * [$last_insert_id description]
   * @var [type]
   */
  protected $last_insert_id;

  // protected $record;
  protected $record;


  /**
   * [$d description]
   * @var [type]
   */
  public $properties = array();

  /**
   * [$arguments description]
   * @var array
   */
  protected $arguments = array( 'driver', 'host', 'database', 'user', 'password' );

  /* Constructor */
  /**
   * Database connection
   * @var PDO
   */
  protected $db;

  /**
   * Latest query statement
   * @var PDOStatement
   */
  protected $result;

  /**
   * Database information
   * @var stdClass
   */
  protected $info;


  /**
   * [$statement description]
   * @var array
   */
  protected $statement = array();

  /**
   * [$columns description]
   * @var array
   */
  protected $columns = array();

  /**
   * Tables shema information cache
   * @var array
   */
  protected $table = array();

  /**
   * [$id description]
   * @var [type]
   */
  // protected $id;

  /**
   * Primary keys information cache
   * @var array
   */
  protected $key = array();

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
    $this->db = DbFactory::connect($config);
    if ( ! $this->db) {
        $debugbar = app()->debugbar;
        $debugbar::$debugbar['exceptions']->addException(new Exception('Error connecting to database. Please check your settings'));
      flash()->error('Error connecting to database. Please check your settings');
    }
    restore_exception_handler();
    $this->info = (object) array($this->arguments);
    unset($this->info->password);
  }

  public function __get($key) { 
    return $this->properties[$key];
  }
  
  public function __set($key, $value) { 
    return $this->properties[$key] = $value;
  }

  public function config ($key = null, $value = null) 
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

  public function __toString () 
  {
    return $this->result ? $this->result->queryString : null;
  }  

  public function safe_exception (Exception $exception) 
  {
    die('Uncaught exception: '.$exception->getMessage());
  }

  public function table_name() 
  {
    $class = get_called_class();
    if($class=="Model") {
      $class = $this->table_name;
    }
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

  public function raw($sql) 
  {
    return $this->getResult($sql);
  }

  public function exec($sql,$data=array()) 
  {
    $this->result = $this->db->fetch($sql);
    return $this->result;
  }

  public function all($extra_unsafe_sql = false) 
  { 
    $sql = "SELECT * FROM ". $this->table_name();
    if ($extra_unsafe_sql) { 
      $sql .= " ".$extra;
    }
    $sql .= ";";
    return $this->getResult($sql);
  }

  public function delete($id) {
    $sql = 'DELETE FROM ' . $this->table_name() . ' WHERE id = ?';
    $q = $this->db->execute($sql,array($id));
    if ($q) {
      return true;
    } else {
      return false;
    }
  }

  public function find($id,$join_type='',$foreign_table='') 
  { 

    if(is_array($id)) {
      //we have an array lets use find by instead
      return $this->find_by($id,'',$join_type,$foreign_table);
    } elseif(is_numeric($id)) {
      $where = ' WHERE '.$this->table_name().'.id = ?';
      $sql = $this->getSql($join_type,'',$foreign_table,$where);
      return $this->getResult($sql[0],array($id));
    } else {
      return null;
    }
  }

  public function find_by($args, $order_by='', $limit='', $join_type='', $foreign_table='') 
  { 
    $type = new PdoValueBinder($this->db->_dbh);
    //loop through find by and get the where statments
    if (isset($args) && is_array($args)) {
      try {
        // set default where string
        $sql_where = '';
        // quote string
        $quote = '';
        // total arguments provided
        $total_args = count($args);
        // count for the array
        $cnt = 1;
        // defult string for the AND clause
        $and = '';
        $where = '';
        $values = [];
        // if the array is an associative array
        if (isAssoc($args)) {
          //loop through the data provided to the find_by function
          foreach ($args as $key => $value) {
            // set $and var if there are more args to come
            if ($cnt < $total_args) {
              $and = 'AND ';
            } else {
              $and = '';
            }
            // if the value is an array lets loop through it and build the actual sql clause
            if(is_array($value)) {
              // values begin here
              $where = $this->table_name().'.? IN (';
              $values[] = $type->type($key);
              // loop through array to get the values
              for($i=0;$i<count($value);$i++) {
                // check if string
                if(is_string($value[0])) {
                  if($i+1!=count($value)) {
                    $where .= '?, ';
                    $values[] = $type->type($value[$i]);
                  } else {
                    $where .= '?)';
                    $values[] = $type->type($value[$i]);
                  }
                // else its a numeric value
                } else {
                  if($i+1!=count($value)) {
                    $where .= '?, ';
                    $values[] = $type->type($value[$i]);
                  } else {
                    $where .= '?)';
                    $values[] = $type->type($value[$i]);
                  }
                }
              }
              $sql_where .= $where;
              if($cnt!=count($args)) {
                $sql_where .= ' OR ';
              }
            } else {
              $sql_where .= '`'.$this->table_name().'`.`'.$key.'` = ? '.$and;
              $values[] = $type->type($value);
            }
            $cnt++;
          }
        // Array is not associative
        } else {
          $where = '';
          for($i=0;$i<count($args);$i++) {
            if($i+1!=count($args)) {
              $where .= '?, ';
              $values[] = $type->type($args[$i]);
            } else {
              $where .= '?)';
              $values[] = $type->type($args[$i]);
            }
          }
          if($order_by = '') {

          }
          $sql_where .= $this->table_name().'.id IN ('.$where;
        }
        $sql_where = ' WHERE '.$sql_where;
      } catch (Exception $e) {
        $debugbar = app()->debugbar;
        $debugbar::$debugbar['exceptions']->addException($e);
      }
    } else {
      // if the just provided a single numeric value send it to find to handle
      return $this->find($args,$join_type,$foreign_table);
    }
    if($limit=='') {
      $order_by .= ';';
    }
    $sql2 = $this->getSql($join_type,$order_by,$foreign_table,$sql_where,$limit);
    // dd($sql2[0]);
    $x2 = $this->getResult($sql2[0],$values);
    return $x2;
  }

  private function getResult($sql,$data=null)
  {
    if(count($this->data)!=0) {
      $data = $this->data;
    }
    $result = $this->db->fetch($sql,$data);
    if (count($result)==1) {
      // we have a single row so we want to include each element to this object dynamically
      // $this->record = $result;
      foreach (((array) $result[0]) as $key => $value) {
        // uses the magic method to set and get variables from the properties array
        $this->$key = $value;
      }
      return $this;
    } else {
      return $result;
    }
  }

  private function getSql($join_type='', $order_by='', $foreign_table='', $where='', $limit='')
  {
    $join_sql = '';
    $join_sql2 = '';
    $where2 = $where;
    // base select statement
    $select = '';
    // the model class name
    $has_one_where = '';
    // holds the values so we can build a prepared statement
    $values = [];
    // set the order by
    if ($order_by!='' && $order_by !=';') {
      $order_by = ' ORDER BY '.$order_by;
    } else {
      $order_by = $order_by;
    }
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
        $select .= ',`'.$foreign_table.'`.`id` as `'.$foreign_table.'_id`';
        switch ($join_type) {
          case 'has_one':
            $join_sql .= ' LEFT OUTER JOIN `'.$foreign_table.'` ON `'.$this->table_name().'`.`id`=`'.$foreign_table.'`.`'.$this->table_name().'_id`';
            // $where = '`'.$this->table_name().'`.`id` = '.$where;
            $where2 = '`'.$this->table_name().'`.`id` = ?';
            break;
          case 'belongs_to':
            $join_sql .= ' LEFT OUTER JOIN `'.$foreign_table.'` ON `'.$this->table_name().'`.`'.$foreign_table.'_id`=`'.$foreign_table.'`.`id`';
            $has_one_where = '`'.$this->table_name().'`.';
            break;
          case 'has_many':
            $join_sql .= ' LEFT OUTER JOIN `'.$foreign_table.'` ON `'.$this->table_name().'`.`id`=`'.$foreign_table.'`.`'.$this->table_name().'_id`';
          // case 'has_many_through': // not implemented
          default:  
            # code...
            break;
        }       
      }
    }

    // find() function
    if (is_array($where)) {
      $c = count($where);
      $idin2 = $has_one_where.'`id` IN (';
      for ($i=0;$i<count($where);$i++) {
        $idin2 .= '?';
        $values[] = $where[$i];
        if($i<count($where)-1) {
          $idin .= ',';
        }
      }
      $idin .= ')';
      $where2 = $idin;
    }
    if($this->select=='') {
      $this->select = 'SELECT *'.$select;
    }
    if($this->select =='' && $this->where=='') {
      $this->where = ' WHERE '.$where2;
    } elseif ($this->where != '') {
      $this->where = ' WHERE '.$this->where;    
    } else {
      $this->where = $where2;
    }
    if($order_by =='') {
      $order_by = ';';
    }
    $sql_stub = $this->select.' FROM `'.$this->table_name().'`'.$join_sql.$this->where.$order_by.$limit;
    return array($sql_stub,$where2);
  }

  public function search($column,$operator,$value,$limit = null) 
  { 
    if ($limit!=null) {
      $limit1 = ' LIMIT '.$limit;
    } else {
      $limit1 = '';
    }
    $sql = "SELECT * FROM ".$this->table_name()." WHERE ".$column." ".$operator." '".$value."'".$limit1.";";
    return $this->getResult($sql);
  }

  # Placeholder; Override this within individual models!
  public function validate() {}

  public function exists() 
  { 
    // $this->instance();
    if (isset($this->id) && $this->id !='') {
      $sql = "SELECT * FROM ".$this->table_name()." WHERE id = ".$this->id." LIMIT 1";
      if ($this->db->fetch($sql)) {
        return true;
      } else {
        return false;
      }
    } else {
      return false;
    }
  }
  protected function loadPropertiesFromDatabase() 
  { 
    $sql = "SHOW COLUMNS FROM ".$this->table_name()." WHERE EXTRA NOT LIKE '%auto_increment%'";
    $rs = $this->db->fetch($sql);
    return $rs;
  }
        
  public function create() 
  {
    $sql = "SHOW COLUMNS FROM ".$this->table_name()." WHERE EXTRA NOT LIKE '%auto_increment%'";
    $this->record = $this;
    $this->result = $this->db->fetch( $sql );
    for ($i=0;$i<count($this->result);$i++) {
      $d = (string) $this->result[$i]->Field;
      $this->record->{$d} = null;
    }
    return $this->record;
  }

  public function save() 
  { 
    # Table Name && Created/Updated Fields
    $table_name = $this->table_name();
    $data = (object) $this->properties;
    $time = date('Y-m-d H:i:s');
    if (property_exists($data, 'id')) {
      //update time data
      $data->updated_at = $time;
      $this->id= $data->id;
    } else {
      //new record
      $data = $this->record;
      $data->created_at = 'NOW()';
      $data->updated_at = '';
    }
// 
    // dd($data);
    $properties = $this->loadPropertiesFromDatabase();
    # Create SQL Query
    $sql_set_string = '';
    $total_properties_count = count($properties);
    $x = 0;
    // first create values 
    foreach ($properties as $k=> $v) {
      $val = $v->Field;
      $type = $v->Type;
      if($data->$val == NULL) {
        $values[] = '';
      } else {
        $values[] = str_replace("`", "``", $data->$val);
      }
      $x++;
    }
    // set the sql statement
    if (count($values)!=$total_properties_count) {
      $total_properties_count = count($values);
    }
    $x = 0;
    foreach ($properties as $k=> $v) {
      $val = $v->Field;
      $type = $v->Type;
      $sql_set_string .= '`'.$val.'` = ?';
      if ($x < $total_properties_count-1) { 
        $sql_set_string .= ', '; 
      } else {  
        $sql_set_string .= '';     
      }
      $x++;
    }

    # Final SQL Statement
    $sql2 = '`'.$table_name."` SET ".$sql_set_string;
    if (property_exists($data, 'id')) { 
      $final_sql = 'UPDATE '.$sql2.' WHERE `id` = ?;';
      $values[] = $data->id;
    } else { 
      $final_sql = "INSERT INTO ".$sql2.';';
    }
    // dd($final_sql);
    if (static::validate() === false) {
      return false;
    }
    $q = false;
    // if ($this->validate()) {
      // dd('test');
      $q = $this->db->execute($final_sql, $values);
      $this->lastId = $this->db->lastId;
    // }
    if ($q) {
      return true;
    } else {
      return false;
    }
  }

  public function select($sql=null) {
    if(is_array($sql)) {
      $sql_statement = 'SELECT ';
      //loop through the array creating a string
      for($i=0;$i<count($sql);$i++) {
        $col = str_replace('.','`.`',$sql[$i]);
        if ($i == count($sql)-1) {
          $sql_statement .= '`'.$col.'` ';
        } else {
          $sql_statement .= '`'.$col.'`, ';
        }
      }
    } elseif (is_string($sql)) {
      // not forced to do prepared statements but its there
      $sql_statement = str_replace('SELECT ','',$sql);
      $sql_statement = "SELECT ".$sql." ";
    } else {
      // no sql provided select all
      $this->all();
    }
    $this->select = $sql_statement;
    return $this;
  }

  public function where($sql='',$data=null) {
    //set the where sql
    $this->where = $sql;
    $this->data = $data;
    return $this;
  }

  public function get() {
    $sql = $this->getSql('','',$this->data);
    return $this->getResult($sql[0],$this->data);
  }

  private function strpos_array($haystack, $needles) 
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

  public function interpolateQuery($query, $params) {
      $keys = array();
      # build a regular expression for each parameter
      foreach ($params as $key => $value) {
        if (is_string($key)) {
          $keys[] = '/:'.$key.'/';
        } else {
          $keys[] = '/[?]/';
        }
      }
      $query = preg_replace($keys, $params, $query, 1, $count);
      return $query;
  }

} 