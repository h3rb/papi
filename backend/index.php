<?php

include '../modules/apache_request_headers_fpm.php';

/**/ if ( isset($_POST["data"]) ) {

// note that the following is the SSL address
$url = "https://api.mydomain.com/index.php"; // the index.php is optional if _htaccess is used
// since we don't necessarily have a cert when testing, we'll change it
$url = "http://localhost/";

$headers = array(
 'X-Papi-Application-Id: 22ce'
);
$fields = array(
 "data" => $_POST["data"]
);
if ( isset($_POST["ADMINISTRATOR"]) && $_POST["ADMINISTRATOR"] == 1 ) {
 $headers[]= 'X-Papi-Admin-Token: 3d3d3d';
}
if ( strlen($_POST["SESSION"]) > 0 ) {
 $headers[]= 'X-Papi-Session-Token: '.$_POST["SESSION"];
}

$session_token=isset($_POST["SESSION"])?$_POST["SESSION"]:'';

$curl_error="";

//open connection
$ch = curl_init();

curl_setopt($ch, CURLOPT_URL,            $url                          );
curl_setopt($ch, CURLOPT_HTTPHEADER,     $headers                      );
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1                             );
curl_setopt($ch, CURLOPT_POST,           count($fields)                );
curl_setopt($ch, CURLOPT_POSTFIELDS,     http_build_query($fields) );
curl_setopt($ch, CURLOPT_TIMEOUT,        10                            );

$result = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$is_valid_json = json_decode($result,true);
if ( curl_error($ch) )  ( $curl_error= 'CURL Error: '.curl_error($ch) );
curl_close($ch);

/**/ }

?>
<!DOCTYPE html>
<HTML>
<HEAD>
<TITLE>PAPI Backend</TITLE>
<SCRIPT type="text/javascript" src="json-formatter.js"></SCRIPT>
</HEAD>
<STYLE>
pre {
    white-space: pre-wrap;       /* Since CSS 2.1 */
    white-space: -moz-pre-wrap;  /* Mozilla, since 1999 */
    white-space: -pre-wrap;      /* Opera 4-6 */
    white-space: -o-pre-wrap;    /* Opera 7 */
    word-wrap: break-word;       /* Internet Explorer 5.5+ */
}
</STYLE>
<BODY>
<H1>PAPI Backend</H1>
<HR><PRE><?php var_dump($result,true); ?></PRE>
<TABLE width=100%><TR><TD VALIGN=TOP>
<FORM method=POST>
 <INPUT NAME="ADMINISTRATOR" TYPE="CHECKBOX" VALUE="1"> As administrator?<BR>
 Session Token: <INPUT NAME="SESSION" TYPE="TEXT" VALUE="<?php echo (isset($session_token) ? $session_token : ''); ?>"><BR>
 JSON data: <BR>
 <TEXTAREA NAME="data" ROWS=10 COLS=40><?php echo $_POST["data"]; ?></TEXTAREA><BR>
 <INPUT NAME="SUBMIT" TYPE="SUBMIT" VALUE="REQUEST">
</FORM>
</TD><TD>
<h3>Sample Requests</h3>
<PRE>
{ "action": "login", "username":"h3rb", "password":"guest" }
{ "action": "forgot", "email":"your@gmail.com" }
{ "action": "me" }
{ "action": "user", "create": { "firstname":"Jim", "lastname":"Jefferies", "company":"of-millions", "username":"jimbo", "password":"sassafras", "email":"jim@jefferies.agency" } }
{ "action": "user", "delete":"-ID-" }
{ "action": "user", "update": { "ID":"-ID-", "firstname":"james" } }
{ "action": "user", "get": "-ID-" }
{ "action": "logout" }
</PRE>
</TD></TR></TABLE>
<?php
 if ( !is_null($result) ) { echo '<PRE>'; var_dump($result); echo '</PRE><BR>'; }
 if ( !is_null($result) ) { ?> Response code: <?php echo $httpcode; }
 if ( isset($is_valid_json) && !is_null($is_valid_json) ) { ?><BR>JSON Validated OK
<SCRIPT>
const myJSON = <?php echo $result; ?>;
const formatter = new JSONFormatter(myJSON,Infinity);
document.body.appendChild(formatter.render());
</SCRIPT><BR>
RAW: <PRE><?php echo $result; ?></PRE>
<?php } else echo ' &rarr; INVALID JSON: <PRE>'.$result.'</PRE>';
if ( strlen($curl_error) > 0 ) echo $curl_error;
?>
</BODY>
</HTML>
