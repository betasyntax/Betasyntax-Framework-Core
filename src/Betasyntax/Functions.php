<?php
use App\Models\User;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Betasyntax\Core\Application;
use Plasticbrain\FlashMessages\FlashMessages;

if ( ! function_exists('app'))
{
  /**
   * Get the available container instance.
   *
   * @param  string  $make
   * @param  array   $parameters
   * @return 
   */
  function app()
  {
    return Betasyntax\Core\Application::getInstance();
  }
}

if (!function_exists('config'))
{
  /**
   * Get the config object.
   *
   * @param  string  $view
   * @param  array   $data
   * @param  array   $mergeData
   * @return 
   */
  function config($key)
  {
    $config = app()->config;
    return $config->conf[$key];
  }
}

if (!function_exists('env'))
{
  /**
   * Get the config object.
   *
   * @param  string  $view
   * @param  array   $data
   * @param  array   $mergeData
   * @return 
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
   * @param  string  $view
   * @param  array   $data
   * @param  array   $mergeData
   * @return 
   */
  function view($view = null, $data = array())
  {
    $twig = app()->container->get(app()->getViewObjectStr());
    $twig->loadHelpers();
    $twig->render($view,$data);
  }
}

if (!function_exists('__file_meta'))
{
  /**
   * Get the evaluated view contents for the given view.
   *
   * @param  string  $view
   * @param  array   $data
   * @param  array   $mergeData
   * @return 
   */
  function __file_meta()
  {
    return __FILE__ . ':~' . __LINE__ . ': ';
  }
}

if (!function_exists('debugException'))
{
  /**
   * Get the evaluated view contents for the given view.
   *
   * @param  string  $view
   * @param  array   $data
   * @param  array   $mergeData
   * @return 
   */
  function debugException($e)
  {
    return app()->debugbar->exception($e);
  }
}


if (!function_exists('dd'))
{
  /**
   * Return dd in the view
   *
   * @param  string  $view
   * @param  array   $data
   * @param  array   $mergeData
   * @return 
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
   * Return dd in the view
   *
   * @param  string  $view
   * @param  array   $data
   * @param  array   $mergeData
   * @return 
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
   * Return dd in the view
   *
   * @param  string  $view
   * @param  array   $data
   * @param  array   $mergeData
   * @return 
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
   * Show the flash
   *
   * @param  string  $view
   * @param  array   $data
   * @param  array   $mergeData
   * @return \Illuminate\View\View
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
   * Get the evaluated view contents for the given view.
   *
   * @param  string  $view
   * @param  array   $data
   * @param  array   $mergeData
   * @return \Illuminate\View\View
   */
  function redirect($url='/')
  {
    return app()->response->redirect($url);
  }
}