<?php namespace Betasyntax\Core\Interfaces\View;

use Betasyntax\Core\Application;

Interface ViewInterface
{
  public function __construct(Application $app);
  public function render($view,$data);
  public function loadHelpers();
}