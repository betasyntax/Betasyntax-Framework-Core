<?php namespace Betasyntax;

use Betasyntax\Core\Application;
use Noodlehaus\Config as Conf;

class Config {
  public $conf;

  public function __construct() {
    if (defined('PHPUNIT_BETASYNTAX_TESTSUITE') == true) {
      $basepath = __dir__.'/config/';
    } else {
      $basepath = (string) app()->getBasePath().'/config/';
    }
    $this->conf['app'] = Conf::load($basepath.'app.php');
    $this->conf['db'] = Conf::load($basepath.'db.php');
    $this->conf['email'] = Conf::load($basepath.'email.php');
    $this->conf['mounts'] = Conf::load($basepath.'mounts.php');
    // dd($this->conf);
  }
}
