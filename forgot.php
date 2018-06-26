<?php

 global $suppress_auth; $suppress_auth=1;
 include 'core/Page.php';

 $g=getpost();
 $g['email']=urldecode($g['email']);
 $g['key']=urldecode($g['key']);

 $pw=NULL;
 $error=false;
 $mismatched=false;
 $key_error=false;
 $success=false;
 $db_error=false;
 if ( isset($g['newpassword']) ) {
  $pw=$g['newpassword'];
  if ( strcmp($pw,$g['repnewpassword']) !== 0 ) $mismatched=true; // Passwords didn't match
  else if ( strlen($g['newpassword']) < 8 ) $error=true;
  else {
   $user=_User::Forgotten($g['key']);
   if ( !false_or_null($user) ) {
    if ( _User::PasswordReset($g['key'],$pw) == FALSE ) $db_error=true; // Couldn't change pw
    else $success=true; // Worked out.
   } else $key_error=true; // Key Expired
  }
 }

 $p = new Page();

 $p->title="My API - Password Reset Form";
 $p->jQuery();

 $p->HTML('
  <BR>
  <CENTER><img src="i/logo.jpg"></CENTER><BR>
   <BR>
  <h1>Password Reset Request</h1>
  <FORM method="POST" action="forgot">
   <input type="hidden" name="email" value="'.urlencode($g['email']).'">
   <input type="hidden" name="key" value="'.urlencode($g['key']).'">
   <CENTER>
   <INPUT type="password" name="newpassword" id="newpassword" style="width:50%;margin:auto;"/><BR/>
   <INPUT type="password" name="repnewpassword" id="repnewpassword" style="width:50%;margin:auto;"/><BR/>
   <button id="submit">Set Password</button><BR>
   </CENTER>
  </FORM>

');

 if ( $error ) {
  $p->HTML('<div><span class="fi-warning"></span> Password did not meet the minumum requirements.  Increase complexity.</div>');
 }

 if ( $key_error ) {
  $p->HTML('<div><span class="fi-warning"></span> Your forgot password key has expired, sending another.  Check your email.</div>');
  $user=_User::FindByEmail($g['email']);
  if ( !false_or_null($user) ) Auth::Forgot($user);
 }

 if ( $db_error ) {
  $p->HTML('<div><span class="fi-warning"></span> Database error occurred, contact support for help</div>');
 }

 if ( $mismatched ) {
  $p->HTML('<div><span class="fi-warning"></span> Passwords do not match</div>');
 }

 if ( $success ) {
  $p->HTML('<div>Password updated successfully.</div>');
 }


 $p->Render();
