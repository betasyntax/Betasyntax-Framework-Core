<?php

namespace Betasyntax\Db\Adapter;

use PDO as pd;
/**
 * MySQLi Pdo
 */
class Pdo implements AdapterInterface
{
  private $_dbh;
  public $_rec_set;

  public function connect(\config\Database $config)
  {
    $dsn = sprintf('mysql:dbname=%s;host=%s', $config->dbscheme, $config->host);
    $this->_dbh = new pd($dsn, $config->user, $config->password, array(pd::MYSQL_ATTR_INIT_COMMAND => 'SET sql_mode="ALLOW_INVALID_DATES"',pd::ATTR_ERRMODE => pd::ERRMODE_EXCEPTION));
    restore_exception_handler();
    $this->_dbh->exec('SET NAMES "UTF8"');
  }
  public function fetch($sql)
  {
    $sth = $this->_dbh->prepare($sql);
    $sth->execute();
    $sth->setFetchMode(pd::FETCH_OBJ);
    $this->_rec_set = $sth->fetchAll();
    return $this->_rec_set;
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