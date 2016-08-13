<?php namespace Betasyntax\Logger;

use Exception;
use Monolog\Logger as Monolog;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;

class Logger
{
    protected $app;
    protected $logger;

    public function __construct()
    {
      $this->app = app()->getInstance();
      $this->logger = new Monolog('app');
      try {
        $this->logger->pushHandler(new StreamHandler($this->app->getBasePath().'/../storage/logs/apsp.log', Monolog::DEBUG));
        $this->logger->pushHandler(new FirePHPHandler());
        $this->logger->addInfo('My logger is now ready');
      } catch (Exception $e) {
      }
    }

    public function __invoke(Request $request, Response $response, callable $next)
    {
        $this->__construct();

        return $next($request, $response);
    }


}