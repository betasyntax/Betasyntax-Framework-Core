<?php namespace Betasyntax\Core\Config\View;

use Betasyntax\Core\Application;

Class ViewProvider
{
  public function __construct(Application $app)
  {
    $className = sprintf("\\Betasyntax\\Db\\Adapter\\%s", $config->driver);
    if (class_exists($className)) {
      $adapter = new $className();
      $adapter->connect($config);
      return $adapter;
    }
  }
}