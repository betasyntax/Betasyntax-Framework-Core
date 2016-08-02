<?php namespace Betasyntax\Core;

use Closure;

class Application 
{
  protected $version = '0.1';   

  /**
   * Create a new Illuminate application instance.
   *
   * @param  string|null  $basePath
   * @return void
   */
  
  public function __construct($basePath = null)
  {
    echo $this->version;
    $this->registerBaseBindings();

    $this->registerBaseServiceProviders();

    $this->registerCoreContainerAliases();

    if ($basePath) $this->setBasePath($basePath);
  }

  public function registerBaseBindings()
  {

  }

  public function registerBaseServiceProviders()
  {

  }

  public function registerCoreContainerAliases()
  {

  }
}