<?php namespace Betasyntax\View;

use Betasyntax\Core\Application;
use MtHaml\Environment as HamlEnv;
use MtHaml\Support\Twig\Loader as HamlLoader;
use MtHaml\Support\Twig\Extension as HamlExt;
use Betasyntax\Core\Interfaces\View\ViewInterface;
use Betasyntax\Wayfinder;

Class View
{
  public $twig;
  protected $basePath;
  protected $app;

  public function __construct()
  {
    $path = '/mnt/html/dev1/app/Views/';
    $twigLoader = new \Twig_Loader_Filesystem(array($path));
    $this->twig = new \Twig_Environment($twigLoader);
    $this->loadHelpers();
  }
}