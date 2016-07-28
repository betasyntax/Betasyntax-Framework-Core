<?php namespace Betasyntax;

use Betasyntax\Authenticate;
use App\Models\Setting;

class Setup 
{

  public static function wizard($domain)
  {
    if($domain==app()->auth_domain) {
      if(app()->session->isLoggedIn!=0) {
        //check db for setup.
        $is_started = Setting::find_by(['key_name'=>'setup_first_run']);
        if(count($is_started)==1) {
          if($is_started->value=='0') {
            header("Location: /setup");
          }
        }

      }
    }
  }

}