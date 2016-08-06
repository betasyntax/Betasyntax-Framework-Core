<?php namespace Betasyntax\View;

use MtHaml\Environment as HamlEnv;
use MtHaml\Support\Twig\Loader as HamlLoader;
use MtHaml\Support\Twig\Extension as HamlExt;

Class View
{
  protected $twig;
  public function __construct(Application $app)
  {

    $haml = new HamlEnv('twig');
    $twig = new \Twig_Loader_Filesystem(array($app->basePath.'app/Views/'));
    $hamll = new HamlLoader($haml, $twig);

    $this->twig = new Twig_Environment($hamll);
    $this->twig->addExtension(new HamlExt());
  }

  public function test() {
    echo $this->twig;
  }
}