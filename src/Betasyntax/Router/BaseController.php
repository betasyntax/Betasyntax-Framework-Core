<?php 
namespace Betasyntax\Router;

class BaseController
{  
  public $domain = ''; // default auth domain
  protected $close_session = false; // required to close session writing for any scripts requiring heavy ajax
  protected $response = null;
  protected $session;
  protected $flash = null;
  protected $middleware = [];
  protected $debugbar;

  public function __construct()
  {
    // error_log($this->middleware);
    // var_dump($this->middleware);
  }

  public function getMiddleware()
  {
    return $this->middleware;
  }
}