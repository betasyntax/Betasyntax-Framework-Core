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
  private static $status = 1; // on by default

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
      WHERE node.lft BETWEEN parent.lft AND parent.rgt AND parent.menu_id = $menu_id AND node.menu_id = $menu_id 
      GROUP BY node.title, node.id 
      ORDER BY node.lft;      
    ";

    $menu = new Menu;
    $data = array();
    $tree = $menu->exec($sql);

    $result        = '';
    $currDepth     = 0; 
    $prevDepth     = 0; 
    $lastNodeIndex = count($tree) - 1;
    $cnt = 0;
    $hidden = false;
    $hiddenDepth = NULL;
    foreach ($tree as $index => $currNode) {
      if($currNode->status=='root') {
        continue;
      }
      // Always open a node
      if($currNode->depth == 0 || $currNode->depth == $hiddenDepth) {
        $hidden = false;
        $hiddenDepth = NULL;
      }
      if($hiddenDepth != $currNode->depth && $hidden) {
        $currDepth = $currNode->depth;
        if($currDepth > $hiddenDepth)
          continue;
      }
      $t = ($index == 0) ? 1 : 2;
      if ($slug == $currNode->slug) {
        $active = 'active';
      } else {
        $active = '';
      }
      $target = '';
      if($currNode->type=='external') {
        $target = 'target="_blank"';
      }
      if ($currNode->depth > $currDepth || $index == 0) {
          if ($cnt == 0) {
            $result .= '<ul class="' . $ul_class . '" data-id="'.$currNode->id.'">';
          } else {
            // if(self::$status==1 && $currNode->hidden == 0)
            $result .= '<ul data-id="'.$currNode->id.' '.$hiddenDepth.'">';
          }
          $cnt++;
      }
      // Level up?
      if ($currNode->depth < $currDepth) {
        $cnt--;
        if(!$hidden && $hiddenDepth == NULL && $currDepth != 0) {
          $result .= str_repeat('</ul></li>', $currDepth - $currNode->depth);
        }
      }
      if(self::$status==1 && $currNode->hidden == 0 && $currNode->status == 'enabled') {
        $result .= '<li data-id="'.$currNode->id.' '.$hiddenDepth.'"><a href="'.$currNode->url.'" class="'.$active.'" '.$target.'>'.$currNode->title.'</a>';
        // Check if there's chidren
        if ($index != $lastNodeIndex && $tree[$index + 1]->depth <= $tree[$index]->depth) {
            $result .= '</li>'; // If not, close the <li>
        }
      }
      // Adjust current depth
      $currDepth = $currNode->depth;
      // Are we finished?
      if ($index == $lastNodeIndex) {
        $result .='</ul>';
      }
    }
    echo $result;
  }

  public static function buildAdminHtmlTree($slug,$menu_id,$ul_class='') {
    self::setSlug($slug);
    self::setMenuId($menu_id);
    self::setUlClass($ul_class);
    $sql = "
      SELECT node.*,node.title, (COUNT(parent.title) - 1) AS depth
      FROM menus AS node,
        menus AS parent
      WHERE node.lft BETWEEN parent.lft AND parent.rgt AND parent.menu_id = $menu_id AND node.menu_id = $menu_id  
      GROUP BY node.title, node.id 
      ORDER BY node.lft;      
    ";
    $menu = new Menu;
    $data = array();
    $tree = $menu->exec($sql);
    // Bootstrap loop
    $result        = '';
    $currDepth     = 0; 
    $lastNodeIndex = count($tree) - 1;
    // Start the loop
    $cnt = 1;
    foreach ($tree as $index => $currNode) {
      if($currNode->status=='root') {
        continue;
      }
      // Level down? (or the first)
      if ($currNode->depth > $currDepth || $index == 0) {
          if ($cnt == 0) {
            $result .= '<ol class="dd-list">';
          } else {
            $result .= '<ol class="dd-list">';
          }
      }
      // Level up?
      if ($currNode->depth < $currDepth) {
          $result .= str_repeat('</ol></li>', $currDepth - $currNode->depth);
      }
      // Always open a node
      $t = ($index == 0) ? 1 : 2;
      if ($slug == $currNode->slug) {
        $active = 'active';
      } else {
        $active = '';
      }
      $col = 'status_en';
      $italics = '';
      $bold = '';
      $status = 'check-circle';
      $hidden = '';
      if($currNode->hidden==1) {
        $hidden = '-slash';
      }
      if($currNode->status=='disabled') {
        $col = 'status_ds';
        $bold = ' bold';
        // $status = 'times-circle';
        $status = 'ban';

      }
      if($currNode->hidden==1 && $currNode->is_root==0) {
        $italics = ' italics';
      }
      if($currNode->is_root==1) {
        $col = '';
      }
      $url = $currNode->url;
      if($url=='NULL') {
        $url=='';
      }
      $type = '';
      if($currNode->type=='internal') {
        $type = '/';
      }
      $result .= '<li class="dd-item" data-title="'.$currNode->title.'" data-status="'.$currNode->status.'" data-hidden="'.$currNode->hidden.'" data-id="'.$currNode->id.'" ';
      $result .= 'data-lft="'.$currNode->lft.'" data-rgt="'.$currNode->rgt.'" data-depth="'.$currNode->depth.'">';
      $result .= '<div class="dd-handle dd3-handle">Drag</div>';
      $result .= '<div class="dd3-content'.$bold.$italics.'"><span id="node-'.$currNode->id.'" class="menu-title-txt">'.$currNode->title.'</span>';
      $result .= '<span id="node-input-'.$currNode->id.'" class="menu-title"><input id="title-'.$currNode->id.'" value="'.$currNode->title.'" />';
      $result .= '<div class="checkmark fa fa-check"></div><div class="cancel fa fa-times"></div></span>';
      $result .= '<span id="menu-url-'.$currNode->id.'" class="menu-url"> ('.$url.')</span>';
      $result .= '<span id="node-url-'.$currNode->id.'" class="menu-url-text"><input id="url-'.$currNode->id.'" value="'.$url.'" />';
      $result .= '<div class="checkmark1 fa fa-check"></div><div class="cancel1 fa fa-times"></div></span>';
      $result .= '<div class="edit-options fa fa-pencil"></div>';
      $result .= '<div class="hidden-options fa fa-eye'.$hidden.'"></div>';
      $result .= '<div class="status-options fa fa-'.$status.'"></div></div>';
      // Check if there's chidren
      if ($index != $lastNodeIndex && $tree[$index + 1]->depth <= $tree[$index]->depth) {
          $result .= '</li>'; // If not, close the <li>
      }
      // Adjust current depth
      $currDepth = $currNode->depth;
      // Are we finished?
      if ($index == $lastNodeIndex) {
          $result .= '</ol>' . str_repeat('</li></ol>', $currDepth);
          $last_id = $currNode->id;
      }
      $cnt++;
    }
    echo $result;
  }

  public static function tree($elements, $parentId = 0)
  {
    $branch = array();
    echo count($elements);
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

  static function pathFinder($slug) {
    $sql = "
      SELECT parent.title,parent.url 
      FROM menus AS node,
              menus AS parent
      WHERE node.lft BETWEEN parent.lft AND parent.rgt
              AND node.slug = '$slug'
      ORDER BY parent.lft;     
    ";

    $menu = new Menu;
    $data = array();
    $tree = $menu->exec($sql);
    
    $cnt = count($tree);
    $cnts = 1;
    echo '<div id="breadcrumbs">';
    foreach ($tree as $index => $currNode) {
      if($cnts == $cnt) {
        if($cnts > 1)
          echo $currNode->title;
      } else {
        if($cnts >= 1)
          echo '<a href="'.$currNode->url.'">'.$currNode->title.'</a> \ ';
      }
      $cnts++;
    }
    echo '</div>';
  }
}
