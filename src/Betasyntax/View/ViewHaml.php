<?php namespace Betasyntax\View;

use Betasyntax\Core\Application;
use MtHaml\Environment as HamlEnv;
use MtHaml\Support\Twig\Loader as HamlLoader;
use MtHaml\Support\Twig\Extension as HamlExt;
use Betasyntax\Core\Interfaces\View\ViewInterface;
use Betasyntax\Wayfinder;
use Betasyntax\Core\View\ViewProvider;

Class ViewHaml extends ViewProvider
{
  public $twig;
  protected $basePath;
  protected $app;

  public function __construct()
  {
    $path = app()->getBasePath().'/app/Views/';
    $haml = new HamlEnv('twig');
    $twigLoader = new \Twig_Loader_Filesystem(array($path));
    $hamll = new HamlLoader($haml, $twigLoader);
    $this->twig = new \Twig_Environment($hamll);

    $this->twig->clearCacheFiles();
    $this->twig->addExtension(new HamlExt());

    $this->loadHelpers();
    $this->loadLocalHelpers();
  }
}