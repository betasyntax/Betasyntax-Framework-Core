<?php namespace Betasyntax\Core\Container;

use Betasyntax\Core\Application;
use League\Container\Container as AppContainer;

Class Container
{
  /**
   * The registered type aliases.
   *
   * @var array
   */
  protected $aliases = [];

  /**
   * Application instance
   * @var object
   */
  protected $app;

  /**
   * Alias a type to a different name.
   *
   * @param  string  $abstract
   * @param  string  $alias
   * @return void
   */
  public function alias($abstract, $alias)
  {
    $this->aliases[$alias] = $abstract;
  }

  /**
   * Get the alias for the container
   * @param  $class
   * @return string
   */
  protected function getAlias($abstract)
  {
    return isset($this->aliases[$abstract]) ? $this->aliases[$abstract] : $abstract;
  }


  public function start(Application $app)
  {
    $this->app = new AppContainer;
    foreach ($this->aliases as $key => $value) {
      $this->app
        ->add($value, $key)
        ->withArgument($app); 
    }
  }
  public function get($item) 
  {
    return $this->app->get($item);
  }
}