<?php namespace Betasyntax\Core\MountManager;

use League\Flysystem\Filesystem;
use League\Flysystem\MountManager;
use League\Flysystem\Adapter\Local;

Class Mounts
{
  protected $app;
  protected $local;
  protected $manager;

  public function __construct()
  {
    $this->app = app();
    $localAdapter = new Local($this->app->getBasePath());
    $configAdapter = new Local($this->app->getBasePath().'/config/');
    $logAdapter = new Local($this->app->getBasePath().'/storage/logs/', LOCK_EX, Local::DISALLOW_LINKS, [
    'file' => [
        'public' => 0777,
        'private' => 0700,
    ],
    'dir' => [
        'public' => 0755,
        'private' => 0700,
      ]
    ]);

    $local = new Filesystem($localAdapter);
    $local_config = new Filesystem($configAdapter);
    $local_storage = new Filesystem($logAdapter);
    $this->manager = new MountManager([
      'local' => $local,
      'local_config' => $local_config,
      'local_log' => $local_storage
    ]);
  }
  public function getManager()
  {
    return $this->manager;
  }
}
