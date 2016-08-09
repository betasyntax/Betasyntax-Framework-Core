<?php
// File: ./Db/Factory.php
namespace Betasyntax\Db;

use Betasyntax\Database;
/**
 *  DbFactory Class
 */
class DbFactory
{
  /**
   * @param  DbConfig $config
   * @return $adapter
   */
  public static function connect(Database $config)
  {
    $className = sprintf("\\Betasyntax\\Db\\Adapter\\%s", $config->driver);
    if (class_exists($className)) {
      $adapter = new $className();
      $adapter->connect($config);
      return $adapter;
    }
  }
}
