<?php 

use PHPUnit\Framework\TestCase;
use Betasyntax\Core\Application;

Class ApplicationTest extends TestCase
{  
  public $app;
  public $path;

  public function setUp(){
    // @session_start();
    $this->path = __DIR__.'/../../../../';
    parent::setUp();
  }

  /**
   * @runInSeparateProcess
   */
  public function testCase()
  {
    $this->app = new Application($this->path);
    $this->assertInstanceOf(Application::class, $this->app);
    $this->assertObjectHasAttribute('router', $this->app);
    $this->assertObjectHasAttribute('version', $this->app);
    $this->assertObjectHasAttribute('basePath', $this->app);
    $this->assertObjectHasAttribute('instance', $this->app);
    $this->assertObjectHasAttribute('view', $this->app);
    $this->assertObjectHasAttribute('router', $this->app);
    $this->assertObjectHasAttribute('config', $this->app);
    $this->assertObjectHasAttribute('session', $this->app);
    $this->assertObjectHasAttribute('flash', $this->app);
  }

  public function testApplicationIsSingleton()
  {
    $container = new Application($this->path);
    $this->assertSame($container, Application::getInstance());

    $container2 = new Application($this->path);
    $this->assertInstanceOf(Application::class, $container2);

    $this->assertNotSame($container, $container2);
  }

  public function testCanAddProperty()
  {
    $this->app = new Application($this->path);
    $this->app->testAttribute = 'foo';
    $this->assertEquals('foo',$this->app->testAttribute);
  }

  public function testEnvironmentIsSet()
  {
    $this->app = new Application($this->path);
    $this->assertEquals('local',$this->app->env['env']);
  }

  public function testRouterDispatcher()
  {
    $app = new Application($this->path);
    $url = '/welcome';
    $method = "GET";
    $this->assertFalse($app->router->dispatch($app,$url,$method));
  }
}
