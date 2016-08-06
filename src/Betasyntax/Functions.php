<?php
namespace Betasyntax;

class Functions
{ 

  public static function dd()
  {
    $args = func_get_args();
    echo '<pre>';
    call_user_func_array('var_dump', $args);
    echo '</pre>';
  }

  public static function strpos_array($haystack, $needles) 
  {
    if (is_array($needles)) {
      foreach ($needles as $str) {
        if (is_array($str)) {
          $pos = strpos_array($haystack, $str);
        } else {
          $pos = strpos($haystack, $str);
        }
        if ($pos !== FALSE) {
          return TRUE;
        }
      }
    } else {
      return strpos($haystack, $needles);
    }
  }
}
