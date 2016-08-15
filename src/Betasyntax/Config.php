<?php namespace Betasyntax;

use Betasyntax\Core\Application;
use Noodlehaus\Config as Conf;

class Config {
    
    protected $app;
    public $conf;

    public function __construct() {
      $this->app = app();
      $this->conf = Conf::load($this->app->getBasePath().'/config/config.php');
    }
}