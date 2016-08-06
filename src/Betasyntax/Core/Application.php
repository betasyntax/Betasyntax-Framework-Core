<?php namespace Betasyntax\Core;

use Closure;
use Betasyntax\Core\Container\Container;

class Application extends Container
{
  protected $version = '0.1';   
  protected $basePath;
  /**
   * Create a new Illuminate application instance.
   *
   * @param  string|null  $basePath
   * @return void
   */
  
  public function __construct($basePath = null)
  {
    if ($basePath) $this->setBasePath($basePath);

  }

  public function setBasePath($basePath)
  {
    $this->basePath =$basePath;
  }
}
