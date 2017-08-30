<?php

namespace Betasyntax;

use App\Models\Menu;

class Wayfinder 
{
  private static $active_page;
  private static $menu_id;
  private static $ul_class;
  private static $data = array();
  private static $cnt = 0;

  public static function setSlug($slug) 
  {
    self::$active_page = $slug;
  }

  public static function getSlug() 
  {
    return self::$active_page;
  }

  public static function setMenuId($menu_id) 
  {
    self::$active_page = $menu_id;
  }

  public static function getMenuId() 
  {
    return self::$active_page;
  }

  public static function setUlClass($ul_class) 
  {
    self::$ul_class = $ul_class;
  }

  public static function getUlClass() 
  {
    return self::$ul_class;
  }

  public static function buildHtmlTree($slug,$menu_id,$ul_class='') { 
    self::setSlug($slug);
    self::setMenuId($menu_id);
    self::setUlClass($ul_class);

    $sql = "
      SELECT node.*,node.title, (COUNT(parent.title) - 1) AS depth
      FROM menus AS node,
        menus AS parent
      WHERE node.lft BETWEEN parent.lft AND parent.rgt AND parent.menu_id = 1 AND node.menu_id = 1 AND node.status = 'enabled' 
      GROUP BY node.title, node.id 
      ORDER BY node.lft;      
    ";
  
    $menu = new Menu;
    $data = array();
    $tree = $menu->exec($sql);


    // dd($sql);
    // Bootstrap loop
    $result        = '';
    $currDepth     = 0; 
    $lastNodeIndex = count($tree) - 1;
    // Start the loop
    $cnt = 0;
    foreach ($tree as $index => $currNode) {
        if($currNode->status=='root') {
          continue;
        }
        // Level down? (or the first)
        if ($currNode->depth > $currDepth || $index == 0) {
            if ($cnt == 0) {
              $result .= '<ul class=' . $ul_class . '>';
              $cnt++;
            } else {
              $result .= '<ul>';
            }
        }
        // Level up?
        if ($currNode->depth < $currDepth) {
            $result .= str_repeat('</ul></li>', $currDepth - $currNode->depth);
        }
        // Always open a node
        $t = ($index == 0) ? 1 : 2;
        if ($slug == $currNode->slug) {
          $active = 'active';
        } else {
          $active = '';
        }
        $result .= '<li><a href="/' . $currNode->url . '" class="' . $active . '">' . $currNode->title . '</a>';
        // Check if there's chidren
        if ($index != $lastNodeIndex && $tree[$index + 1]->depth <= $tree[$index]->depth) {
            $result .= '</li>'; // If not, close the <li>
        }
        // Adjust current depth
        $currDepth = $currNode->depth;
        // Are we finished?
        if ($index == $lastNodeIndex) {
            $result .= '</ul>' . str_repeat('</li></ul>', $currDepth);
        }
    }
    echo $result;
  }

  public static function tree($elements, $parentId = 0)
  {
    $branch = array();
    echo count($elements);


    // var_dump($elements);
    foreach ($elements as $element) {

        // echo $element['id'].' ';
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
}
