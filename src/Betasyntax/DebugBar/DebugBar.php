<?php namespace Betasyntax\DebugBar;

use ReflectionClass;

Class DebugBar
{
  protected static $instance;
  public static $debugbar;
  protected static $jsRender;
  protected static $stack;
  protected static $collectors = [];

  public function __construct() {
    if(static::$instance==null) {
      static::$instance=$this;
    }
    static::$debugbar = new \DebugBar\StandardDebugBar(); 
    $this->jsRender('/debug');
    static::$debugbar['time']->startMeasure('View', 'View operation');
  }

  static function getInstance()
  {
    return static::$instance;
  }

  public function jsRender()
  {
    // static::$debugbar["messages"]->addMessage('DebugBar Loaded:');
    static::$debugbar["messages"]->addMessage('Environment '.app()->env['env']);
    $x =  static::$debugbar->getJavascriptRenderer('/debug');
    return static::$jsRender = $x;
  }

  public function time($status,$name,$desc='')
  {
    if($status='start') {
      static::$debugbar['time']->startMeasure($name, $desc);
    } else {
      static::$debugbar['time']->stopMeasure($name);
    }
  }

  public function message($value,$tag='',$type = 'info', $object = null)
  {
    if($object==null && ($type=='addMessage'||$type=='info')) {
    //   $class='Debug: ';
    //   static::$stack[$type][] = $class.$value;
      if($tag!='') {
        $tag=$tag.': ';
        static::$stack[$type][] = $tag;
      }
      static::$stack[$type][] = $value;
    } elseif($object == null) {
      if($tag!='') {
        $tag=$tag.': ';
        static::$stack[$type][] = $tag;
      }
      static::$stack[$type][] = $value;
    } else {
      $class = get_class($object);
      // echo $class;
      $reflector = new ReflectionClass($class);
      $class = $reflector->getFileName().': ';
      static::$stack[$type][] = $class.$value;
    }
    
  }

  public function addCollector($object)
  {
    $class = get_class($object);
    if(!in_array($class,static::$collectors)) {
      static::$collectors[] = $class;
      static::$debugbar->addCollector($object);
    }
  }

  public function exception($value)
  {
    static::$debugbar['exceptions']->addException($value);
  }

  public function getJsRender()
  {
    $this->time('stop','View');
    $this->time('stop','Application');
    // static::$debugbar['time']->stopMeasure('View');
    // static::$debugbar['time']->stopMeasure('Application');
    return static::$jsRender;
  }

  public function render()
  {

    if(is_array(static::$stack)){
        foreach (static::$stack as $type => $value) {
          if(is_array($value)) {
            foreach ($value as $key => $val) {
              if($type=='info'||$type=='addMessage') {
                static::$debugbar["messages"]->{$type}($val);
              } elseif($type=='error') {
                static::$debugbar["messages"]->error($val);
              } elseif ($type=='warn') {
                // static::$debugbar["messages"]->warn($val);
              }
            }
          }
    
        }}
    return static::$jsRender->render();
  }
}