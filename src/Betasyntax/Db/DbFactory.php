<?php namespace Betasyntax\Db;

use Betasyntax\Db\DatabaseConfig;

/**
 *  DbFactory Class
 */
class DbFactory
{
  /**
   * @param  DbConfig $config
   * @return $adapter
   */
  public static function connect(DatabaseConfig $config)
  {
    $className = sprintf("\\Betasyntax\\Db\\Adapter\\%s", $config->driver);
    if (class_exists($className)) {
      $adapter = new $className();
      $adapter->connect($config);
      return $adapter;
    }
  }
}
