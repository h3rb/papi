<?php

function startsWith($haystack, $needle){     $length = strlen($needle);     return (substr($haystack, 0, $length) === $needle);}
function is_ssl() {
return true;
// uncomment the next line and comment out the line above to properly check for ssl, requires a cert
// if ( isset($_SERVER['HTTPS']) ) { if ( 'on' == strtolower($_SERVER['HTTPS']) ) return true; if ( '1' == $_SERVER['HTTPS'] ) return true; } elseif ( isset($_SERVER['SERVER_PORT']) && ( 443 == intval($_SERVER['SERVER_PORT']) ) ) return true; return false;
}
if ( !is_ssl() ) { // The following block is used to restrict access to the insecure version, and bump users to the secure one.
 if (  !startsWith($_SERVER["REMOTE_ADDR"],"127.")
    && !startsWith($_SERVER["REMOTE_ADDR"],"52.")
    && !startsWith($_SERVER["REMOTE_ADDR"],"172.") ) {
  echo 'Access denied to '.$_SERVER['REMOTE_ADDR']; die;
  header("Location: https://api.mydomain.com"); die;
 }
}

 global $plog_level; $plog_level=1;
 include 'core/Page.php';

 global $TODAY;
 $TODAY = strtotime('now');

 function RemoveKeys( $in, $rem ) { // Filters unwanted tags from incoming database-modifying statements
  $out=array();
  foreach ( $in as $keyed=>$value ) {
   if ( is_array($rem) ) {
    $found=FALSE;
    foreach($rem as $v) if ( $keyed===$v ) $found=TRUE;
    if ( $found ) continue;
   } else if ( $keyed === $rem ) continue;
   $out[$keyed]=$value;
  }
  return $out;
 }

 function OnlyKeys( $in, $rem ) { // Permits on a certain set of keys for database-modifying statements
  $out=array();
  foreach ( $in as $keyed=>$value ) {
   if ( is_array($rem) ) {
    $found=FALSE;
    foreach($rem as $v) if ( $keyed!==$v ) $found=TRUE;
    if ( !$found ) continue;
   } else if ( $keyed !== $rem ) continue;
   $out[$keyed]=$value;
  }
  return $out;
 }

 $g=getpost();

 //var_dump($g);

 if ( !isset($g["data"]) ) Auth::EndTransmit("Nothing was sent or malformed request.",-1,$g);

 $j=json_decode($g["data"],true);
 if ( is_null($j) ) Auth::EndTransmit("JSON from post 'data' was malformed.",102,$g);

 if ( !isset($j["action"]) ) Auth::EndTransmit("JSON 'action' was not specified.");

 $action = $j["action"];

 $json=array("result"=>"success");

 global $headers,$session_id,$session,$session_token,$database,$auth,$user,$is_admin;
 switch ( $action ) {

  case "identify":
    $json=array(
      "host"=>$_SERVER["HTTP_HOST"],
      "method"=>$_SERVER["REQUEST_METHOD"],
      "post"=>$_POST,
      "headers"=>$headers,
      "session_id"=>$session_id,
      "session"=>$session,
      "admin"=>$is_admin
    );
   break;

  case "login":
    if ( !isset($j["username"])
      || !isset($j["password"]) ) Auth::EndTransmit("Not enough parameters for action 'login'",102,$g);
    $un=$j["username"];
    $pw=$j["password"];
    $session_id=Auth::Login($un,$pw);
    if ( $session_id === FALSE ) Auth::EndTransmit("Could not log in, no password set, check for password reset email",102);
    if ( $session_id === NULL ) Auth::EndTransmit("Invalid username/password.",102);
    $json=array("result"=>$auth,"session"=>$session_token,"admin"=>$is_admin);
   break;

  case "logout":
    if ( is_null($session) ) Auth::EndTransmit("Not logged in",-1);
    if ( Auth::Logout($session) ) Auth::EndTransmit("Logged out.",1);
    else Auth::EndTransmit("Session had expired.",-1);
   break;

  case "forgot":
    if ( !isset($j["email"]) ) Auth::EndTransmit("Not enough parameters for action 'forgot'",102);
    $em=$j["email"];
    $user=User::FindByEmail($em);
    if ( false_or_null($user) ) Auth::EndTransmit("No such user with that email address.",102);
    $result=Auth::Forgot($user);
    $json=array("result"=>"success","message"=>"Forgot email sent.","values"=>$result);
   break;

  case "me":
    if ( is_null($session) || is_null($auth) ) Auth::EndTransmit("Not logged in",-1);
    $json=array( "result"=>array(
     "user"=>RemoveKeys($auth,array("password","forgotKey","forgotExpires")),
    ));
   break;

  case "users":
    if ( is_null($session) ) Auth::EndTransmit("Not logged in",-1);
    $result=User::GetUsers($auth);
    if ( false_or_null($result) ) Auth::EndTransmit("Nothing found",0);
   break;

  case "user":
    if ( is_null($session) ) Auth::EndTransmit("Not logged in",-1);
    if ( isset($j["create"]) ) {
     $filter=array( "firstname", "lastname", "company", "username", "password" );
     $filtered=OnlyKeys($j["create"],$filter);
     $result=User::CreateNew($co,$filtered);
     $json=array("result"=>$result);
    } else if ( isset($j["update"]) ) {
     $filter=array( "firstname", "lastname", "company", "username", "password", "email" );
     $userId=$j["ID"];
     if ( false_or_null(User::UpdateProtected($auth,$userId,$filtered)) ) Auth::EndTransmit("Could not complete action 'update' on 'user'",102,$g);
     $result="success";
    } else if ( isset($j["get"]) ) {
     $result=User::FindByID($auth,$j["get"]);
     if ( false_or_null($result) ) Auth::EndTransmit("Nothing found",0);
    } else if ( isset($j["delete"]) ) {
     $result=User::DeleteByID($auth,$j["delete"]);
     if ( false_or_null($result) ) Auth::EndTransmit("Nothing found",0);
    }
    if ( false_or_null($result) ) Auth::EndTransmit("Nothing found",0);
    $json=array("result"=>$result);
   break;

  case "datetime":
    $json["result"]=array( "currentTime"=>array("__Type:"=>"Date","iso"=>$TODAY) );
   break;

  default: Auth::EndTransmit("No action requested",-1); break;

 }

echo json_encode($json);
