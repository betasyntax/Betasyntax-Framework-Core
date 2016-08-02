<?php 
namespace Betasyntax;

use Betasyntax\Authentication as Auth;

class BaseController
{  
  public $domain = ''; // default auth domain
  protected $close_session = false; // required to close session writing for any scripts requiring heavy ajax
  protected $response = null;
  protected $session;
  protected $flash = null;

  public function __construct()
  {
    $this->flash = app()->flash;
    $this->session = app()->session;

    Auth::secure($this->domain);
    
    if (get_called_class()!='App\Controllers\SetupController') {
      Setup::wizard($this->domain);
    }

    if($this->close_session) {
      $this->session->close();
    }
  }
}