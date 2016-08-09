<?php

use Betasyntax\Core\Application;
use Plasticbrain\FlashMessages\FlashMessages;

if ( ! function_exists('app'))
{
  /**
   * Get the available container instance.
   *
   * @param  string  $make
   * @param  array   $parameters
   * @return mixed|\Illuminate\Foundation\Application
   */
  function app()
  {
    return Betasyntax\Core\Application::getInstance();

    // return Container::getInstance()->make($make, $parameters);
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
   * @return \Illuminate\View\View
   */
  function view($view = null, $data = array())
  {
    // echo "<pre>";
    // var_dump(app());
    $twig = app()->container->get('Betasyntax\View\View');
    // var_dump(app()->getVersion());
    $twig->loadHelpers();
    $twig->render($view,$data);
  }
}

if (!function_exists('dd'))
{
  /**
   * Get the evaluated view contents for the given view.
   *
   * @param  string  $view
   * @param  array   $data
   * @param  array   $mergeData
   * @return \Illuminate\View\View
   */
  function dd($view = null, $data = array())
  {
    echo app()->util->dd($data);
  }
}

if ( ! function_exists('flash'))
{
  /**
   * Get the evaluated view contents for the given view.
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