<?php namespace Betasyntax\View;

use Betasyntax\Core\Application;
use MtHaml\Environment as HamlEnv;
use MtHaml\Support\Twig\Loader as HamlLoader;
use MtHaml\Support\Twig\Extension as HamlExt;
use Betasyntax\Core\Interfaces\View\ViewInterface;
use Betasyntax\Wayfinder;

Class ViewHaml
{
  public $twig;
  protected $basePath;
  protected $app;

  public function __construct()
  {
    $app = new Application;
    if($app->getInstance()!=NULL) {
      $this->app = $app::getInstance();
    } else {
      $this->app = $app;
    }
    $path = '/mnt/html/dev1/app/Views/';


    $haml = new HamlEnv('twig');

    $twigLoader = new \Twig_Loader_Filesystem(array($path));
    $hamll = new HamlLoader($haml, $twigLoader);
    $this->twig = new \Twig_Environment($hamll);

    $this->twig->addExtension(new HamlExt());
    $this->loadHelpers();
  }

  public function test() {
    echo $this->twig;
  }

  public function render($view,$data) {
    echo $this->twig->render($view,$data);
  }

  public function loadHelpers()
  {
    $wayfinder = new \Twig_SimpleFunction('Wayfinder', function ($slug) {
      Wayfinder::_setSlug($slug);
      $data = Wayfinder::tree(0);
    });
    $flash = new \Twig_SimpleFunction('flash', function () {
      echo flash()->display(null,false);
    });
    $this->twig->addFunction($flash);
    $this->twig->addFunction($wayfinder);
  }
}