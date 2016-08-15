<?php namespace Betasyntax\Core;

use Closure;
use Betasyntax\Core\Container\Container as BaseContainer;
use League\Container\Container as AppContainer;
use Betasyntax\Core\Services\ServiceProvider;
use Betasyntax\Session;

/**
 * Main Application class
 */
class Application 
{
  /**
   * The global application instance
   * @var object
   */
  protected static $instance;

  /**
   * The version number
   * @var string
   */
  protected $version = '0.0.3';

  /**
   * The base path of the application
   * @var string
   */
  protected $basePath = NULL;

  /**
   * Provides the name of the view class in use. configured in app/config.php
   * @var string
   */
  protected $viewObjectStr;

  /**
   * The main application configuration
   * @var array
   */
  protected $appConf;

  /**
   * Holds all the core component references for the application
   * @var array
   */
  protected $appProviders;

  /**
   * The main container object league/container
   * @var type
   */
  public $container;

  /**
   * Main view object twig
   * @var object
   */
  public $view;

  /**
   * The router object
   * @var object
   */
  public $router;

  /**
   * Config object
   * @var object
   */
  public $config;

  /**
   * Holds the session object
   * @var object
   */
  public $session;

  /**
   * Holds the flash object
   * @var object
   */
  public $flash;

  /**
   * Holds the authentication object
   * @var object
   */
  public $auth;

  /**
   * Holds the main response object guzzle
   * @var object
   */
  public $response;

  /**
   * Holds the logger object
   * @var object
   */
  public $logger;

  /**
   * Holds the debug bar object
   * @var object
   */
  public $debugbar;

  /**
   * Holds the environment config
   * @var array
   */
  public $env = [];

  /**
   * The registered type aliases.
   *
   * @var array
   */
  protected $aliases = [];
  public $pdo;
  public $pdo_queries = [];
  public $pdo_records = [];
  public $trace;
  public $twig;

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
    //get the main app config
    $this->getConfArray();
    //configure dotenv
    $this->getEnvironment();
    //assign the middleware array
    $this->appProviders = $this->getProvidersArray();
    //boot the app
    $this->boot();

  }

  /**
   * [getInstance get the app instance. to use the app object just use app()->property/method]
   * @return [type] [description]
   */
  static function getInstance()
  {
    return static::$instance;
  }

  public function isProd()
  {
    if($this->env['env']=='prod') {
      return TRUE;
    }
    return FALSE;
  }

  private function getEnvironment()
  {
    $env = new \Dotenv\Dotenv($this->basePath.'/../');
    $env->load();
    $this->env['env'] = getenv('APP_ENV');
    $this->env['debug'] = getenv('APP_DEBUG');
    $this->env['appSecret'] = getenv('APP_KEY');
  }

  private function getConfArray()
  {
    $this->appConf = include $this->basePath.'/../config/app.php';
  }
  public function conf($key)
  {
    return $this->appConf[$key];
  }

  private function getProvidersArray()
  {
    return $this->appConf['providers'];
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
    foreach ($this->appProviders as $k => $v) {
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
    $this->container->addServiceProvider(new ServiceProvider($this,$this->getProvidersArray())); 

  }

  public function router()
  {
  }
}
