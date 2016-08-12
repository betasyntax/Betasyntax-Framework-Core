<?php

namespace Betasyntax\Db\Adapter;

use PDO as pd;
use Betasyntax\Database;
/**
 * MySQLi Pdo
 */
class Pdo implements AdapterInterface
{
  private $_dbh;
  public $_rec_set;

  public function connect(Database $config)
  {
    $dsn = sprintf('mysql:dbname=%s;host=%s', $config->dbscheme, $config->host);
    // var_dump("dsn");
    // var_dump($dsn);
    // exit();
    // $conf = config('mysql');
    // $errormode = $conf['errormode'];
    // try {
      $this->_dbh = new pd($dsn, $config->user, $config->password, array(pd::MYSQL_ATTR_INIT_COMMAND => 'SET sql_mode="ALLOW_INVALID_DATES"'));
      // restore_exception_handler();
      // $this->_dbh->exec('SET NAMES "UTF8"');
    // }
    //   catch( PDOException $err ) {
    // }
  }
  public function fetch($sql)
  {
    // try {
      $sth = $this->_dbh->prepare($sql);
      $sth->execute();
      $sth->setFetchMode(pd::FETCH_OBJ);
      $this->_rec_set = $sth->fetchAll();
      return $this->_rec_set;
    // } catch(PDOException $e) { 
    //    echo "PDO: " . (string) $e; 
    // } catch(Exception $e) { 
    //    echo "OTHER: " . (string) $e; 
    // }
  }
  public function execute($sql)
  {
    return $this->_dbh->exec($sql);
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