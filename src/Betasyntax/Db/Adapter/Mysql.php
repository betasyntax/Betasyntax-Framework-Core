<?php namespace Betasyntax\Db\Adapter;

use PDO;
use Betasyntax\Db\DatabaseConfig;

/**
 * MySQLi Pdo
 */
class Mysql implements AdapterInterface
{
  public $_dbh;
  public $_rec_set;
  public $lastId;

  public function connect(DatabaseConfig $config)
  {
    $app = app();
    $dsn = sprintf('mysql:dbname=%s;host=%s', $config->dbscheme, $config->host);
    try {
      $this->_dbh = new PDO($dsn, $config->user, $config->password, array(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION,PDO::MYSQL_ATTR_INIT_COMMAND => 'SET sql_mode="ALLOW_INVALID_DATES"'));

      $this->_dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
      $app->pdo = $this->_dbh;
    } catch(PDOException $e) {
      $debug = $app->debugbar;
      $debug::$debugbar['exceptions']->addException($e);
      echo 'ERROR: ' . $e->getMessage();
    }
  }
  public function fetch($sql,$data=null)
  {
    $app = app();
    $started = microtime(true);
    $sth = $this->_dbh->prepare($sql);
    $test = $sth->execute($data);
    $sth->setFetchMode(PDO::FETCH_OBJ);
    $this->_rec_set = $sth->fetchAll();
    $this->lastId = $this->_dbh->lastInsertId();
    if( ! $app->isProd()) {
      $end = microtime(true);
      $difference = $end - $started;
      $queryTime = number_format($difference, 10);
      $app->debugbar->addCollector(new \Betasyntax\DebugBar\DbCollector());
      $app->pdo_queries[] = [$sql,$queryTime,'test'];
      $app->pdo_records[] = $this->_rec_set;
    }

    return $this->_rec_set;  
  }

  public function query($sql)
  {
    echo $sql;
    echo $this->_dbh->exec($sql);
    dd($this->_dbh->errorInfo());
  }

  public function execute($sql,$data)
  {
    try {
      $app = app();
      $started = microtime(true);
      $this->_dbh->beginTransaction(); 
      $sth = $this->_dbh->prepare($sql);
      $data = $sth->execute($data);
      $this->lastId = $this->_dbh->lastInsertId();
      $this->_dbh->commit(); 
      if( ! $app->isProd()) {
        $end = microtime(true);
        $difference = $end - $started;
        $queryTime = number_format($difference, 10);
        $app->debugbar->addCollector(new \Betasyntax\DebugBar\DbCollector());
        $app->pdo_queries[] = [$sql,$queryTime,'test'];
        $app->pdo_records[] = $this->_rec_set;
      }

      return true;
    } catch(PDOExecption $e) { 
      $this->_dbh->rollback(); 
      return "Error!: " . $e->getMessage() . "</br>";
    }
  }

  public function columnMeta() 
  { 
    return $this->_dbh->getColumnMeta(0);
  }
  public function columnCount() 
  {
    return $this->_dbh->columnCount();
  }
}