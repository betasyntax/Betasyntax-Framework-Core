<?php 
namespace Betasyntax;


class BaseController
{  
  public $domain = ''; // default auth domain
  protected $close_session = false; // required to close session writing for any scripts requiring heavy ajax
  protected $response = null;
  protected $session;
  protected $flash = null;
  protected $middleware = [];

}