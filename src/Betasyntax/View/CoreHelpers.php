<?php namespace Betasyntax\View;
// use Betasyntax\Wayfinder;
//define your views

class CoreHelpers
{
  protected static $debugbarRender;
  protected static $app;
  protected static $debugbar;
  protected static $render;

  public static function helpers()
  {
    static::$app = app();

    $wayfinder = new \Twig_SimpleFunction('Wayfinder', function ($slug) {
      \Betasyntax\Wayfinder::_setSlug($slug);
      $data = \Betasyntax\Wayfinder::tree(0);
    });

    $brandingStatus = new \Twig_SimpleFunction('brandingStatus', function () {
      $x = Setting::search('key_name','=','show_branding',1);
      for($i=0;$i<count($x);$i++) {
        $s = $x->value;
      }
      return $s;
    });

    $link_to = new \Twig_SimpleFunction('link_to', function ($text, $route=['/'],$data=[],$attributes=[]) {
      $url = urlHelper($route,$data);
      if(isset($attributes)){
        $cnt='';
        foreach ($attributes as $key => $value) {
          $cnt .= $value[0].'="'.$value[1].'" ';
        } 
      }
      echo '<a href="'.$url.'" '.$cnt.'>'.$text.'</a>';
    });

    $link_to_remote = new \Twig_SimpleFunction('link_to_remote', function ($text, $url, $attributes) {
      if(isset($attributes)){
        $cnt='';
        foreach ($attributes as $key => $value) {
          $cnt .= $value[0].'="'.$value[1].'" ';
        } 
      }
      echo '<a href="'.$url.'" '.$cnt.'>'.$text.'</a>';
    });

    $debugBarHead = new \Twig_SimpleFunction('debugBarHead', function () {
      $app = app();
      if( ! $app->isProd()) {
        static::$debugbarRender = $app->debugbar;
        $test = static::$debugbarRender;
        static::$render = $test->getJsRender();
        echo static::$render->renderHead();
      } else {
        echo '';
      }
    });

    $debugBarBody = new \Twig_SimpleFunction('debugBarBody', function () {
      $app = app();
      if( ! $app->isProd()) {
        echo static::$debugbarRender->render();
      } else {
        echo '';
      }
    });

    $flash = new \Twig_SimpleFunction('flash', function () {
      echo flash()->display(null,false);
    });

    $dd = new \Twig_SimpleFunction('dd', function ($data) {
      dd($data);
    });

    return [
      'wayfinder'=>$wayfinder,
      'flash'=>$flash,
      'debugBarHead'=>$debugBarHead,
      'debugBarBody'=>$debugBarBody,
      'link_to'=>$link_to,
      'link_to_remote'=>$link_to_remote,
      'dd'=>$dd
    ];
  }
}
