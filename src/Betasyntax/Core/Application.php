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
   * Access to the pdo_queries
   * @var array
   */
  public $pdo_queries = array();

  /**
   * Access to the pdo_queries
   * @var array
   */
  public $pdo_records = array();
  
  /**
   * Access to the dynamic properties created by the service providers
   * @var array
   */
  private $data = array();

  /**
   * The global application instance
   * @var object
   */
  protected static $instance;

  /**
   * The version number
   * @var string
   */
  protected $version = '0.0.6';

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
   * Access to the class of the view service
   * @var string
   */
  public $viewClass;
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
    if (defined('PHPUNIT_BETASYNTAX_TESTSUITE') == true) {
      $this->getEnvironment();
    } else {
      $this->env['env']='test'
    }
    //load the backtrace if we are in development and turn on error reporting
    if (!$this->isProd())
      errReporter();
    //boot the app
    $this->boot();
    // Only load debug bar in production mode
    if(!$this->isProd()) {
      debugStack('Application');
    }

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
    return $this->viewClass;
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

  /**
   * Magic function to set a dynamic property
   * @param string $name The name of the property
   * @param object $value Can be any php type
   */
  public function __set($name, $value)
    {
      $this->data[$name] = $value;
      // $this->$key = $value;
    }

    /**
     * A magic function to get the dynamic property
     * @param string $name The name of the property
     * @return object Returns the property object
     */
    public function __get($name)
    {
      if (array_key_exists($name, $this->data)) {
        return $this->data[$name];
      }
      // return $name;
    }

    /**
     * A magic function to tell us if the property is set or not.
     * @param string $name Name of the property to check
     * @return boolean Returns true or false if the property is set
     */
    public function __isset($name)
    {
      return isset($this->data[$name]);
    }

    /**
     * Magic method to garbage collect our unused properties
     * @param string $name Property name
     */
    public function __unset($name)
    {
      unset($this->data[$name]);
    }
}
