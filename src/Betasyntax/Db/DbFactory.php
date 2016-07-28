<?php
// File: ./Db/Factory.php
namespace Betasyntax\Db;

use config\Config;
/**
 *  DbFactory Class
 */
class DbFactory
{
  /**
   * @param  \config\DatabaseConfig
   * @return $adapter
   */
  public static function connect(\config\DatabaseConfig $config)
  {
    $className = sprintf("\\Betasyntax\\Db\\Adapter\\%s", $config->driver);
    if (class_exists($className)) {
      $adapter = new $className();
      $adapter->connect($config);
      return $adapter;
    }
  }
}
