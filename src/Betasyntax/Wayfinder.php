<?php

namespace Betasyntax;

use App\Models\Menu;

class Wayfinder 
{
  private static $activePage;
  private static $data = array();
  private static $cnt = 0;

  public static function setSlug($slug) 
  {
    self::$activePage = $slug;
  }

  public static function getSlug() 
  {
    return self::$activePage;
  }
    
  public static function tree($elements, $parentId = 0)
  {
    $branch = array();
    foreach ($elements as $element) {
        if ($element['parent_id'] == $parentId) {
            $children = self::tree($elements, $element['id']);
            if ($children) {
                $element['children'] = $children;
            } else {
              $element['children'] = null;
            }
            $branch[] = $element;
        }
    }
    return $branch;
  }

  public static function menuArray($data) {
    if (count($data)>0) {
      foreach ($data as $key => $value) {
        $menuData[$value->id] = array(
          "id" => $value->id,  
          "parent_id" => $value->parent_id,  
          "name" => $value->title, 
          "url" => $value->url,
          "type" => $value->type,
          "status" => $value->status,
          "slug" => $value->slug
        );
      }
    }
    return $menuData;
  }

  public static function buildHtmlTree($menuArray,$ul_class='') { 
    if($ul_class!='') {
      $ul_class=' class="'.$ul_class.'"';
    }
    $html = '';
    $active = '';
    $opt = '';
    echo '<ul'.$ul_class.'>';
    foreach($menuArray as $menu) {
      if($menu['slug']==self::$activePage) {
        $active = ' class="active"';
      }
      if ($menu['type']=='external') {
        $url='#';
        $opt = 'onClick="window.open(\''.$menu['url'].'\')"';
      } else {
        $url=$menu['url'];
      }
      echo '<li'.$active.'>';
      $active = '';
      echo '<a href="'.$url.'" '.$opt.'>'.$menu['name'].'</a>';
      if(is_array($menu['children'])) {
        self::buildHtmlTree($menu['children']);
      }
      echo '</li>';
    }
    echo '</ul>';
  }
}
