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
    if ($basePath) $this->setBasePath($basePath);
    $this->boot();
    // $this->

    // var_dump($container);
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
    $this->container = new AppContainer;
    // $this->container->delegate(
    //   new \League\Container\ReflectionContainer
    // );

    // $this->container->add('Betasyntax\Core\Application');
    $this->container->add('Betasyntax\Router');
    
    // $this->container->add('Betasyntax\Config');
    // $this->container->add('Betasyntax\ModelsLoader');
    $this->container->add('Betasyntax\Functions');
    $this->container->add('Betasyntax\Authentication');
    $this->container->add('Betasyntax\Response');
    // $this->container->add('config\Database')->withArgument($this);
    $this->container->add('Betasyntax\View\View')->withArgument($this);
    $this->container->add('Betasyntax\ModelsLoader');
    $this->container->add('config\Database');
    // new \Betasyntax\Config($this);

    $this->session = Session::getInstance();

    $this->container->get('Betasyntax\ModelsLoader');
    $this->util = $this->container->get('Betasyntax\Functions');
    $this->auth = $this->container->get('Betasyntax\Authentication');
    $this->response = $this->container->get('Betasyntax\Response');
  }
}
