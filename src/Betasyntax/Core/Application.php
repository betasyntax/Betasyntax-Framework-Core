<?php namespace Betasyntax\Core;

use Closure;
use Betasyntax\Session;
use Betasyntax\Core\Services\ServiceProvider;
use League\Container\Container as AppContainer;
use Betasyntax\Core\Container\Container as BaseContainer;

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
   * The registered type aliases.
   *
   * @var array
   */
  protected $aliases = [];

  /**
   * Holds all the core component references for the application
   * @var array
   */
  public $appProviders;

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
   * Holds our backtrace object so we can view our stacks
   * @var string
   */
  public $trace;

  /**
   * Create a new Illuminate application instance.
   *
   * @param  string|null  $basePath
   * @return void
   */
  
  public function __construct($basePath = null)
  {    
    //set the instance and store a reference to itself
    if(static::$instance==null)
      static::$instance=$this;
    //set the base path so we can use it for other plugins in the system
    $this->setBasePath($basePath);
    //configure dotenv
    $this->getEnvironment();
    //load the backtrace if we are in development and turn on error reporting
    if (!$this->isProd())
      errReporter();
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

  /**
   * Returns true if in production set in your .env file. false if in local mode which should be your dev machine
   * @return boolean
   */
  public function isProd()
  {
    if($this->env['env']=='prod') {
      return true;
    }
    return false;
  }

  /**
   * Loads the environment from your .env file
   * @return array
   */
  private function getEnvironment()
  {
    $env = new \Dotenv\Dotenv($this->basePath);
    $env->load();
    $this->env['env'] = getenv('APP_ENV');
    $this->env['debug'] = getenv('APP_DEBUG');
    $this->env['appSecret'] = getenv('APP_KEY');
  }

  /**
   * Get the application version number
   * @return string
   */
  public function getversion()
  {
    return $this->version;
  }

  /**
   * Sets the applications base path
   * @param string $basePath holds the base path string for the application
   */
  private function setBasePath($basePath)
  {
    $this->basePath = realpath($basePath);
  }

  /**
   * Get the base path for the app 
   * @return string
   */
  public function getBasePath()
  {
    return $this->basePath;
  }

  /**
   * Returns the applications view class object
   * @return string
   */
  public function getViewObjectStr()
  {
    for($i=0;$i<count($this->appProviders);$i++) {
      foreach ($this->appProviders[$i] as $k => $v) {
        if ($k == 'view')
          return $v;
      }
    }
  }

  /**
   * Application boot method. Loads the app providers and sets some default application variables
   * @return void
   */
  public function boot()
  {
    //start the session
    $this->session = Session::getInstance();
    // create the container instance
    $this->container = new AppContainer;    
    //boot the app and registers any middlewhere
    $this->container->addServiceProvider(new ServiceProvider($this)); 
  }
}
