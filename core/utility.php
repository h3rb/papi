<?php

if ( !function_exists('plog') ) {
 function plog( $s, $prefix='last' ) {
  global $plog_level;
  if ( !isset($plog_level) ) if ( !is_null($gv=g('plog_level')) ) $plog_level=$gv;
  if ( !isset($plog_level) || $plog_level == 0 ) return;
  if ( $plog_level === 2 ) { echo date('r').'| '.( is_string($s) ? $s : print_r($s,TRUE)).PHP_EOL; return; }
  return @file_put_contents(
   SITE_ROOT.'/cache/logs/'.$prefix.'-log.txt',
   date('r').'| '.( is_string($s) ? $s : print_r($s,TRUE)).PHP_EOL,
   FILE_APPEND
  );
 }
}

if ( !function_exists('deep_json_encode') ) {
 function deep_json_encode( $a, $loose=FALSE ) {
  if ( $loose === FALSE )
  $o= json_encode($a, JSON_PRESERVE_ZERO_FRACTION/* | JSON_INVALID_UTF8_SUBSTITUTE in 7.2*/, 512);
  else
  $o= json_encode($a, JSON_PRESERVE_ZERO_FRACTION | JSON_PARTIAL_OUTPUT_ON_ERROR /* | JSON_INVALID_UTF8_SUBSTITUTE in 7.2*/, 512 );
  $e=json_last_error();
  switch ( $e ) {
   case JSON_ERROR_NONE: return $o;
   case JSON_ERROR_DEPTH: return 'JSON_ERROR_DEPTH';
   case JSON_ERROR_STATE_MISMATCH: return 'JSON_ERROR_STATE_MISMATCH';
   case JSON_ERROR_CTRL_CHAR: return 'JSON_ERROR_CTRL_CHAR';
   case JSON_ERROR_SYNTAX: return 'JSON_ERROR_SYNTAX';
   case JSON_ERROR_UTF8: return 'JSON_ERROR_UTF8';
   case JSON_ERROR_RECURSION: return 'JSON_ERROR_RECURSION';
   case JSON_ERROR_INF_OR_NAN: return 'JSON_ERROR_INF_OR_NAN';
   case JSON_ERROR_UNSUPPORTED_TYPE: return 'JSON_ERROR_UNSUPPORTED_TYPE';
   case JSON_ERROR_INVALID_PROPERTY_NAME: return 'JSON_ERROR_INVALID_PROPERTY_NAME';
   case JSON_ERROR_UTF16: return 'JSON_ERROR_UTF16';
  }
 }
}

if ( !function_exists('deep_json_decode') ) {
 function deep_json_decode( $a, $loose=FALSE ) {
  if ( is_array($a) ) { plog("deep_json_decode received an array: ".vars($a).' Backtrace: '.vars(debug_backtrace())); return $a; }
  if ( $loose === FALSE )
  $o= json_decode($a, TRUE, 512, JSON_PRESERVE_ZERO_FRACTION/* | JSON_INVALID_UTF8_SUBSTITUTE in 7.2*/);
  else
  $o= json_decode($a, TRUE, 512, JSON_PRESERVE_ZERO_FRACTION | JSON_PARTIAL_OUTPUT_ON_ERROR /* | JSON_INVALID_UTF8_SUBSTITUTE in 7.2*/);
  $e=json_last_error();
  switch ( $e ) {
   case JSON_ERROR_NONE: return $o;
   case JSON_ERROR_DEPTH: return 'JSON_ERROR_DEPTH';
   case JSON_ERROR_STATE_MISMATCH: return 'JSON_ERROR_STATE_MISMATCH';
   case JSON_ERROR_CTRL_CHAR: return 'JSON_ERROR_CTRL_CHAR';
   case JSON_ERROR_SYNTAX: return 'JSON_ERROR_SYNTAX';
   case JSON_ERROR_UTF8: return 'JSON_ERROR_UTF8';
   case JSON_ERROR_RECURSION: return 'JSON_ERROR_RECURSION';
   case JSON_ERROR_INF_OR_NAN: return 'JSON_ERROR_INF_OR_NAN';
   case JSON_ERROR_UNSUPPORTED_TYPE: return 'JSON_ERROR_UNSUPPORTED_TYPE';
   case JSON_ERROR_INVALID_PROPERTY_NAME: return 'JSON_ERROR_INVALID_PROPERTY_NAME';
   case JSON_ERROR_UTF16: return 'JSON_ERROR_UTF16';
  }
 }
}

if ( !function_exists('endsWith') ) {
 // search forward starting from end minus needle length characters
 function endsWith($haystack, $needle) {
  $len=strlen($needle);
  return matches(substr($haystack, strlen($haystack)-$len, $len), $needle);
 }
}


if ( !function_exists('numtoletter') ) {
 function numtoletter($a) {
  if (!is_numeric($a) ) return 'NaN';
  $final=("".$a);
  $final=str_replace('1','A',$final);
  $final=str_replace('2','B',$final);
  $final=str_replace('3','C',$final);
  $final=str_replace('4','D',$final);
  $final=str_replace('5','E',$final);
  $final=str_replace('6','F',$final);
  $final=str_replace('7','G',$final);
  $final=str_replace('8','H',$final);
  $final=str_replace('9','I',$final);
  $final=str_replace('0','Z',$final);
  $final=str_replace('-','O',$final);
  return $final;
 }
}

if ( !function_exists("mum") ) { function mum( $val ) { return floatval($val) * 3.45; } }

if ( !function_exists("is_assoc") ) {
 function is_assoc(array $arr) {
   if (array() === $arr) return false;
   return array_keys($arr) !== range(0, count($arr) - 1);
 }
}

if ( !function_exists("is_sequent") ) {
 function is_sequent(array $arr) { return !is_assoc($arr); }
}

if ( !function_exists("array_of_arrays_to_arrays") ) {
 // Converts an array of like-keyed arrays into "an array of arrays" keyed similarly.
 // Actively seeks out irregularities, setting them to the default empty value.
 // Example:  array( array("a"=>1,"b"=>2), array("a"=>3,"b"=>4) )
 //   becomes array( "a"=>array(1,3), "b"=>array(2,4) )
 // Returns false on failure due to input data not being correct.
 function array_of_arrays_to_arrays( array $in, $default_empty_value=NULL ) {
  if ( !is_array($in) ) return FALSE;
  if ( count($in) === 0 ) return FALSE;
  if ( !is_sequent($in) ) return FALSE;
  $out=array();
  foreach ( $in as $a ) foreach ( $a as $k=>$v ) if ( !isset($out[$k]) ) $out[$k]=array();
  $keys=array_keys($out);
  foreach ( $in as $a ) foreach ( $keys as $k ) {
   if ( isset($a[$k]) ) $out[$k][]=$a[$k]; else $out[$k][]=$default_empty_value;
  }
  return $out;
 }
}


if ( !function_exists('a') ) {
 function a() {
  return func_get_args();
 }
}

if ( !function_exists('csv') ) {
 function csv($arr,$sep='\t') {
  $out='';
  $first=array_shift($arr);
  $columns=array_keys($first);
  array_unshift($arr,$first);
  $out='Index'.$sep.implode($sep,$columns)."\n";
  foreach ( $arr as $index=>$row ) {
   $out.=$index.$sep.implode($sep,$row)."\n";
  }
  return $out;
 }
}

if ( !function_exists('fromcsv') ) {
 function fromcsv($in,$sep=',') {
  $out=explode("\n",$in);
  foreach ( $out as &$line ) $line=explode($sep,$out);
  return $out;
 }
}


if ( !function_exists("is_decimal_value") ) {
 function is_decimal_value( $a ) {
    $d=0;
    $b= str_split($a);
    foreach ( $b as $c ) {
        if ( is_numeric($c) ) continue;
        if ( stripos($c,".") === 0 ) {
            $d++;
            if ( $d > 1 ) return FALSE;
            else continue;
        } else
        return FALSE;
    }
    return TRUE;
 }
}

if ( !function_exists('is_date_string') ) {
 function is_date_string( $date ) { 
  if ( strtotime($date) > strtotime(0) ) return TRUE;
  return false;
 }
}

if ( !function_exists("biggest") ) {
 function biggest(&$out,$in) {
  if ( false_or_null($out) || $out < $in ) $out=$in;
 }
}

if ( !function_exists("smallest") ) {
 function smallest(&$out,$in) {
  if ( false_or_null($out) || $out > $in ) $out=$in;
 }
}

if ( !function_exists("safe_average") ) {
 function safe_average( $a, $d ) {
  if ( intval($d) === 0 ) return 0;
  return ( floatval($a)/floatval($d) );
 }
}

if ( !function_exists("percent") ) {
 function percent( $a, $b ) {
  $ratio=floatval($a)/floatval($b);
  return $ratio * 100.0;
 }
}

// Initializes a value in an array if it is unset.
if ( !function_exists('arrinit') ) {
 function arrinit(&$arr,$index,$value='') {
  if ( !isset($arr[$index]) ) $arr[$index]=$value;
 }
}

// Averages a value across an array.
if ( !function_exists('avgarray') ) {
  function avgarray( $a, $key=NULL ) {
   if ( false_or_null($key) ) {
    $count=0;
    $total=0.0;
    foreach ( $a as $k=>$v ) if ( !false_or_null($v) ) { $count++; $total+=$v; }
    if ( $count === 0 ) return 0;
    return $count/$total;
   } else {
    $count=0;
    $total=0.0;
    foreach ( $a as $k=>$v ) if ( !false_or_null($v) && is_array($v) && isset($v[$key]) ) { $count++; $total+=$v[$key]; }
    if ( $count === 0 ) return 0;
    return $count/$total;
   }
  }
}

// Using an input array as a blueprint, generates an identical recursive structure with values zeroed.
if ( !function_exists('zeroarray') ) {
  function zeroarray( $in ) {
   $out=array();
   foreach ( $in as $k=>$v ) {
    if ( is_array($v) ) $out[$k]=zeroarray($v);
    else if ( is_decimal_value($v) ) $out[$k]=0.0;
    else if ( is_integer($v) ) $out[$k]=0;
    else $out[$k]=$v;
   }
   return $out;
  }
}

// Add up structured array A and B and return the result.
if ( !function_exists('addarray') ) {
  function addarray( $a,$b ) {
   foreach ( $b as $k=>$v ) {
    if ( is_array($v) ) $a[$k]=addarray($a[$k],$v);
    else if ( is_decimal_value($v) ) $a[$k]+=$v;
    else if ( is_integer($v) ) $b[$k]+=$v;
   }
   return $a;
  }
}

// Divide values in a structured array by a divisor (single number or matching structural array), and return it.
if ( !function_exists('dividearray') ) {
  function dividearray( $a,$divisor ) {
   if ( is_array($divisor) ) {
    foreach ( $divisor as $k=>$v ) {
     if ( is_array($v) ) $a[$k]=dividearray($a[$k],$v);
     else if ( is_decimal_value($v) && $v > 0.0 ) $a[$k]/=$v;
     else if ( is_integer($v) && $v > 0.0 ) $a[$k]/=$v;
    }
    return $a;
   } else {
    $o=zeroarray($a);
    foreach ( $a as $k=>$v ) {
     if ( is_array($v) ) $o[$k]=dividearray($a[$k],$divisor);
     else if ( is_decimal_value($v) && $v > 0.0 ) $o[$k]=$a[$k]/$divisor;
     else if ( is_integer($v) && $v > 0.0 ) $o[$k]=$a[$k]/$divisor;
     else $o[$k]=$v;
    }
    return $o;
   }
  }
}

// Find the values that are biggest and return them.
if ( !function_exists('bigger_array') ) {
 function bigger_array( $r, $b ) {
  if ( is_null($r) ) return $b;
  else foreach ( $b as $k=>$v ) {
   if ( is_array($v) ) $r[$k]=bigger_array($r[$k],$v);
   else if ( is_decimal_value($v) && floatval($v) > floatval($r[$k]) ) $r[$k]=$v;
   else if ( is_numeric($v) && intval($v) > intval($r[$k]) ) $r[$k]=$v;
  }
  return $r;
 }
}

// Find the biggest values across a structured array and return it.
if ( !function_exists('biggest_in_array') ) {
  function biggest_in_array( $a ) {
   $r=NULL;
   $count=0;
   foreach ( $a as $b ) $r=bigger_array($r,$b);
   return $r;
  }
}

// Find the values that are smallest and return them.
if ( !function_exists('smaller_array') ) {
 function smaller_array( $r, $b ) {
  if ( is_null($r) ) return $b;
  else foreach ( $b as $k=>$v ) {
   if ( is_array($v) ) $r[$k]=smaller_array($r[$k],$v);
   else if ( is_decimal_value($v) && floatval($v) < floatval($r[$k]) ) $r[$k]=$v;
   else if ( is_numeric($v) && intval($v) < intval($r[$k]) ) $r[$k]=$v;
  }
  return $r;
 }
}

// Find the smallest values across a structured array and return it.
if ( !function_exists('smallest_in_array') ) {
  function smallest_in_array( $a ) {
   $r=NULL;
   $count=0;
   foreach ( $a as $b ) $r=smaller_array($r,$b);
   return $r;
  }
}


// Add up structured arrays A...x and return the result.
if ( !function_exists('addarrays') ) {
  function addarrays( $arrs ) {
   if ( !is_array($arrs) ) return $arrs;
   if ( count($arrs) <2 ) return $arrs;
   if ( !is_sequent($arrs) ) return $arrs;
   $total=array_pop($arrs);
   foreach ($arrs as $a) {
    $total=addarray($total,$a);
   }
   return $total;
  }
}


if (!function_exists('avgarrays') ) {
  function avgarrays( $arrs ) {
   $divisor=count($arrs);
   if ( $divisor === 0 ) return FALSE;
   return dividearray(addarrays($arrs),$divisor);
  }
}


// Copy an array disallowing pointer-in-memory features of PHP
if ( !function_exists('array_copy') ) {
function array_copy( array $array ) {
        $result = array();
        foreach( $array as $key => $val ) {
            if( is_array( $val ) ) {
                $result[$key] = array_copy( $val );
            } elseif ( is_object( $val ) ) {
                $result[$key] = clone $val;
            } else {
                $result[$key] = $val;
            }
        }
        return $result;
}
}

if ( !function_exists('g') ) {
 global $globular; $globular=array();
 function g( $v, $a=NULL ) {
  global $globular;
  if ( !is_null($a) ) $globular[$v]=$a;
  else if ( !isset($globular[$v]) ) return NULL;
  return $globular;
 }
}

if ( !function_exists('vars') ) {
 function vars($a) { return var_export($a,true); }
}


if ( !function_exists('javascript') ) {
  function javascript($code) { return '<SCRIPT type="text/javascript">'.$code.'</SCRIPT>'; }
  function js($code) { return javascript($code); }
}

if ( !function_exists('trailing_slash') ) {
 function trailing_slash($str) {
  $str=trim($str);
  $str_split=str_split($str);
  if ( $str_split[strlen($str)-1] == '/' ) return $str;
  return $str.'/';
 }
}

if ( !function_exists('include_all') ) {
 function include_all( $folder ) {
  if ( folder_exists($folder) ) {
   $includes=scandir($folder);
   foreach ( $includes as $include )
    if ( endsWith($include,".php") )
     include_once($folder.$include);
  }
 }
}

if ( !function_exists('folder_exists') ) {
 function folder_exists( $fpath ) {
  if ( file_exists($fpath) ) return is_dir($fpath) ? TRUE : FALSE;
  return FALSE;
 }
}

if ( !function_exists('array_msort') ) {
 function array_msort($array, $cols) {
  $colarr = array();
  foreach ($cols as $col => $order) {
   $colarr[$col] = array();
   foreach ($array as $k => $row) { $colarr[$col]['_'.$k] = strtolower($row[$col]); }
  }
  $eval = 'array_multisort(';
  foreach ($cols as $col => $order) {
   $eval .= '$colarr[\''.$col.'\'],'.$order.',';
  }
  $eval = substr($eval,0,-1).');';
  eval($eval);
  $ret = array();
  foreach ($colarr as $col => $arr) {
   foreach ($arr as $k => $v) {
    $k = substr($k,1);
    if (!isset($ret[$k])) $ret[$k] = $array[$k];
    $ret[$k][$col] = $array[$k][$col];
   }
  }
  return $ret;
 }
}

if ( !function_exists('renumber') ) {
 function renumber($a) {
  $b=array();
  foreach ( $a as $unordered=>$value ) $b[]=$value;
  return $b;
 }
}

if ( !function_exists('AZSort') ) {
 function AZSort($arr,$colname,$renumber=FALSE) {
  $sorted= array_msort($arr, array($colname=>SORT_ASC));
  if ( $renumber !== FALSE ) return renumber($sorted);
  return $sorted;
 }
}

if ( !function_exists('array_orderby') ) {
 function array_orderby( /* $data, ... 'column', sort_flag ... */ ) {
  $args = func_get_args();
  $data = array_shift($args);
  foreach ($args as $n => $field) {
   if (is_string($field)) {
    $tmp = array();
    foreach ($data as $key => $row) $tmp[$key] = $row[$field];
    $args[$n] = $tmp;
   }
  }
  $args[] = &$data;
  call_user_func_array('array_multisort', $args); // Suppress warnings...
  return array_pop($args);
 }
}

if ( !function_exists('boolval') ) {
 function boolval($in, $strict=false) {
  $out = FALSE;
  if ( is_string($in) ) $in=strtolower($in);
  if ( $strict === false ) {
   if (in_array($in,array('true', 'yes', 'y', '1', 'on', true, 1 ), true)) $out = TRUE;
   else if (in_array($in,array('false', 'no', 'n', '0', 'off', false, 0, null ), true)) $out = FALSE;
  } else $out = ($in?TRUE:FALSE);
  return $out;
 }
}

if ( !function_exists('arrays_combined') ) {
 function arrays_combined($in) {
  $in=func_get_args();
  $out=array();
  foreach ( $in as $array ) {
   foreach ( $array as $v ) {
    $out[]=$v;
   }
  }
  return $out;
 }
}

if ( !function_exists('array_copy') ) {
 function array_copy( $in ) {
  $out = array();
  foreach( $in as $k=>$v )
   if ( is_array($v) ) $out[$k] = array_copy( $v );
   elseif ( is_object($v) ) $out[$k] = clone $v;
   else $out[$k] = $v;
  return $out;
 }
}

if ( !function_exists('all_array_values_include') ) {
 function acl_array_values_include( $search, $required, $delim=' ' ) {
  if ( is_string($search) ) $search=words($search,$delim);
  if ( is_string($required) ) $required=words($required,$delim);
  foreach ( $required as $acl ) {
   $found=FALSE;
   foreach ( $search as $b ) if ( matches($b,$acl) ) $found=TRUE;
   if ( $found===FALSE ) return FALSE;
  }
  return TRUE;
 }
}

if ( !function_exists('dropzerolen') ) {
// Drops zero length strings from a string array, returns new array
   function dropzerolen( $strarray ) {
        $c = count($strarray); $x=0;
        $newarray=array();
        for ( $i=0; $i<$c; $i++ ) if ( strlen($strarray[$i]) > 0 ) $newarray[$x++] = $strarray[$i];
        return $newarray;
   }
}

if ( !function_exists('matches') ) {
 function matches( $a, $b, $case=FALSE ) {
  if ( is_array($a) && is_array($b) ) { // array recurse
   if ( count($a) !== count($b) ) return FALSE;
   $_A=array_copy($a);
   $_B=array_copy($b);
   while ( count($_A) > 0 ) {
    $one=array_pop($_A);
    $two=array_pop($_B);
    if ( matches($one,$two,$case) === FALSE ) return FALSE;
   }
   return TRUE;
  } else if ( is_string($a) && is_string($b) ) { // case insensitive match
   $a=trim($a);
   $b=trim($b);
   if ( $case === FALSE ) return strcasecmp($a,$b) === 0 ? TRUE : FALSE;
   else return strcmp($a,$b) === 0 ? TRUE : FALSE;
  } else if ( is_integer($a) && is_integer($b) ) {
   return $a === $b ? TRUE : FALSE;
  } else if ( is_double($a) && is_double($b) ) {
   return $a === $b ? TRUE : FALSE;
  } else if ( (is_integer($a) || is_double($a)) && (is_integer($b) || is_double($b)) ) {
   return floatval($a) == floatval($b) ? TRUE : FALSE;
  } else if ( is_bool($a) && is_bool($b) ) {
   return ($a === $b) ? TRUE : FALSE;
  } else if ( is_object($a) && is_object($b) ) {
   if ( $a->is($b) === FALSE ) return FALSE;
   return ($a == $b) ? TRUE : FALSE;
  } else return ($a == $b) ? TRUE : FALSE;
 }
}

if ( !function_exists('findmatches') ) {
 function findmatches( $named, &$inlist, $match ) {
  foreach ( $inlist as $array ) if ( matches($array[$named],$match) ) return $array;
  return FALSE;
 }
}

if ( !function_exists('matchvalue') ) {
 function matchvalue( $array, $value ) {
  foreach ( $array as $key=>$pair ) if ( matches($pair,$value) ) return $key;
  return FALSE;
 }
}

if ( !function_exists('false_or_null') ) {
 function false_or_null( $a ) {
  if ( is_array($a) && count($a) === 0 ) return TRUE;
  if ( is_string($a) && strlen(trim($a)) === 0 ) return TRUE;
  if ( $a === false || $a === FALSE
    || $a === null || $a === NULL
    || $a === 0 || $a === 0.0 ) return TRUE;
  return FALSE;
 }
}

if ( !function_exists('show_array') ) {
 function show_array( $a, $indent='' ) {
  $out='array( '.PHP_EOL;
  $end_char=strlen($indent) == 0 ? ';' : ',';
  foreach ( $a as $k=>$v ) {
   $out.=$indent;
   if ( is_string($k) ) $out.='"'.$k.'"';
   else $out.=$k;
   $out.=' => ';
   if ( is_string($v) ) $out.='"'.$v.'"';
   else if ( is_array($v) ) $out.=show_array($a,$indent.' ');
   $out.=','.PHP_EOL;
  }
  $out.=$indent.')'.$end_char.PHP_EOL;
  return $out;
 }
}

if ( !function_exists('error') ) {
 function error($context,$message,$fatal=FALSE) {
  plog($context.': '.$message);
  error_log($context.': '.$message);
  if ( $fatal !== FALSE ) die;
 }
}

if ( !function_exists('str_replace_1once') ) {
 function str_replace_1once($needle, $replace, $haystack) {
  $pos = strpos($haystack, $needle);
  if ($pos === false) return $haystack;
  return substr_replace($haystack, $replace, $pos, strlen($needle));
 }
}

if ( !function_exists('urlvars') ) {
 function urlvars( $in ) {
  if ( !is_array($in) ) $in=func_get_args();
  else {
   $out=array();
   foreach ( $in as $a=>$b ) { $out[]=$a; $out[]=$b; }
   $in=$out;
  }
  $out='';
  $final=array();
  $r=0;
  $was=NULL;
  foreach ($in as $n=>$v) {
   if ( is_null($was) ) {
    $was=$v;
    continue;
   }
   $final[$was]=$v;
   $was=NULL;
  }
  foreach ($final as $k=>$v) $out.='&'.$k.'='.$v;
  return str_replace_1once('&','?',$out);
 }
}

if ( !function_exists('ajaxvars') ) {
 function ajaxvars( $in ) {
  if ( !is_array($in) ) $in=func_get_args();
  else {
   $out=array();
   foreach ( $in as $a=>$b ) { $out[]=$a; $out[]=$b; }
   $in=$out;
  }
  $out='';
  $final=array();
  $r=0;
  $was=NULL;
  foreach ($in as $n=>$v) {
   if ( is_null($was) ) {
    $was=$v;
    continue;
   }
   $final[$was]=$v;
   $was=NULL;
  }
  foreach ($final as $k=>$v) $out.='&'.$k.'='.$v;
  return $out;
 }
}

if ( !function_exists('getorpost') ) {
 function getorpost($e) {
  if ( isset($_GET[$e]) ) return $_GET[$e];
  if ( isset($_POST[$e]) ) return $_POST[$e];
  return FALSE;
 }
}

if ( !function_exists('getpost') ) {
 function getpost() {
  $a=array();
  foreach ( $_GET  as $k=>$v ) $a[$k]=$v;
  foreach ( $_POST as $k=>$v ) $a[$k]=$v;
  return $a;
 }
}

if ( !function_exists('page_input') ) {
 function page_input($keys) {
  if ( !is_array($keys) ) $keys=func_get_args();
  global $getpost;
  if ( !isset($getpost) || !is_array($getpost) ) $getpost=getpost();
  plog('page_input:getpost(): '.vars($getpost));
  plog('page_input:checked against required input parameters '.vars($keys));
  foreach ( $keys as $numbered=>$v ) if ( !isset($getpost[$v]) ) return FALSE;
  plog('page_input:PASSED');
  return $getpost;
 }
}

if ( !function_exists('words') ) {
// Splits a series of words (a b c) into an array(0=>a,1=>b,2=>c)
 function words($r,$delim=' ') {
  if ( is_array($r) ) $parts=$r; else $parts=explode($delim,$r);
  if ( !is_array($parts) ) return array();
  $out=array();
  foreach ( $parts as $part ) {
   $trimmed=trim($part);
   if ( strlen($trimmed) > 0 ) $out[]=$trimmed;
  }
  return $out;
 }
}

if ( !function_exists('wordpairs') ) {
 function wordpairs($text) {
  $words=words(str_replace("\r",'',str_replace("\n",' ',$text)));
  $out=array();
  $i=0; $key='';
  foreach ( $words as $word ) {
   if ( $i % 2 === 0 ) $key=$word;
   else $out[$key]=$word;
   $i++;
  }
  return $out;
 }
}

if ( !function_exists('ints') ) {
 function ints($r,$delim=' ') {
  $out=words($r,$delim);
  foreach ( $out as $indexed=>&$result ) $result=intval($result);
  return $out;
 }
}

if ( !function_exists('ints_equals') ) {
 function ints_equals( $a, $b, $delim=' ' ) {
  $a=ints($a);
  $b=ints($b);
  if ( count($a) != count($b) ) return false;
  do {
   $pa=array_pop($a);
   $pb=array_pop($b);
   $test = intval($pa) === intval($pb);
   if ( !$test ) return false;
  } while ( $test && $pa !== NULL && $pb !== NULL );
  return true;
 }
}

if ( !function_exists('ints_words') ) {
 function ints_words($r,$delim=' ') {
  $out=words($r,$delim);
  foreach ( $out as $indexed=>&$result ) if ( is_numeric($result) ) $result=intval($result);
  return $out;
 }
}

if ( !function_exists('colons') ) {
// Variant of words() that supports (a:b:c) instead of (a b c)
 function colons($r) { return words($r,':'); }
}

if ( !function_exists('wordpairs') ) {
// Splits a series of space-separated equals-delimited key value pairs (a=c b=d) into an array(a=>c,b=>d)
 function wordpairs($r,$delim='=',$worddelim=' ') {
  $words=words($r,$worddelim);
  $out=array();
  foreach ( $words as $pair ) {
   $parts=explode($delim,$pair);
   $trimmed_key=trim($parts[0]);
   $trimmed_value=trim($parts[1]);
   if ( strlen($trimmed_key) == 0 ) {
    $out[]=$trimmed_value;
   } else {
    $out[$trimmed_key]=$trimmed_value;
   }
  }
  return $out;
 }
}

if ( !function_exists('contains') ) {
 function contains($haystack,$needle,$case=FALSE) {
  if ( $case === FALSE ) {
   return ( stripos($haystack,$needle) !== FALSE ) ? TRUE : FALSE;
  } else {
   return ( strpos($haystack,$needle) !== FALSE ) ? TRUE : FALSE;
  }
 }
}

if ( !function_exists('isfile') ) {
 function isfile($a,$path='',$show_errors=TRUE,$fatal=FALSE) {
  $path=trailing_slash($path);
  if ( is_array($a) ) {
   foreach ( $a as $file ) if ( !file_exists($path.$file) ) {
    if ( $show_errors === TRUE ) error('isfile()','`'.$path.$file.'` did not exist',$fatal);
    return FALSE;
   }
   return TRUE;
  } else if ( is_string($a) ) {
   if ( !file_exists($path.$a) ) {
    if ( $show_errors === TRUE ) error('isfile()','`'.$path.$file.'` did not exist',$fatal);
    return FALSE;
   }
   return TRUE;
  }
  if ( $show_errors ) error('isfile()','was provided a non-string, non-array',$fatal);
  return FALSE;
 }
}

if ( !function_exists('hash_code') ) {
    // Generates a unique 32 bit string based on previous executions
    // URL-safe hashing only letters Aa-Zz and 0-9
    // Optional parameters: define a set, for multiple exclusive hash sets,
    //                      define a hash length, defaulting to 254 chars
    // 1.55409285284366e+60 unique values
 function hash_code( $codeset = "1", $hashlength = 254 ) {
  $fn = SITE_ROOT."cache/hashes/Hashes_" . $codeset . ".txt";
  if ( file_exists($fn) ) $previous = file_get_contents($fn);
  else $previous = "";
  $hashcodes = explode("\n",$previous);
  $found = 1;
  while ( $found > 0 ) {
   // generate a new hash
   $newcode = "";
   for ( $x = 0; $x < $hashlength; $x++ ) {
    if ( rand(0,1) == 1 ) $newcode = $newcode . chr(rand(48,57));
    else if ( rand(0,1) == 1 )
    $newcode = $newcode . chr(rand(65,90));
    else       $newcode = $newcode . chr(rand(97,122));
   }
   $found = 0; // check for duplicates, each must be unique
   $array_length = count($hashcodes);
   for ( $y = 0; $y < $array_length; $y++ ) {
    if ( strcmp( $hashcodes[$y], $newcode ) == 0 ) $found++;
   }
   if ( is_numeric($newcode) ) $found++; // can't be a number
  }
  $hashcodes[] = $newcode;
  file_put_contents_atomic($fn,implode("\n",$hashcodes));
  return $newcode;
 }
}

        // bitvector mathematics
if ( !function_exists('flag') ) {
    function flag( $bitvector, $flag ) {
     $bit = intval($bitvector);
     $flag = intval($flag);
     if ( $bitvector & $flag ) return true;
     return false;
    }
}

if ( !function_exists('biton') ) {
    function biton( $bit, $flag ) {
     $bit = (int) $bit;
     $flag = (int) $flag;
     return $bit & $flag;
    }
}

if ( !function_exists('bitoff') ) {
    function bitoff( $bit, $flag ) {
     $bit = (int) $bit;
     $flag = (int) $flag;
     return $bit & ~($flag);
    }
}

if ( !function_exists('bittoggle') ) {
    function bittoggle( $bit, $flag ) {
     $bit = (int) $bit;
     $flag = (int) $flag;
     return $bit ^ (1 << $flag);
    }
}

// Backtick management.

if ( !function_exists('adt') ) {
        // Adds ` ticks to a list of fields seperated by , commas (fix by RainCT)
        // see also: adq, sq, msq
        function adt( $strlist ) {
                $stra = explode(',', str_replace('`', '', $strlist));
                if ( count($stra) == 1 ) return '`'.$stra[0].'`';
                foreach ( $stra AS $key => $value) {
                        $stra[$key] = ' `'.$value.'`';
                }
                return implode(',', $stra);
        }
}

if ( !function_exists('adq') ) {
        // Adds ' single quote to a list of fields seperated by , commas (fix by RainCT)
        // see also: adt, sq, msq
        function adq( $strlist ) {
                $stra = explode(',', str_replace("'", '', $strlist));
                if ( count($stra) == 1 ) return "'".$stra[0]."'";
                foreach ( $stra AS $key => $value) {
                        $stra[$key] = " '".$value."'";
                }
                return implode(',', $stra);
        }
}

if ( !function_exists('sq') ) {
        // Slash quotes: fixes \" and \' to be " and ' (the sourceforge bug)
        // see also: adt, adq, msq
        function sq( $str ) {
            $str = str_replace("\\'", "'", $str);
            $str = str_replace("\\\"", '"', $str);
            return $str;
        }
}

if ( !function_exists('msq') ) {
        // Make slash quotes: fixes " and ' to be \" and \' (the sourceforge bug)
        // see also: adt, sq, adq
        function msq( $str ) {
            $str = str_replace("'", "\\'", $str);
            $str = str_replace("\"", '\\\"', $str);
            return $str;
        }
}

if ( !function_exists('smash_tick') ) {
    function smash_tick( $ticked ) {     return str_replace( "`","", $ticked );    }
}

if ( !function_exists('file_put_contents_atomic') ) {
    // file_put_contents() will cause concurrency problems - that is,
    // it doesn't write files atomically (in a single operation),
    // which sometimes means that one php script will be able to,
    // for example, read a file before another script is done writing
    // that file completely.
    // The following function was derived from a function in Smarty
    //  (http://smarty.php.net) which uses rename() to replace the file
    //  - rename() is atomic on Linux.
    // On Windows, rename() is not currently atomic, but should be in
    // the next release (circa 2008). Until then, this function,
    // if used on Windows, will fall back on unlink() and rename(),
    // which is still not atomic...

    define("FILE_PUT_CONTENTS_ATOMIC_TEMP", dirname(__FILE__)."/cache");
    define("FILE_PUT_CONTENTS_ATOMIC_MODE", 0777);

    function file_put_contents_atomic($filename, $content) {
      $temp = tempnam(FILE_PUT_CONTENTS_ATOMIC_TEMP, 'temp');
      if (!($f = @fopen($temp, 'wb'))) {
        $temp = FILE_PUT_CONTENTS_ATOMIC_TEMP
                . DIRECTORY_SEPARATOR
                . uniqid('temp');
        if (!($f = @fopen($temp, 'wb'))) {
            trigger_error("file_put_contents_atomic() : error writing temporary file '$temp'", E_USER_WARNING);
            return false;
        }
      }
      @fwrite($f, $content);
      @fclose($f);
      if (!@rename($temp, $filename)) {
           @unlink($filename);
           @rename($temp, $filename);
      }
      @chmod($filename, FILE_PUT_CONTENTS_ATOMIC_MODE);
      return true;
    }
}

if ( !function_exists('strip_html_tags') ) {
 /**
  * Remove HTML tags, including invisible text such as style and
  * script code, and embedded objects.  Add xxxline breaksxxx spaces around
  * block-level tags to prevent word joining after tag removal.
  */
 function strip_html_tags( $text )
 {
    $text = preg_replace(
        array(
          // Remove invisible content
            '@<head[^>]*?>.*?</head>@siu',
            '@<style[^>]*?>.*?</style>@siu',
            '@<script[^>]*?.*?</script>@siu',
            '@<object[^>]*?.*?</object>@siu',
            '@<embed[^>]*?.*?</embed>@siu',
            '@<applet[^>]*?.*?</applet>@siu',
            '@<noframes[^>]*?.*?</noframes>@siu',
            '@<noscript[^>]*?.*?</noscript>@siu',
            '@<noembed[^>]*?.*?</noembed>@siu',
          // Add line breaks before and after blocks
            '@</?((address)|(blockquote)|(center)|(del))@iu',
            '@</?((div)|(h[1-9])|(ins)|(isindex)|(p)|(pre))@iu',
            '@</?((dir)|(dl)|(dt)|(dd)|(li)|(menu)|(ol)|(ul))@iu',
            '@</?((table)|(th)|(td)|(caption))@iu',
            '@</?((form)|(button)|(fieldset)|(legend)|(input))@iu',
            '@</?((label)|(select)|(optgroup)|(option)|(textarea))@iu',
            '@</?((frameset)|(frame)|(iframe))@iu',
        ),
        array(
            ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
            " ", " ", " ", " ", " ", " ",  // was \n$0
            " ", " ",
        ),
        $text );
    return strip_tags( $text );
 }
}


if ( !function_exists('current_page_url') ) {
 function current_page_url() {
  if ( !isset($_SERVER['SERVER_PORT']) && !isset($_SERVER['SERVER_NAME']) && !isset($_SERVER['REQUEST_URI']) )
   return FALSE;
  $pageURL = 'http';
  if (isset($_SERVER['HTTPS']) && $_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
  $pageURL .= "://";
  if ($_SERVER["SERVER_PORT"] != "80") {
   $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
  } else {
   $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
  }
  return $pageURL;
 }
}


if ( !function_exists('browser') ) {
 function browser( ) {
  return ( isset( $_SERVER['HTTP_USER_AGENT'] ) ) ? strtolower( $_SERVER['HTTP_USER_AGENT'] ) : '';
 }
}


if ( !function_exists('isIE') ) {
 function isIE( ) {
  if (stristr(browser(), "msie")) return true;
  else return false;
 }
}


if(!function_exists('mime_content_type')) {

    function mime_content_type($filename) {

        $mime_types = array(

            'txt' => 'text/plain',
            'htm' => 'text/html',
            'html' => 'text/html',
            'php' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'xml' => 'application/xml',
            'swf' => 'application/x-shockwave-flash',
            'flv' => 'video/x-flv',

            // images
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'ico' => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml',

            // archives
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            'exe' => 'application/x-msdownload',
            'msi' => 'application/x-msdownload',
            'cab' => 'application/vnd.ms-cab-compressed',

            // audio/video
            'mp3' => 'audio/mpeg',
            'qt' => 'video/quicktime',
            'mov' => 'video/quicktime',

            // adobe
            'pdf' => 'application/pdf',
            'psd' => 'image/vnd.adobe.photoshop',
            'ai' => 'application/postscript',
            'eps' => 'application/postscript',
            'ps' => 'application/postscript',

            // ms office
            'doc' => 'application/msword',
            'rtf' => 'application/rtf',
            'xls' => 'application/vnd.ms-excel',
            'ppt' => 'application/vnd.ms-powerpoint',

            // open office
            'odt' => 'application/vnd.oasis.opendocument.text',
            'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        );

        $ext = strtolower(array_pop(explode('.',$filename)));
        if (array_key_exists($ext, $mime_types)) {
            return $mime_types[$ext];
        }
        elseif (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME);
            $mimetype = finfo_file($finfo, $filename);
            finfo_close($finfo);
            return $mimetype;
        }
        else {
            return 'application/octet-stream';
        }
    }
}

// Basic enumeration class
// Source: http://stackoverflow.com/questions/254514/php-and-enumerations
/* Usage example in examples/enums.php */
abstract class Enum {
    private static $constCacheArray = NULL;
    public static function get() {
        if (self::$constCacheArray == NULL) {
            self::$constCacheArray = array();
        }
        $calledClass = get_called_class();
        if (!array_key_exists($calledClass, self::$constCacheArray)) {
            $reflect = new ReflectionClass($calledClass);
            self::$constCacheArray[$calledClass] = $reflect->getConstants();
        }
        return self::$constCacheArray[$calledClass];
    }
    public static function has($name, $strict = false) {
     $constants = self::get();
     if ( is_integer($name) ) {
      return in_array(intval($name),$constants,$strict = true);
     } else
     if ( is_string($name) ) {
      if ($strict) return array_key_exists($name, $constants) ? TRUE : FALSE;
      $keys = array_map('strtolower', array_keys($constants));
      return in_array(strtolower($name), $keys) ? TRUE : FALSE;
     } else
     if ( is_array($name) ) {
      foreach ( $name as $names ) if ( !self::has($names) ) return FALSE;
      return TRUE;
     } else return FALSE;
    }
    public static function set($v) {
     if ( self::has($v) ) return $v;
     return FALSE;
    }
    // Given a name, return its numeric value
    public static function num($named) {
     $options=self::get();
     foreach ( $options as $name=>$value ) {
      if ( matches($name,$named) ) return $value;
     }
     return FALSE;
    }
    public static function asOptions( $selected=NULL ) {
     $enumerated=self::get();
     $options=array();
     if ( $selected === NULL ) {
      foreach ( $enumerated as $name=>$value ) {
       $options[]=array( 'value'=>$value, 'label'=>$name, 'name'=>static::name($value) );
      }
     } else {
      foreach ( $enumerated as $name=>$value ) {
       $opt=array( 'value'=>$value, 'label'=>$name, 'name'=>static::name($value) );
       if ( matches($selected,$value) ) $opt['selected']=TRUE;
       $options[]=$opt;
      }
     }
     return $options;
    }
    public static function asOptions2( $selected=NULL ) {
     $enumerated=self::get();
     $options=array();
     if ( $selected === NULL ) {
      foreach ( $enumerated as $name=>$value ) {
       $options[]=array( 'value'=>$value, 'name'=>$name, 'label'=>static::name($value) );
      }
     } else {
      foreach ( $enumerated as $name=>$value ) {
       $opt=array( 'value'=>$value, 'name'=>$name, 'label'=>static::name($value) );
       if ( matches($selected,$value) ) $opt['selected']=TRUE;
       $options[]=$opt;
      }
     }
     return $options;
    }
    public static function asOptions3( $selected=NULL ) {
     $enumerated=self::get();
     $options=array();
     if ( $selected === NULL ) {
      foreach ( $enumerated as $name=>$value ) {
       $options[]=array( 'value'=>$value, 'name'=>$name, 'label'=>static::name($value) );
      }
     } else {
      foreach ( $enumerated as $name=>$value ) {
       $opt=array( 'value'=>$value, 'name'=>$name, 'label'=>static::name($value) );
       if ( matches($selected,$value) ) $opt['selected']=TRUE;
       $options[]=$opt;
      }
     }
     $out='';
     foreach ( $options as $opt ) {
      $out.='<option value="'.$opt['value'].'"'.(isset($opt['selected']) && $opt['selected']===TRUE ? ' selected' : '').'>'.$opt['label'].'</option>';
     }
     return $out;
    }
    public static function asArray() {
     $enumerated=self::get();
     $options=array();
     foreach ( $enumerated as $name=>$value ) {
      $options[(static::name($value))]=$value;
     }
     return $options;
    }
    // Override for getting the name from a number
    protected static function name($n) { return 'Undefined ('.$n.')'; }
}



if ( !function_exists('cook') ) {
 function cook( $v, $s, $t=timeout ) {
  $s=base64_encode($s);
  $domain = str_replace( "http:/", "", current_page_url() );
  $domain=explode("/", $domain);
  $domain=str_replace("www.", "", $domain[1]);
  $dt=time()+$t;
  setcookie( $v, $s, $dt, '/', $domain, isset($_SERVER["HTTPS"]), false );
  $_SESSION[$v]=$s;
  $domain='.'.$domain;
  setcookie( $v, $s, $dt, '/', $domain, isset($_SERVER["HTTPS"]), false );
 }
}

if ( !function_exists('uncook') ) {
 function uncook( $v ) {
  unset($_SESSION[$v]);
  $domain = str_replace( "http:/", "", curPageURL() );
  $domain=explode("/", $domain);
  $domain=str_replace( "www.", "", $domain[1] );
  $dt=time()-3600;
  $res=setcookie($v, "", $dt, '/', $domain, isset($_SERVER["HTTPS"]), false );
  $res=setcookie($v, "NOT VALID", mktime(12,0,0,1, 1, 1970), '/', $domain, isset($_SERVER["HTTPS"]), false );
  $res=setcookie($v, "", mktime(12,0,0,1, 1, 1970), '/', $domain, isset($_SERVER["HTTPS"]), false );
  setcookie($v,FALSE, $dt );
  $domain='.'.$domain;
  $res=setcookie($v, "", $dt, '/', $domain, false, false );
  $res=setcookie($v, "NOT VALID", mktime(12,0,0,1, 1, 1970), '/', $domain, isset($_SERVER["HTTPS"]), false );
  $res=setcookie($v, "", mktime(12,0,0,1, 1, 1970), '/', $domain, isset($_SERVER["HTTPS"]), false );
  setcookie($v,FALSE, $dt );
 }
}

if ( !function_exists('uncookjs') ) {
 function uncookjs( $v ) {
     return '
<script type="text/javascript">
 var cookie_date = new Date();
 cookie_date.setTime( cookie_date.getTime() -1 );
 document.cookie = "'.$v.'=; expires="+ cookie_date.toGMTString();
</script>
';
 }
}
if ( !function_exists('show_cookie') ) {
 function show_cookie( ) {
  var_dump($_COOKIE);
 }
}

if ( !function_exists('redirect') ) {
 function redirect( $url, $delay = 0 ) {
  if ( $delay == 0 )
   echo '<script type="text/javascript"> window.location = "' . $url . '"; </script>';
  else
   echo '<script type="text/javascript"> function delayer(){ window.location = ' . "'"
        . $url . "'; } setTimeout('delayer()', '" . $delay . "'" . '); </script>';
 }
}

if ( !function_exists('Post') ) {
 function Post($url,$fields) {
  $fields_string="";
  //url-ify the data for the POST
  foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
  rtrim($fields_string, '&');
  //open connection
  $ch = curl_init();
  //set the url, number of POST vars, POST data
  curl_setopt($ch,CURLOPT_URL, $url);
  curl_setopt($ch,CURLOPT_POST, count($fields));
  curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
  curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
  //execute post
  $result = curl_exec($ch);
  //close connection
  curl_close($ch);
  return $result;
 }
}

if ( !function_exists('LoadDatastore') ) {
 function LoadDatastore($filename) {
  if ( !file_exists($filename) ) return array();
  $datastore=array();
  $file=file_get_contents($filename);
  $lines=explode(PHP_EOL,$file);
  foreach ( $lines as $line ) if ( strlen(trim($line)) > 0 ) {
   $part=explode("#|#|#",$line);
   $datastore[$part[0]]=$part[1];
  }
  return $datastore;
 }
}

if (!function_exists('SaveDatastore') ) {
 function SaveDatastore($datastore,$filename) {
  // Save the updated datastore.
  $out="";
  foreach( $datastore as $k=>$v ) $out.=$k."#|#|#".$v.PHP_EOL;
  file_put_contents($filename,$out);
 }
}

if ( !function_exists('cash_format') ) {
 function cash_format( $number ) {
  $f=floatval($number);
  setlocale(LC_MONETARY, 'en_US');
  return money_format('%i', $f);
 }
}

if ( !function_exists('human_filesize') ) {
 function human_filesize($size,$unit="") {
  if( (!$unit && $size >= 1<<30) || $unit == "Gb")
    return number_format($size/(1<<30),2)." Gb";
  if( (!$unit && $size >= 1<<20) || $unit == "mb")
    return number_format($size/(1<<20),2)." mb";
  if( (!$unit && $size >= 1<<10) || $unit == "k")
    return number_format($size/(1<<10),2)." k";
  return number_format($size)." bytes";
 }
}

if ( !function_exists('create_thumbnail') ) {
 function create_thumbnail( $name, $pathToImages, $pathToThumbs, $thumbWidth ) {
  $info = pathinfo($pathToImages . $fname);
  if ( matches($info['extension'],'jpg') || matches($info['extension'],'png') ) {
   $img = imagecreatefromjpeg( $pathToImages.$fname );
   $width = imagesx( $img );
   $height = imagesy( $img );
   $new_width = $thumbWidth;
   $new_height = floor( $height * ( $thumbWidth / $width ) );
   $tmp_img = imagecreatetruecolor( $new_width, $new_height );
   imagecopyresized( $tmp_img, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height );
   if ( matches($info['extension'],'jpg') ) imagejpeg( $tmp_img, $pathToThumbs.$fname );
   else if ( matches($info['extension'],'png') ) imagepng( $tmp_img, $pathToThumbs.$fname );
  }
 }
}


if ( !function_exists('slugify') ) {
 function slugify( $a ) {
  $slug='';
  $a=explode(',',$a);
  foreach ( $a as $tag ) {
   $tag=str_replace(' ','-',trim($tag));
   $slug.=$tag.'-';
  }
  return rtrim($slug,'-');
 }
}

if ( !function_exists('parse_ini_m') ) {
 function parse_ini_m($str) {
  if(empty($str)) return false;
  $lines = explode("\n", $str);
  $ret = Array();
   $inside_section = false;
  foreach($lines as $line) {
   $line = trim($line);
   if(!$line || $line[0] == "#" || $line[0] == ";") continue;
   if($line[0] == "[" && $endIdx = strpos($line, "]")){
    $inside_section = substr($line, 1, $endIdx-1);
    continue;
   }
   if(!strpos($line, '=')) continue;
   $tmp = explode("=", $line, 2);
   if($inside_section) {
     $key = rtrim($tmp[0]);
     $value = ltrim($tmp[1]);
     if(preg_match("/^\".*\"$/", $value) || preg_match("/^'.*'$/", $value)) {
      $value = mb_substr($value, 1, mb_strlen($value) - 2);
     }
     $t = preg_match("^\[(.*?)\]^", $key, $matches);
     if(!empty($matches) && isset($matches[0])) {
       $arr_name = preg_replace('#\[(.*?)\]#is', '', $key);
       if(!isset($ret[$inside_section][$arr_name]) || !is_array($ret[$inside_section][$arr_name])) {
        $ret[$inside_section][$arr_name] = array();
       }
       if(isset($matches[1]) && !empty($matches[1])) {
        $ret[$inside_section][$arr_name][$matches[1]] = $value;
       } else {
        $ret[$inside_section][$arr_name][] = $value;
       }
     } else {
      $ret[$inside_section][trim($tmp[0])] = $value;
     }
   } else {
    $ret[trim($tmp[0])] = ltrim($tmp[1]);
   }
  }
  return $ret;
 }
}

if ( !function_exists('multiexplode') ) {
 function multiexplode($delims,$string) {
  $ready = str_replace($delims, $delims[0], $string);
  $launch = explode($delims[0], $ready);
  return  $launch;
 }
}

if ( !function_exists('int_in') ) {
 function int_in($i,$a) {
  $occurs=0;
  $i=intval($i);
  foreach ( $a as $j ) {
   $j=intval($j);
   if ( $j === $i ) $occurs++;
  }
  return $occurs;
 }
}

if ( !function_exists('starts_with') ) {
 // search backwards starting from haystack length characters from the end
 function starts_with( $haystack, $needle ) {
  return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
 }
}

if ( !function_exists('tagged') ) {
 function tagged( $string_or_array_tags, $string_or_array_required, $delim=',' ) {
  if ( is_string($string_or_array_required) ) $required=words($string_or_array_required,$delim);
  else $required=$string_or_array_required;
  if ( is_string($string_or_array_tags) ) $tags=words($string_or_array_tags,$delim);
  else $tags=$string_or_array_tags;
  $found=0;
  foreach ( $required as $word ) {
   foreach ( $tags as $tag )
    if ( matches($tag,$word) ) { $found+=1; break; }
  }
  return $found === count($required) ? TRUE : FALSE;
 }
}

if ( !function_exists('filter_tags') ) {
 function filter_tags($tags, $supported, $delim=',') {
  $out=array();
  $words=words($tags,$delim);
  foreach ( $words as $w ) if ( tagged($supported,$w) ) $out[]=$w;
  return $out;
 }
}

if ( !function_exists('filtered_tags') ) {
 function filtered_tags($tags, $supported, $delim=',') {
  $out=array();
  $words=words($tags,$delim);
  foreach ( $words as $w ) if ( tagged($supported,$w) ) $out[]=$w;
  return implode($delim,$out);
 }
}

if ( !function_exists('string_argument') ) {
  function _FILLER($c) { return ( $c === ' ' || $c === ',' || $c === '=' || $c === "\n" || $c === "\r" || $c === "\t" ); }
  function _SEP($c) { return ( $c === "'" || $c === '"' ); }
  function _NESTERS($c) { return ( $c === '{' || $c === '[' || $c === '(' ); }
  function _NESTERE($c) { return ( $c === '}' || $c === ']' || $c === ')' ); }
  function char_in( $c, $list ) { $O=strlen($list); for ( $o=0; $o<$O; $o++ ) if ( $list[$o] === $c ) return TRUE; return FALSE; }
  function string_argument( &$arg1, $argument, $arg ) {
   $cEnd = ' '; $arg1="";
   // Advance past spaces and interim commas, equal signs, newlines, skip #comments
   while ( isset( $argument[$arg]) && (_FILLER($argument[$arg]) || $argument[$arg] === '#') ) {
    if ( $argument[$arg] === '#' ) {
     while ( $argument[$arg] !== '\n' && $argument[$arg] !== '\r' && $arg < strlen($argument) ) $arg++;
    } else $arg++;
   }
   // Handle nested {} [] (), or quotes "" '' ``
   if ( isset($argument[$arg])
     && ( _NESTERS($argument[$arg]) || _SEP($argument[$arg]) ) ) {
    $nests=1;
    $cStart=$argument[$arg];
    $arg++;
    switch ( $cStart ) {
     case '{': $cEnd = '}'; break;
     case '[': $cEnd = ']'; break;
     case '(': $cEnd = ')'; break;
     case "'": $cEnd = "'"; break;
     case '"': $cEnd = '"'; break;
     case '`': $cEnd = '`'; break;
    }
    while ( $arg < strlen($argument) && $nests > 0 ) {
     if ( $argument[$arg] === $cEnd[0] ) {
      $nests--;
      if ( $nests == 0 ) break;
     } else if ( $argument[$arg] === $cStart[0] ) $nests++;
     $arg1.=($argument[$arg]);
     $arg++;
    }
    $arg++;
   } else {
    while ( $arg < strlen($argument) ) {
     if ( char_in( $argument[$arg], "\n\r[{(,= " ) === TRUE ) break;
     $arg1.=$argument[$arg]; $arg++;
    }
   }
   // Advance past spaces and interim commas, equal signs, newlines, skip #comments
   while ( isset($argument[$arg])
        && ( _FILLER($argument[$arg]) || $argument[$arg] === '#' ) ) {
    if ( $argument[$arg] === '#' ) {
     while ( $argument[$arg] !== '\n' && $argument[$arg] !== '\r' && $arg < strlen($argument) ) $arg++;
    } else $arg++;
   }
   return $arg;
  }
  function pop_first_word( $place, $string, &$out ) { $place=string_argument($out,$string,$place); return $place; }
  function pop_word ( $in, &$out ) {
   if ( strlen(trim(str_replace(","," ",$in))) == 0 ) return '';
   $out=array();
   $total=0;
   $result=array();
   $length=strlen($in);
   $place=0;
   while ( $place < $length ) {
    $place=pop_first_word($place,$in,$out);
    $remaining=substr($in,$place);
    return $remaining;
   }
   return '';
  }
  function pop_words ( $in ) {
   $out=array();
   $total=0;
   $result=array();
   $length=strlen($in);
   $place=0;
   while ( $place < $length ) {
    $place=pop_first_word($place,$in,$word);
    $out[]=$word;
   }
   return $out;
  }
}

if ( !function_exists('ends_with') ) {
 function ends_with( $str, $key ) {
  return (
   strlen($str) - strlen($key) == strrpos($str,$key)
   ? TRUE : FALSE
  );
 }
}


if ( !function_exists('death') ) {
 function death($err) { echo $err.' --> Exitting.'.PHP_EOL; die; }
}

if ( !function_exists('array_average') ) {
 function array_average( $arr ) {
  $t=0;
  foreach ( $arr as $v ) $t+=$v;
  if (count($arr) === 0) return 0;
  return floatval($t)/count($arr);
 }
}

if ( !function_exists('minutes') ) {
 function minutes( $seconds ) {
  return $seconds.' seconds ('.($seconds/60.0).' minutes)';
 }
}

if ( !function_exists('string_fit') ) {
 function string_fit( $str, $size ) {
  $d=$size-strlen($str);
  $out=$str;
  for ( ; $d > 0; $d-- ) $out.=' ';
  return $out;
 }
}

if ( !function_exists('array_format') ) {
 function array_format( $f ) {
  $out='';
  foreach ( $f as $i=>$e ) $out.=string_fit($i,20).$e.PHP_EOL;
  return $out;
 }
}

// Works only in automation.php mode (cli SAPI)
if ( !function_exists('ask_to_continue') ) {
 function ask_to_continue() {
  echo "Are you sure you want to do this?  Type 'yes' to continue: ";
  $handle = fopen ("php://stdin","r");
  $line = fgets($handle);
  if(trim($line) != 'yes'){
     echo "ABORTING!\n";
     exit;
  }
  echo "\n";
  echo "Thank you, continuing...\n";
 }
}

// Works only in automation.php mode (cli SAPI)
if ( !function_exists('ask_for_input') ) {
 function ask_for_input( $trim_it=TRUE ) {
  $handle = fopen ("php://stdin","r");
  $line = fgets($handle);
  if ( $trim_it === TRUE ) return trim($line);
  else return $line;
 }
}


if ( !function_exists('explode_trim') ) {
 function explode_trim($sep,$hay) {
  $res=explode($sep,$hay);
  $fin=array();
  foreach ( $res as $r ) $fin[]=trim($r);
  return $fin;
 }
}

if ( !function_exists('explode_trim_drop_empty') ) {
 function explode_trim_drop_empty($sep,$hay) {
  $res=explode($sep,$hay);
  $fin=array();
  foreach ( $res as $r ) {
   $nex=trim($r);
   if ( strlen($nex) > 0 ) $fin[]=$nex;
  }
  return $fin;
 }
}


if ( !function_exists('kv_from_array') ) {
 function kv_from_array($array) {
  $kv=array();
  $key=false;
  foreach ( $array as $part ) { // key,value,key,value = key:value,key:value
   if ( !$key ) $key=$part;
   else {
    $kv[$key]=$part;
    $key=false;
   }
  }
//  var_dump($kv);
  return $kv;
 }
}

if ( !function_exists('array_from_kv') ) {
 function array_from_kv($array) {
  $out=array();
  foreach ( $array as $k=>$v ) { // key,value,key,value = key:value,key:value
   $out[]=$k;
   $out[]=$v;
  }
  return $out;
 }
}

if ( !function_exists('depipe') ) {
 function depipe($data) {
  $data=explode("|",$data);
  return kv_from_array($data);
 }
}

if ( !function_exists('enpipe') ) {
 function enpipe($arr) {
  return implode("|",array_from_kv($arr));
 }
}

if ( !function_exists('death') ) {
 function death( $msg ) {
  echo $msg.PHP_EOL; die;
 }
}

if ( !function_exists('json_post') ) {
 function json_post( $url, $array_data, $dump=FALSE ) {
  $data_string = json_encode($array_data);
  $curl = curl_init($url);
  curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
  curl_setopt($curl, CURLOPT_POST, true);
  curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($curl, CURLOPT_HEADER, false);
  curl_setopt($curl, CURLOPT_FAILONERROR, true);
  curl_setopt($curl, CURLOPT_VERBOSE, true);
  curl_setopt($curl, CURLOPT_STDERR, $verbose = fopen('php://temp','rw+'));
  curl_setopt($curl, CURLOPT_HTTPHEADER, array(
   'Content-Type: application/json',
   'Content-Length: ' . strlen($data_string)
  ));
  $response = curl_exec($curl);
  echo "Verbose information:\n", !rewind($verbose), stream_get_contents($verbose), "\n";
  curl_close($curl);
  echo 'json_post("'.$url.'") => RESPONSE: ';
  if ( $dump !== FALSE ) var_dump($response);
  if ( stripos($response,"</pre>") !== FALSE ) { // Cake barfed
   $parts=explode("</pre>",$response);
   $response=$parts[count($parts)-1];
  }
  return $response;
 }
}

if ( !function_exists('make_path') ) {
 function make_path($pathname, $mode=0777, $is_filename=false){
  plog("make_path($pathname,$mode$is_filename)");
  return mkdir($pathname,$mode,true);
/*
  $pathname = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $pathname);
  if($is_filename) $pathname = substr($pathname, 0, strrpos($pathname, DIRECTORY_SEPARATOR));
  // Check if directory already exists
  if (is_dir($pathname) || empty($pathname)) return TRUE;
  // Ensure a file does not already exist with the same name
  if (is_file($pathname)) {
   trigger_error('make_path(`'.$pathname.'`) File exists on way to forming the path, cannot proceed', E_USER_ERROR);
   return FALSE;
  }
  // Crawl up the directory tree
  $next_pathname = substr($pathname, 0, strrpos($pathname, DIRECTORY_SEPARATOR));
  if (make_path($next_pathname, $mode) !== FALSE ) {
   if (strlen(trim($pathname))>0
    && !file_exists($pathname)) return mkdir($pathname, $mode);
  }
  return FALSE;
*/
 }
}

if ( !function_exists('remove_path') ) {
 function remove_path($path) {
  plog("remove_path($path)");
  $files = glob(rtrim($path,'/') . '/*');  /**/
  foreach ($files as $file) is_dir($file) ? remove_path($file) : unlink($file);
  return rmdir($path);
 }
}

if ( !function_exists('folder_index_json') ) {
 function folder_index_json($dir, $allow_delete=FALSE, $filter_index=TRUE) {
  $inodes=scandir($dir);
  $file=array();
  foreach ( $inodes as $index=>$pathname ) {
   if ( $filter_index === TRUE && $pathname == "index.php" ) continue;
   if ( $allow_delete !== FALSE && $pathname == $_GET['DELETE'] ) { unlink($pathname); return TRUE; }
   if ( file_exists($pathname) && !is_dir($pathname) )
    $file[]=array(
     "name"=>$pathname,
     "size"=>filesize($pathname),
     "mtime"=>filemtime($pathname)
    );
   }
   return json_encode($file);
  }
}

if ( !function_exists('b64k_decode') ) {
 function b64k_decode( $n ) {
  return base64_decode(str_replace('-','+',str_replace('_','/',$n)));
 }
}

if ( !function_exists('b64k_encode') ) {
 function b64k_encode( $n ) {
  return str_replace('+','-',str_replace('/','_',base64_encode($n)));
 }
}

if ( !function_exists('b64k_json_decode') ) {
 function b64k_json_decode( $n ) {
  return json_decode(base64_decode(str_replace('-','+',str_replace('_','/',$n))),true);
 }
}

if ( !function_exists('b64k_json_encode') ) {
 function b64k_json_encode( $n ) {
  return str_replace('+','-',str_replace('/','_',base64_encode(json_encode($n))));
 }
}

if ( !function_exists('special_decode') ) {
 // Decodes our special formatted request
 // the opposite of the str_replaces, base64 encoded, pipe-delimited key|value|... pairs
 function special_decode($data) {
  $data=explode("|",b64k_decode($data));
  return kv_from_array($data);
 }
}

if ( !function_exists('str_replace_array') ) {
 function str_replace_array($mappings,$haystack) {
  $out=$haystack;
  foreach ( $mappings as $search=>$replace ) $out=str_replace($search,$replace,$haystack);
  return $out;
 }
}

if ( !function_exists('seconds_old')) {
 function seconds_old( $filename ) {
  if ( !file_exists($filename) ) return FALSE;
  return strtotime('now')-filemtime($filename);
 }
}

if ( !function_exists('href') ) {
 function href( $url, $content ) { return '<a href="'.$url.'">'.$content.'</a>'; }
}

if ( !function_exists('is') ) {
 function is( $a, $b ) {
  if ( is_array($b) ) {
   foreach ( $b as $v ) if ( is($a,$v) ) return TRUE;
   return FALSE;
  }
  $a = trim($a);
  $b = trim($b);
  if ( strlen($a) != strlen($b)) return FALSE;
  if ( stripos($a,$b) === 0 ) return TRUE;
  return FALSE;
 }
}


if ( !function_exists('stringval') ) {
 function stringval( $x ) { return "".$x; }
}

if ( !function_exists("RemoveKeys") ) {
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
}

if ( !function_exists("OnlyKeys") ) {
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
}


if ( !function_exists("searchArraySubstr") ) {
 /**
  *  @brief Searches for substring in given array of strings
  *  @param Array[string] array to search
  *  @param string substring to search for
  *  @return int index of string where substring is found
  */
 function searchArraySubstr($array, $substring)
 {
  for ($i = 0; $i < count($array); $i++) {
    if ( strpos($array[$i], $substring) !== false) {
      return $i;
    }
  }
  // Substring not in array
  return -1;
 }
}



if ( !function_exists('http_post') ) {
 function http_post($url,$fields,$files=NULL) {
  // build the urlencoded data
  $post = $fields;
  if ( is_array($files) ) foreach ( $files as $field=>$filename ) {
   $post[$field]=new CURLFile($filename,mime_content_type($filename));
  }
  // open connection
  $ch = curl_init();
  // set the url, number of POST vars, POST data
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
  curl_setopt($ch, CURLOPT_SAFE_UPLOAD, 1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_TIMEOUT, 120);
  // execute post
  $result = print_r($post,TRUE).PHP_EOL."http_post".curl_exec($ch);
  // close connection
  curl_close($ch);
  return $result;
 }
}


if ( !function_exists('limit_str_to') ) {
  function limit_str_to( $s, $lenlimit=40, $strlen=NULL ) {
   if ( $strlen === NULL ) $strlen=strlen($s);
   $len=$lenlimit-3;
   if ( $strlen > $len ) $s=substr($s,0,$len).'...'.substr($s,0,-10);
   return $s;
  }
}


if ( !function_exists("GetStartOfDay") ) {
  function GetStartOfDay( $day, $month, $year ) {
   plog("GetStartOfDay($day,$month,$year)");
   $d=DateTime::createFromFormat("d/m/Y H:i:s",$day.'/'.$month.'/'.$year.' 00:00:00');
   return false_or_null($d) ? NULL : $d->getTimestamp();
  }
}

if ( !function_exists("GetEndOfDay") ) {
  function GetEndOfDay( $day, $month, $year ) {
   plog("GetEndOfDay($day,$month,$year)");
   $d=DateTime::createFromFormat("d/m/Y H:i:s",$day.'/'.$month.'/'.$year.' 23:59:59');
   return false_or_null($d) ? NULL : $d->getTimestamp();
  }
}

if ( !function_exists("GetDayRange") ) {
  function GetDayRange( $day, $month, $year ) {
   plog("GetDayRange:");
   return array(
    "start"=>GetStartOfDay($day,$month,$year),
    "end"=>GetEndOfDay($day,$month,$year)
   );
  }
}

if ( !function_exists("DMYtoArray") ) {
  function DMYtoArray( $dmy, $numeric=FALSE ) {
   plog("DMYtoArray($dmy)");
   $parts=explode("/",str_replace("-","/",$dmy));
   if ( count($parts) != 3 ) return FALSE;
   if ( $numeric === FALSE )
   return array( "day"=>$parts[0], "month"=>$parts[1], "year"=>$parts[2] );
   else
   return $parts;
  }
}

if ( !function_exists("DMYfromArray") ) {
  function DMYfromArray( $arr, $numeric=FALSE ) {
   plog("DMYfromArray(".vars($arr).")");
   if ( $numeric === FALSE )
   return $arr["day"].'/'.$arr["month"].'/'.$arr["year"];
   else
   return $arr[0].'/'.$arr[1].'/'.$arr[2];
  }
}

if ( !function_exists("DMY") ) {
  function DMY( $day, $month, $year ) { return $day.'/'.$month.'/'.$year; }
}

if ( !function_exists("TimestampToDMY") ) {
  function TimestampToDMY( $ts ) {
   $date = new DateTime();
   $date->setTimestamp($ts);
   $date = $date->format("d/m/Y");
   $parts = explode("/",$date);
   return array( "day"=>intval($parts[0]), "month"=>intval($parts[1]), "year"=>intval($parts[2]) );
  }
}

if ( !function_exists("DMYToTimestamp") ) {
  function DMYToTimestamp( $day,$month,$year ) {
   return GetStartOfDay($day,$month,$year);
  }
}

if ( !function_exists("GetStartOfWeek") ) {
 function GetStartOfWeek( $ts, $start="Sunday" ) {
  $date = new DateTime();
  $date->setTimestamp($ts);
  $then=$date->format("w");
  if ( ($then == 0 && is($start,"Sunday"))
    || ($then == 1 && is($start,"Monday")) ) $dmy=TimestampToDMY($ts);
  else {
   $then=strtotime("last $start",$ts);
   $dmy=TimestampToDMY($then);
  }
  return GetStartOfDay($dmy["day"],$dmy["month"],$dmy["year"]);
 }
}

if ( !function_exists("GetEndOfWeek") ) {
 function GetEndOfWeek($ts,$start="Sunday") { return GetStartOfWeek($ts,$start) + 604800 - 1; }
}

if ( !function_exists("GetWeekRange") ) {
  function GetWeekRange( $day, $month, $year ) {
   plog("GetWeekRange:");
   return array(
    "start"=>GetStartOfWeek(DMYToTimestamp($day,$month,$year)),
    "end"=>GetEndOfWeek(DMYToTimestamp($day,$month,$year))
   );
  }
}

if ( !function_exists("GetStartOfMonth") ) {
 function GetStartOfMonth($ts) {
  $date = new DateTime();
  $date->setTimestamp($ts);
  $month=intval($date->format("m"));
  $year=intval($date->format("Y"));
  return GetStartOfDay(1,$month,$year);
 }
}

if ( !function_exists("GetEndOfMonth") ) {
 function GetEndOfMonth($ts) {
  $date = new DateTime();
  $date->setTimestamp($ts);
  $month=intval($date->format("m"));
  $year=intval($date->format("Y"));
  switch ( $month ) {
   case 9: case 4: case 6: case 11: $ending=30; break;
   default: $ending=31; break;
   case 12: if ($year%4==0) $ending=29; else $ending=28; break;
  }
  return GetEndOfDay($ending,$month,$year);
 }
}

if ( !function_exists("GetMonthRange") ) {
  function GetMonthRange( $day, $month, $year ) {
   plog("GetMonthRange:");
   return array(
    "start"=>GetStartOfMonth(DMYToTimestamp($day,$month,$year)),
    "end"=>GetEndOfMonth(DMYToTimestamp($day,$month,$year))
   );
  }
}

// geometry etc

if ( !function_exists("squared") ) {
 function squared($a) { return $a*$a; }
}

if ( !function_exists("cubed") ) {
 function cubed($a) { return $a*$a*$a; }
}

if ( !function_exists("circle_area") ) {
 function circle_area( $radius ) {
  return 2.0*pi()*squared($radius);
 }
}

if ( !function_exists("sphere_volume") ) {
 function sphere_volume( $radius ) {
  return (4.0/3.0)*pi()*cubed($radius);
 }
}

