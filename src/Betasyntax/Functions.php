<?php
use App\Models\User;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Plasticbrain\FlashMessages\FlashMessages;

if ( ! function_exists('app'))
{
  /**
   * Get the available container instance.
   *
   * @return application main instance
   */
  function app()
  {
    return Betasyntax\Core\Application::getInstance();
  }
}

if (!function_exists('urlHelper'))
{
  /**
   * Returns a link for a view
   *
   * @return string
   */
  function urlHelper($route,$args)
  {
    return app()->router->urlHelper($route,$args);
  }
}

if (!function_exists('config'))
{
  /**
   * Get the config object.
   *
   * @return array
   */
  function config($file,$key)
  {
    $config = app()->config;
    return $config->conf[$file][$key];
  }
}

if (!function_exists('env'))
{
  /**
   * Get environment vars set in your .env
   *
   * @return string
   */
  function env($key)
  {
    $config = app()->env;
    return $config->env[$key];
  }
}

if (!function_exists('view'))
{
  /**
   * Get the evaluated view contents for the given view.
   *
   * @return string
   */
  function view($view = null, $data = array())
  {
    // dd(app()->getViewObjectStr());
    $twig = app()->container->get(app()->getViewObjectStr());
    $twig->loadHelpers();
    $twig->render($view,$data);
  }
}
if (!function_exists('errReporter'))
{
  /**
   * Turns on error reporting in local mode
   *
   * @return void
   */
  function errReporter()
  {
    error_reporting(E_ALL);
    ini_set('xdebug.show_exception_trace', 0);
    ini_set('display_errors', TRUE);
    ini_set('display_startup_errors', TRUE);
    ini_set('xdebug.trace_format',2);
    ini_set("html_errors", 1); 
    ini_set("error_prepend_string", "<pre style='color: #333; font-face:monospace; font-size:8pt;'>"); 
    ini_set("error_append_string ", "</pre>");
    app()->trace = debug_backtrace();
  }
} 

if (!function_exists('dd'))
{
  /**
   * Return dd in the view
   *
   * @return string
   */
  function dd($data)
  {
    // echo app()->util->dd($data);
    echo '<pre class="var_dump">';
    var_dump($data);
    echo '</pre>';
  }
}



if (!function_exists('debugbar'))
{
  /**
   * Returns the debug bar object if in local mode
   * 
   * @return instance
   */
  function debugbar()
  {
    // echo app()->util->dd($data);
    return Betasyntax\DebugBar\DebugBar::getInstance();
  }
}

if (!function_exists('debugStack'))
{
  /**
   * Use this function to debug the stack on for your class. Use it anywhere in your app.
   *
   * @return void
   */
  function debugStack($title="")
  {
    $x = Betasyntax\DebugBar\DebugBar::getInstance();
    $x::$debugbar["messages"]->addMessage('Stack Trace: '. $title);
    $x::$debugbar["messages"]->addMessage(debug_backtrace());
  }
}
if ( ! function_exists('flash'))
{
  /**
   * Access to the application flash objecjt. 
   * flash()->error('error');
   * flash()->info('info');
   * flash()->success('success');
   * 
   * @return flash object
   */
  function flash()
  {
    $flash = new FlashMessages();
    return $flash;
  }
}

if ( ! function_exists('redirect'))
{
  /**
   * Redirects to a new page.
   *
   */
  function redirect($url='/')
  {
    return header('Location: '.$url);
  }
}

if ( ! function_exists('debug'))
{
  /**
   * Redirects to a new page.
   *
   */
  function debug($value='',$tag='',$type = 'info', $object = null)
  {
    $debug = debugbar();
    return $debug->message($value,$tag,$type,$object);
  }
}


if ( ! function_exists('isAssoc'))
{
  /**
   * Redirects to a new page.
   *
   */
  function isAssoc($arr)
  {
    return array_keys($arr) !== range(0, count($arr) - 1);
  }
}
