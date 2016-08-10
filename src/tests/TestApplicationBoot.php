<?php 

use PHPUnit\Framework\TestCase;
use Betasyntax\Core\Application;

Class TestApplicationBoot extends TestCase
{  
  public $app;

  /**
   * @runInSeparateProcess
   */
  public function testCase()
  {
    require __DIR__.'/../../../../../vendor/autoload.php';
    $app = new Betasyntax\Core\Application(__DIR__.'/../../../../../public');
    $this->assertInstanceOf(Application::class, $app);
    $this->assertObjectHasAttribute('router', $app);
    $this->assertObjectHasAttribute('version', $app);
    $this->assertObjectHasAttribute('basePath', $app);
    $this->assertObjectHasAttribute('instance', $app);
    $this->assertObjectHasAttribute('view', $app);
    $this->assertObjectHasAttribute('router', $app);
    $this->assertObjectHasAttribute('config', $app);
    $this->assertObjectHasAttribute('session', $app);
    $this->assertObjectHasAttribute('flash', $app);
    $this->assertObjectHasAttribute('auth', $app);
    $this->assertObjectHasAttribute('response', $app);
  }
}
