<?php namespace Betasyntax\Core;

use Closure;
use Betasyntax\Core\Container\Container as BaseContainer;
use League\Container\Container as AppContainer;
use Betasyntax\Core\Services\ServiceProvider;
use Betasyntax\Session;

use League\Container\ServiceProvider\AbstractServiceProvider;
// use League\Container\Container;


class Application 
{
  protected $version = '03';   
  protected $basePath = NULL;
  protected static $instance;
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
    if(static::$instance==null) {
      static::$instance=$this;
    }
    //set the base path so we can use it for other plugins in the system
    $this->setBasePath($basePath);
    $this->boot();


  }
  static function getInstance()
  {
    return static::$instance;
  }

  public function getversion()
  {
    return $this->version;
  }
  public function setBasePath($basePath)
  {
    $this->basePath = realpath($basePath);
  }
  public function getBasePath()
  {
    return $this->basePath;
  }

  public function boot()
  {
    // get the routes!
    $routes = include $this->basePath.'/../app/routes.php';

    //start the session
    $this->session = Session::getInstance();
    
    // create the container instance
    $this->container = new AppContainer;
    
    //this will make containers for all of our classes.
    $this->container->delegate(
      new \League\Container\ReflectionContainer
    );

    // if we need any special special arguments to pass to any of our containers add them here
    $this->container->add('Betasyntax\Router')->withArgument($routes);
    $this->container->add('Betasyntax\View\View')->withArgument($this);
    $this->container->add('Betasyntax\Config')->withArgument($this);

    // set some defaults so we can use them in our controllers.
    // 
    // $this->container->get('Betasyntax\ModelsLoader');
    $this->util = $this->container->get('Betasyntax\Functions');
    $this->auth = $this->container->get('Betasyntax\Authentication');
    $this->response = $this->container->get('Betasyntax\Response');
    $this->config = $this->container->get('Betasyntax\Config');

  }
}
