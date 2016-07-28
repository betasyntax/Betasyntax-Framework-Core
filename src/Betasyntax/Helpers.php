<?php

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
    echo app()->twig->render($view,$data);
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
    return app()->flash;
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