<?php namespace Betasyntax\Core;

use Closure;
use Betasyntax\Core\Container\Container as BaseContainer;
use League\Container\Container as AppContainer;
use Betasyntax\Core\Services\ServiceProvider;

use League\Container\ServiceProvider\AbstractServiceProvider;
// use League\Container\Container;


class Application extends BaseContainer
{
  protected $version = '03';   
  protected $basePath;
  /**
   * The registered type aliases.
   *
   * @var array
   */
  protected $aliases = [];

  /**
   * Create a new Illuminate application instance.
   *
   * @param  string|null  $basePath
   * @return void
   */
  
  public function __construct($basePath = null)
  {
    if ($basePath) $this->setBasePath($basePath);

    $container = new AppContainer;
    var_dump($container);

    $container->addServiceProvider(new Betasyntax\Core\Services\ServiceProvider);
    // $this->registerAliases();
    // $this->register();
  }
  public function getversion()
  {
    return $this->version;
  }
  public function setBasePath($basePath)
  {
    $this->basePath = $basePath;
  }

  // public function registerAliases() {
  //   $aliases = array(
  //     'app' => 'Betasyntax\Core\Application',
  //     'router' => 'Betasyntax\Router',
  //     'config' => 'Betasyntax\Config;',
  //     'view' => 'Betasyntax\View\View'
  //   );
  //   foreach ($aliases as $key => $aliases) {
  //     foreach ((array) $aliases as $alias) {
  //       $this->alias($key, $alias);
  //     }
  //   }    
  // }

  // public function register()
  // {
  //   return parent::start($this);
  // }
}
