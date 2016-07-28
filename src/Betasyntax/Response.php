<?php

namespace Betasyntax;

Class Response
{
  public function redirect($url) 
  {
    header('Location: '.$url);
  }

  public function render($url,$data) 
  {
    echo app()->twig->render($url,$data);
  }
}

