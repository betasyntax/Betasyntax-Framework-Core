<?php 

use PHPUnit\Framework\TestCase;
use Betasyntax\Core\Application;

Class TestApplicationBoot extends TestCase
{  
  public $app;

  public function testCase()
  {
    require __DIR__.'/../../../../../vendor/autoload.php';
    $app = new Betasyntax\Core\Application(__DIR__.'/../../../../../');
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

  }
  // public function testServiceProvidersAreCorrectlyRegistered()
  //   {
  //       $provider = m::mock('Betasyntax\Core\Services\ServiceProvider');
  //       $class = get_class($provider);
  //       $provider->shouldReceive('register')->once();
  //       $app = new Application;
  //       $app->register($provider);
  //       $this->assertTrue(in_array($class, $app->getLoadedProviders()));
  //   }
}
