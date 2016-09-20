<?php 

use PHPUnit\Framework\TestCase;
use Betasyntax\Core\Application;

Class MiddlewareTest extends TestCase
{  
  public $path;

  public function setUp(){
    // @session_start();
    $this->path = __DIR__.'/../../../../../';
    parent::setUp();
  }

  public function testCase()
  {
  }
}
