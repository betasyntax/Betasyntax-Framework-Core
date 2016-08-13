<?php namespace Betasyntax\DebugBar;

Class DebugBar
{
  protected static $instance;
  protected static $debugbar;
  protected static $jsRender;
  protected static $stack;

  public function __construct() {
    if(static::$instance==null) {
      static::$instance=$this;
    }
    static::$debugbar = new \DebugBar\StandardDebugBar(); 
    $this->jsRender('/debug');
    $this->addMessage('DebugBar Loaded');
  }

  static function getInstance()
  {
    return static::$instance;
  }

  public function init()
  {
  }
  public function jsRender()
  {
    return static::$jsRender = static::$debugbar->getJavascriptRenderer('/debug');
  }
  public function addMessage($message)
  {
    // $this->getInstance();
    static::$stack[] = $message;
  }
  public function getJsRender()
  {
    return static::$jsRender;
  }

  public function render()
  {
    foreach (static::$stack as $message) {
      static::$debugbar["messages"]->addMessage($message);
    }
    return static::$jsRender->render();
  }
}