<?php

 class User extends Model {

  static public function FindByEmail( $em ) {
   global $auth_database;
   $m=new User($auth_database);
   return $m->First('email',$em);
  }

  static public function FindByUsername( $un ) {
   global $auth_database;
//   var_dump($auth_database);
   $m=new User($auth_database);
   return $m->First('username',$un);
  }

  static public function FindByID( $who, $id ) {
   // whatever logic you want here, made simple for example
   global $auth_database;
   $m=new User($auth_database);
   return $m->Get($id);
  }

  static public function DeleteByID( $id ) {
   global $auth,$is_admin,$is_manager,$auth_database;
   $target=User::Get($id);
   if ( false_or_null($target) ) return FALSE;
   if ( !$is_admin ) return FALSE;
   global $m; $m=new User($auth_database); $m->Delete(array("ID"=>$target["ID"]));
   return TRUE;
  }

  static public function CreateNew( $un, $em, $pw, $co=NULL ) {
   global $auth_database;
   $m=new User($auth_database);
   if ( strlen(trim($pw)) < 8 ) return NULL;
   if ( strlen(trim($un)) < 4 ) return NULL;
   $user=User::FindByUsername($un);
   if ( !false_or_null($user) ) return FALSE;
   $user=User::FindByEmail($em);
   if ( !false_or_null($user) ) return FALSE;
   $filtered=array(
    'username'=>$un,
    'email'=>$em,
    'password'=>password_hash($pw,PASSWORD_DEFAULT)
   );
   $new_id=$m->Insert($filtered);
   return $m->Get($new_id);
  }

  static public function Forgotten($key) {
   global $auth_database;
   $m=new User($auth_database);
   $user=$m->First("forgotKey",$key);
   if ( !false_or_null($user)
     && intval($user['forgotExpires']) > strtotime('now') ) return $user;
   return FALSE;
  }

  static public function PasswordReset($key,$pw) {
   $user=User::Forgotten($key);
   if ( !false_or_null($user) ) {
    global $auth_database;
    $m=new User($auth_database);
    $m->Update(array(
     "forgotKey"=>"",
     "forgotExpires"=>0,
     "password"=>password_hash($pw,PASSWORD_DEFAULT)
    ),array("ID"=>$user['ID']));
    return TRUE;
   }
   return FALSE;
  }

 };
