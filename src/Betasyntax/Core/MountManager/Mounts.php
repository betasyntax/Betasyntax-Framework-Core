<?php namespace Betasyntax\Core\MountManager;

use League\Flysystem\Filesystem;
use League\Flysystem\MountManager;
use League\Flysystem\Adapter\Local;

Class Mounts
{
  protected $app;
  protected $local;
  protected $manager;
  protected $mounts;

  public function __construct()
  {
    $this->app = app();
    $localAdapter = new Local($this->app->getBasePath());
    $configAdapter = new Local($this->app->getBasePath().'/config/');
    $logAdapter = new Local($this->app->getBasePath().'/storage/logs/', [
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

    $coreMounts = [
      'local' => $local,
      'local_config' => $local_config,
      'local_log' => $local_storage
    ];

    $userMounts = $this->getUserMounts();
    $this->mounts = [];
    foreach ($userMounts as $key => $value) {
      $name = $key;
      foreach ($value as $key => $val) {
        if($key=='type')
          $type = $val;
        if($key=='mount')
          $mount = $val;
        if($key='=file') {
          if(count($val)>0) {
            $file = $val;
          }
        }
        if($key=='dir') {
          if(count($val)>0) {
            $dir = $val;
          }
        }
      }
      $this->mounts[$type.'_'.$name] = new Filesystem(new Local($this->app->getBasePath().$mount));
    }
    $allMounts = array_merge($coreMounts, $this->mounts);
    $this->manager = new MountManager($allMounts);
  }

  private function getUserMounts()
  {
    return config('mounts','filesystems');
  }

  public function getManager()
  {
    return $this->manager;
  }
}
