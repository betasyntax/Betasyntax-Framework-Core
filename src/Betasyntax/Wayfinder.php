<?php

namespace Betasyntax;

use App\Models\Menu;

class Wayfinder 
{
  private static $activePage;
  //default parent id is 1 (home). You also need to set a 
  private static $data = array();
  private static $cnt = 0;

  public static function getMenu($parent_id)
  {
    $sql = "SELECT * FROM menus WHERE parent_id = ".$parent_id." AND status = 'enabled' ORDER BY site_order;";
    self::$cnt++;
    return Menu::raw($sql);
  }

  public static function _setSlug($slug) 
  {
    self::$activePage = $slug;
  }

  public static function _getSlug() 
  {
    return self::$activePage;
  }
  
  public static function tree($parent_id) 
  {
    $result = self::getMenu($parent_id);
    if(self::$cnt==1) {
      echo '<ul class="mainmenu">';
    } else {
      echo '<ul>';
    }
    $counter = 0;
    $row_total = count($result);
    for ($row=0; $row < $row_total; $row++) { 
      $t = self::getMenu($result[$row]->id);
      $active = '';
      $opt = '';
      if (self::$activePage == $result[$row]->slug)
        $active=' class="active"';
      if ($result[$row]->type=='external') {
        $url='#';
        $opt = 'onClick="window.open(\''.$result[$row]->url.'\')"';
      } else {
        $url='/'.$result[$row]->url;
      }
      echo '<li'.$active.'><a href="'.$url.'" '.$opt.'>'.$result[$row]->title.'</a>';
      if (count($t)>0){
        echo '<ul>';
          for ($row2=0; $row2 < count($t); $row2++) { 
            $z=$t;
            if (is_array($t)) {
              $z=$t[$row2];
            }
            if (self::$activePage == $z->slug)
              $active=' class="active"';
            if ($z->type=='external') {
              $url2='#';
              $opt = 'onClick="window.open("'.$z->url.'")"';
            } else {
              // var_dump($z->url);
              if(substr( $z->url, 0, 1 ) === "\/") {
                $url2=$z->url;
              } else {
                $url2='/'.$z->url;
              }
            }
            echo '<li'.$active.'><a href="'.$url2.'" '.$opt.'>'.$z->title.'</a></li>';
            // self::tree($row2->id);
          }
        echo '</ul>';
      }
      echo '</li>';
      if ($counter == ($row_total - 1)) {
        if (app()->session->isLoggedIn==1) {
          echo '<li class="auth"><a href="/logout">Logout</a></li>';
        } else {
          echo '<li class="auth"><a href="/login">Login</a></li>';
          echo '<li class="auth"><a href="/signup">Sign Up</a></li>';
        }
      }
      $counter++;
    }
    echo '</ul>';
  }
}
