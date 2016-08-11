<?php namespace Betasyntax\Core;

use Closure;
use Betasyntax\Core\Container\Container as BaseContainer;
use League\Container\Container as AppContainer;
use Betasyntax\Core\Services\ServiceProvider;
use Betasyntax\Session;


class Application 
{
  protected static $instance;
  protected $version = '0.0.1';   
  protected $basePath = NULL;
  protected $viewObjectStr;
  protected $appConf;
  public $container;
  public $view;
  public $router;
  public $config;
  public $session;
  public $flash;
  public $auth;
  public $response;

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
    //set the instance and store a reference to itself
    if(static::$instance==null) {
      static::$instance=$this;
    }
    //set the base path so we can use it for other plugins in the system
    $this->setBasePath($basePath);
    //assign the middleware array
    $this->appConf = $this->getMiddleWareArray();
    //boot the app
    $this->boot();
  }

  static function getInstance()
  {
    return static::$instance;
  }

  private function getMiddleWareArray()
  {
    $middleware = include $this->basePath.'/../config/app.php';
    return $middleware['providers'];
  }

  public function getversion()
  {
    return $this->version;
  }

  private function setBasePath($basePath)
  {
    $this->basePath = realpath($basePath);
  }

  public function getBasePath()
  {
    return $this->basePath;
  }

  public function getViewObjectStr()
  {
    foreach ($this->appConf as $k => $v) {
      if ($k == 'view')
        return $v;
    }
  }

  public function boot()
  {
    //start the session
    $this->session = Session::getInstance();
    // create the container instance
    $this->container = new AppContainer;    
    //boot the app and registers any middlewhere
    $this->container->addServiceProvider(new ServiceProvider($this,$this->getMiddleWareArray()));    
  }
}
