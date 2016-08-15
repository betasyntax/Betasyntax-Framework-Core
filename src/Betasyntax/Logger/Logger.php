<?php namespace Betasyntax\Logger;

use Exception;
use Monolog\Logger as Monolog;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;
// use Betasyntax\Core\MountManager\MountManager;

class Logger
{
    protected $app;
    protected $logger;

    public function __construct()
    {
      $local_log_error = "Log file storage/logs/app.log is either not present or you have the wrong permissions set.";
      $app = app();
      $this->logger = new Monolog('app');
      try {
        $manager = app()->mountManager->getManager();
        if($manager->has('local_log://app.log')) {
          $this->logger->pushHandler(new StreamHandler($app->getBasePath().'/storage/logs/app.log', Monolog::DEBUG));
          $this->logger->pushHandler(new FirePHPHandler());
        } else {
          if(!$app->isProd()) {
            flash()->error($local_log_error);
          } else {
            error_log($local_log_error);
          }
        }
      } catch (Exception $e) {
        $debugbar = app()->debugbar;
        $debugbar::$debugbar['exceptions']->addException($e);
      }
    }

    public function log($type, $text, $array = array()) 
    {
      try {
        $this->logger->$type($text, $array);
      } catch (Exception $e) {
        $app = app()->debugbar;
        $app::$debugbar['exceptions']->addException($e);
      }
    }
}