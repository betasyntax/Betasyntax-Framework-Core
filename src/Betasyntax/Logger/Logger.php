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
      $app = app()->getInstance();
      $this->logger = new Monolog('app');
      try {
        $this->logger->pushHandler(new StreamHandler($app->getBasePath().'/../storage/logs/app.log', Monolog::DEBUG));
        $this->logger->pushHandler(new FirePHPHandler());
      } catch (Exception $e) {
      }
    }

    public function log($type, $text, $array = array()) 
    {
      $this->logger->$type($text, $array);
    }
}