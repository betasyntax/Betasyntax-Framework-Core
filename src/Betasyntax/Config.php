<?php

namespace Betasyntax;

class Config {
    public function __construct() {
      $this->loader();
    }
    private function loader() {
        foreach (glob(APP_ROOT."config/*.php") as $filename) {
          include $filename;
        }
        foreach (glob(APP_ROOT."app/Models/*.php") as $filename){
          include_once $filename;
        }
    }
}