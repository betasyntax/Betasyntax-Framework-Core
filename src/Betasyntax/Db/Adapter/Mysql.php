<?php

namespace Betasyntax\Db\Adapter;

/**
 * MySQLi Adapter
 */
class Mysqli implements Betasyntax\Db\Adapter\AdapterInterface
{
  private $_mysqli;
  public function connect(\config\DatabaseConfig $config)
  {
    $this->_mysqli = new \mysqli($config->host, $config->user, $config->password, $config->dbscheme);
  }
  
  public function fetch($sql)
  {
    return $this->_mysqli->query($sql)->fetch_object();
  }
  
  public function execute($sql) {}
  public function columnMeta() {}
  public function columnCount() {}
}
