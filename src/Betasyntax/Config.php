<?php namespace Betasyntax;

use Betasyntax\Core\Application;

class Config {
    protected $app;
    public function __construct(Application $app) {
      echo "<pre>";
      $this->app = $app;
      var_dump($app);
      $this->loader();
    }
    private function loader() {
        foreach (glob($this->app->getBasePath()."/config/*.php") as $filename) {
          echo $filename;
          include $filename;
        }
        foreach (glob($this->app->getBasePath()."/app/Models/*.php") as $filename){
          include_once $filename;
        }
    }
}