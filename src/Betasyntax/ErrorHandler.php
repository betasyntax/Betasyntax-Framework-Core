<?php namespace Betasyntax;

Class ErrorHandler
{
  public function __construct()
  {    
    error_reporting(E_ALL);
    ini_set('display_errors', 1);  }
}
  
