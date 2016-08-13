<?php

namespace Betasyntax\Db\Adapter;

use PDO;
use Betasyntax\Database;
/**
 * MySQLi Pdo
 */
class Pgsql implements AdapterInterface
{
  private $_dbh;
  public $_rec_set;

  public function connect(Database $config)
  {
    $dsn = sprintf('pgsql:dbname=%s;host=%s', $config->dbscheme, $config->host);
    $this->_dbh = new PDO($dsn, $config->user, $config->password, array());
  }
  public function fetch($sql)
  {
    $sth = $this->_dbh->prepare($sql);
    $sth->execute();
    $sth->setFetchMode(PDO::FETCH_OBJ);
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