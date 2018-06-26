<?php

 class Session extends Model {

  static public function CreateNew( $user ) {
   if ( false_or_null($user) ) return NULL;
   global $auth_database,$session_id,$session,$session_token;
   $m=new Session($auth_database);
   $m->Delete(array("r_User"=>$user['ID']));
   $new_id=$m->Insert(array(
    'r_User'        => $user['ID'],
    'expiresAt'     => ($expiry=strtotime('now +30 minutes'))
   ));
   $session_token=md5(uniqid($new_id,true));
   $result=$m->Update(array(
    'session_token' => $session_token
   ), array("ID"=>$new_id));
   $session_id=$new_id;
   return $new_id;
  }

  static public function Refresh() {
   global $auth_database,$session,$session_token,$session_id;
   if ( false_or_null($session) ) return NULL;
   $m=new Session($auth_database);
   if ( strtotime('now') >= intval($session['expiresAt']) ) return FALSE;
   $m->Update(array('expiresAt'=>strtotime('now +30 minutes')),array('ID'=>$session_id));
   return TRUE;
  }

  static public function FindByUser( $r_User ) {
   global $database;
   $m=new Session($database);
   $sessions=$m->Select(array('r_User'=>$r_User));
   if ( false_or_null($sessions) ) return NULL;
   if ( count($sessions) === 0 ) return NULL;
   return array_shift($sessions);
  }

  static public function Recall( $session_token ) {
   global $auth_database;
   $m=new Session($auth_database);
   return $m->First("session_token",$session_token);
  }

  static public function Logout( $session_token ) {
   global $auth_database;
   $m=new Session($auth_database);
   $result=$m->First("session_token",$session_token);
   if ( false_or_null($result) ) return FALSE;
   $m->Delete(array("session_token"=>$session_token));
   return TRUE;
  }

 };
