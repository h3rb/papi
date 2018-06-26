<?php

 class Auth {

  public function CheckAPIKeys() {
   global $headers, $auth_database, $auth, $user, $session_id, $session, $session_token, $is_admin;
   $session_id=-1;
   $session=NULL;
   $is_admin=FALSE;
   $headers = apache_request_headers();
   if ( isset($headers['X-Papi-Application-Id']) && $headers['X-Papi-Application-Id']==stringval(MY_APP_ID) ) {
    if ( isset( $headers['X-Papi-Admin-Token'] ) && $headers['X-Papi-Admin-Token']==stringval(MY_ADMIN_TOKEN) ) {
     $is_admin &= TRUE;
    }
    if ( isset( $headers['X-Papi-Session-Token'] ) ) {
     $session_token=$headers['X-Papi-Session-Token'];
     $session=Session::Recall($session_token);
     $session_id=$session['ID'];
     if ( false_or_null($session) ) {
      Auth::EndTransmit("Session is Expired or Invalid",102);
     }
     $result=Session::Refresh();
     if ( $result === NULL ) {
      if ( !$is_admin ) Auth::EndTransmit("Session is Invalid",102);
     }
     if ( $result === FALSE ) {
      if ( !$is_admin ) Auth::EndTransmit("Session is Expired",101);
     }
     $m=new User($auth_database);
     $auth=$m->Get($session['r_User']);
     if ( false_or_null($auth) ) {
      if ( !$is_admin ) Auth::EndTransmit("User is not valid for Session",101);
     }
     $user=$auth;
     $is_admin=(intval($user['su'])>0)?TRUE:$is_admin;
    }
// var_dump($user);
// var_dump($is_admin);
// var_dump($headers);
// var_dump(stringval($headers['X-Papi-Admin-Token']));
// var_dump(strcmp($headers['X-Papi-Admin-Token'],MY_ADMIN_TOKEN));
// var_dump(MY_ADMIN_TOKEN);
    return TRUE;
   }
   Auth::EndTransmit("No API keys found");
  }

  static public function EndTransmit( $log_msg, $parse_code=-1, $values=NULL ) {
   plog("Bad Request 400 Sent - $log_msg");
   http_response_code(400);
   header("HTTP/1.0 400 Bad Request");
   if ( is_array($values) )
   echo json_encode( array(
     "code"=>$parse_code,
     "message"=>$log_msg,
     "values"=>$values
    )
   );
   else echo json_encode( array(
     "code"=>$parse_code,
     "message"=>$log_msg
    )
   );
   die;
  }

  static public function IsAdministrator() {
   global $is_admin;
   return $is_admin;
  }

  static public function SetPassword( $user, $new_password ) {
   if ( false_or_null($user) ) return NULL;
   if ( strlen(trim($new_password)) < 8 ) return FALSE;
   global $auth_database;
   $m=new User($auth_database);
   $m->Update(
    array('password'=>password_hash($new_password,PASSWORD_DEFAULT)),
    array('ID'=>$user['ID'])
   );
   return TRUE;
  }

  static public function Login( $un, $pw ) {
   global $is_admin;
   $user=User::FindByUsername($un);
//   var_dump($user);
   if ( false_or_null($user) ) return NULL;
   if ( $is_admin ) return Session::CreateNew($user);
   if ( isset($user['password']) && strlen(trim($user['password'])) === 0 ) {
    Auth::Forgot($user['ID']);
    return FALSE;
   }
   if ( !password_verify($pw,$user['password']) ) return FALSE;
   if ( intval($user['su']) > 0 ) $is_admin=TRUE;
   return Session::CreateNew($user);
  }

  static public function ACL( $required ) {
   global $auth;
   if ( !is_array($auth) ) return FALSE;
   if ( !isset($auth['acl']) ) return FALSE;
   plog('Checking ACL: '.(is_array($required)?implode(',',$required):$required));
   return ACL::has($auth['acl'],$required);
  }

  static public function Forgot( $user ) {
   global $auth_database;
   $forgotExpires=strtotime('now +1 hour');
   $forgotKey=b64k_encode(md5(uniqid($user['username'].$forgotExpires,true)));
   $m=new User($auth_database);
   $m->Update(array(
    "forgotExpires"=>$forgotExpires,
    "forgotKey"=>$forgotKey
    ),array( "ID"=>$user['ID'] ));
   $msg="You have indicated that you have forgotten your password, or your password was reset. "
       ."Please click the following link to set your password, and verify your email: ".PHP_EOL.PHP_EOL
       .site."forgot?email=".urlencode($user['email'])."&key=".urlencode($forgotKey).PHP_EOL.PHP_EOL
       ."Please note this chance to reset your password will expire in one hour. "
       ." If it expires, this link with regenerate a new email with an active password reset link.".PHP_EOL.PHP_EOL
       ."Thank you.".PHP_EOL
       ;
   mail($user['email'],"PAPI: Forgot Password",$msg);
   return $msg;
  }

  static public function Logout( $user ) {
   global $session_token;
   return Session::Logout($session_token);
  }

 };
