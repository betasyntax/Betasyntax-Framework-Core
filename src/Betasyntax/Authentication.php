<?php namespace Betasyntax;

use App\Models\User;

Class Authentication
{
  /*
  **
  *** The __constructor method will run when the class is first created
  *** Please not that in the constructor it should take 0 args if posible
  **
  */
  public function __construct(){
    if(!isset(app()->session->isLoggedIn))
      app()->session->isLoggedIn=0;
  }

  public function isLoggedIn() {
    return app()->session->isLoggedIn;
  }

  public function authenticate($req) {
    $user = User::find_by(['email'=>$req['email'],'status'=>'enabled'],1);
    if(count($user)==1) {
      if(isset($user->email)){
        if (password_verify($req['password'], $user->password)) {
          return true;
        }
      }
    }    
  }

  public static function domain($domain)
  { 
    return (empty($domain))?:'web';
  }

  public static function secure($domain)
  {
    if($domain=='admin') {
      if(app()->session->isLoggedIn==0) {
        header('Location: '.app()->loginUrl);
      }
    } 
  }
}
