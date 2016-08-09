<?php namespace Betasyntax;

use Betasyntax\Core\Application;

class ModelsLoader 
{
    public function __construct() {
      $this->loader();
    }

    private function loader() {
      foreach (glob(app()->getBasePath()."/../app/Models/*.php") as $filename){
        include_once $filename;
      }
    }
}