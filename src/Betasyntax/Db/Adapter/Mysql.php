<?php

namespace Betasyntax\Db\Adapter;

use Betasyntax\Db\Adapter\AdapterInterface as DbInterface;
use config\DatabaseConfig as DbConfig;
use mysqli;
/**
 * MySQLi Adapter
 */
class Mysql implements DbInterface
{
  
  private $_mysqli;

  public function connect(DbConfig $config)
  {
    $this->_mysqli = new mysqli($config->host, $config->user, $config->password, $config->dbscheme);
  }
  
  public function fetch($sql)
  {
    return $this->_mysqli->query($sql)->fetch_object();
  }
  
  public function execute($sql) {}
  public function columnMeta() {}
  public function columnCount() {}
}
