<?php namespace Betasyntax\Core\View;

use Betasyntax\Core\Application;
use Betasyntax\View\CoreHelpers;

Class ViewProvider
{
  protected $app;

  public function __construct()
  {    
    $app = new Application;
    if($app->getInstance()!=NULL) {
      $this->app = $app::getInstance();
    } else {
      $this->app = $app;
    }
  }

  public function render($view,$data) {
    echo $this->twig->render($view,$data);
  }

  public function loadHelpers()
  {
    $helpers = new \App\Helpers;
    foreach ($helpers::helpers() as $helper => $func) {
      $this->twig->addFunction($func);
    }
  }

  public function loadLocalHelpers()
  {
    $helpers = new CoreHelpers;
    foreach ($helpers::helpers() as $helper => $func) {
      $this->twig->addFunction($func);
    }
  }
}