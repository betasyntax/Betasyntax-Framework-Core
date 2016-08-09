<?php namespace Betasyntax;

use Betasyntax\Core\Application;
use Noodlehaus\Config as Conf;

class Config {
    
    protected $app;
    public $conf;

    public function __construct(Application $app) {
      $this->app = $app;
      $this->conf = Conf::load($app->getBasePath().'/../config/config.json');
    }
}