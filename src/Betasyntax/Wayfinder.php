<?php

namespace Betasyntax;

use App\Models\Menu;

class Wayfinder 
{
  private static $activePage;
  private static $data = array();
  private static $cnt = 0;

  public static function getMenu($parent_id,$cat_id = 0)
  {
    $model = new Menu;
    $arrayCategories = array();
    $sql = "SELECT * FROM menus WHERE status = 'enabled' AND menu_category_id = ".$cat_id." ORDER BY site_order;";
    $data = $model->raw($sql);
    if (count($data)>0) {
      foreach ($data as $key => $value) {
        $arrayCategories[$value->id] = array(
          "parent_id" => $value->parent_id, 
          "name" => $value->title, 
          "url" => $value->url,
          "type" => $value->type,
          "status" => $value->status,
          "slug" => $value->slug
        );
      }
    }
    return $arrayCategories;
  }

  public static function _setSlug($slug) 
  {
    self::$activePage = $slug;
  }

  public static function _getSlug() 
  {
    return self::$activePage;
  }
    
  public static function createTreeView($array, $currentParent, $running = false,$id='mainmenu', $currLevel = 0, $prevLevel = -1 ) {
    if($running) {
      self::$cnt=0;
    }
    foreach ($array as $categoryId => $category) {
      $active = '';
      $opt = '';
      if ($currentParent == $category['parent_id']) {  
        if (self::$activePage == $category['slug'])
          $active=' class="active"';
        if ($category['type']=='external') {
          $url='#';
          $opt = 'onClick="window.open(\''.$category['url'].'\')"';
        } else {
          $url='/'.$category['url'];
        }
        if (self::$cnt==0 ) {
          echo '<ul class='.$id.'>'; 
        }          
        if ($currLevel > $prevLevel) {
          if(self::$cnt !=0)
            echo "<ul>"; 
        }

        if ($currLevel == $prevLevel) echo " </li> ";

        echo '<li'.$active.'>';
        echo '<a href="'.$url.'" '.$opt.'>'.$category['name'].'</a>';

        if ($currLevel > $prevLevel) { 
          $prevLevel = $currLevel; 
        }
        $currLevel++; 
        self::$cnt++;
        self::createTreeView ($array, $categoryId, false, $id, $currLevel, $prevLevel);
        $currLevel--;
      }   
    }
    if ($currLevel == $prevLevel) 
      echo " </li>  </ul> ";
  }
}
