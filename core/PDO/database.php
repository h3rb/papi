<?php

/*
 *  Copyright (c) 2009 H. Elwood Gilliland III
 *  New BSD License
 */

//define('TEST',1);

class Database extends PDO {
 var
  $errors, $query, $prepared, $result, $driver, $driver_code, $driver_codes;
 private $errorFunction;

 public function __construct($d, $u="", $p="",$driver='mysql') {
  $o = array(
   PDO::ATTR_PERSISTENT => true,
   PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
  );
  try { parent::__construct($d, $u, $p, $o); }
  catch (PDOException $e) {
   plog('Database::Error in PDO/database.php');
   $this->errors[] = $e->getMessage();
   plog($e->getMessage());
   plog($d);
   plog($u);
   plog('(password hidden)');
   plog($driver);
   return null;
  }
  $result="";
  $this->errors=array();
  $this->driver_codes = array( 1=>'sqlite', 2=>'mysql' /* ... */ );
  if ( count($this->errors) == 0 ) {
   $this->driver = $driver; // didn't work: $this->getAttribute(PDO::ATTR_DRIVER_NAME);
   switch ( $this->driver ) {
    case 'sqlite': $this->driver_code = 1; break;
     case 'mysql': $this->driver_code = 2; break;
          default: $this->driver_code = 0; break;
   }
  } else {
   $this->driver='error';
   $this->driver_code=-1;
  }
 }

 // Debug messages
 private function Debug() {
  if(!empty($this->errorFunction)) {
   if(!empty($this->query)) $error["Query"] = $this->query;
   if(!empty($this->prepared))
   $error["Prepared"] = trim(print_r($this->prepared, true));
   $error["Backtrace"] = debug_backtrace();
   $this->errors[]=$error;
    $func = $this->errorCallbackFunction;
    $func($error);
  } else {
   global $plog_level;
   if ( $plog_level == 1 ) {
    plog('DB ERROR: '.vars($this->errors));
    $this->errors=array();
   }
  }
 }

 function Tables( $db_name ) {
  $q='SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_TYPE="BASE TABLE" AND TABLE_SCHEMA="'.$db_name.'";';
  $tables=$this->Run($q);
  if ( is_array($tables) ) {
   $out=array();
   foreach ($tables as $t) {
    $out[]=$t['TABLE_NAME'];
   }
   return $out;
  } else return FALSE;
 }
 
 // Get desired columns from a table
 function Fields($table, $filter=false) {
  switch ( $this->driver_code ) {
   case -1: return array( 'error'=>1 );
   case 1:
    $query = "PRAGMA table_info('" . $table . "');";
    $key = "name";
   break;
   case 2:
    $query = "DESCRIBE " . $table . ";";
    $key = "Field";
   break;
   default:
    $query = "SELECT column_name FROM information_schema.columns WHERE table_name = '" . $table . "';";
    $key = "column_name";
   break;
  }
  $this->result = $this->Run($query);
  if ( $this->result !== false ) {
   $columns = array();
   foreach($this->result as $record) $columns[] = $record[$key];
   if ( $filter !== false )
    return array_values(array_intersect($columns, array_keys($filter)));
   else return $columns;
  }
  return array();
 }

 // Converts a single word into an array for the special case of the prepared clause
 private function Clean($prepared) {
  if(!is_array($prepared)) {
   if(!empty($prepared)) $prepared = array($prepared);
   else $prepared = array();
  }
  return $prepared;
 }

 private function implode_fields( $data ) {
  return implode(", ",$data);
 }

 public function index_values( $values ) {
  $out=array();
  foreach ( $values as $k=>$v ) $out[]=$v;
  return $out;
 }

 private function implode_values( $values ) {
  return implode(", :",$values);
 }

 // Insert into $table using $data= array ( 'field'=>value )
 public function Insert($table, $data) {
  plog('db->Insert: table='.$table.', $data='.vars($data));
  $query = "INSERT INTO " . $table
     . " (" . $this->implode_fields($columns=array_keys($data))
     . ") VALUES (:" . $this->implode_values($columns) . ");";
  $prepared = array();
  foreach($columns as $field) $prepared[":$field"] = $data[$field];
  $this->result=$this->Run($query, $prepared);
  plog("Prepared: ".str_replace("\n","",vars($prepared)));
  if ( !$this->result ) {
   $last_err=$this->errors[count($this->errors)-1];
   plog("INSERT errors: ".vars($last_err));
   plog("Latest QUERY was: ".vars($this->query));
  }
  return $this->lastInsertId();
 }

 public function Run($query, $prepared="") {
  plog("Query: ".$query);
  $this->query = trim($query);
  $this->prepared = $this->Clean($prepared);
  if ( defined('TEST') ) {
   return $this->query;
  }
  try {
   $pdo = $this->prepare($this->query);
   if($pdo->execute($this->prepared) !== false) {
    if(preg_match("/^(" . implode("|", array("select", "describe", "pragma")) . ") /i", $this->query))
     return $pdo->fetchAll(PDO::FETCH_ASSOC);
    elseif(preg_match("/^(" . implode("|", array("delete", "insert", "update")) . ") /i", $this->query))
     return $pdo->rowCount();
   }
  } catch (PDOException $e) {
   $this->errors[] = $e->getMessage();
   $this->Debug();
   return false;
  }
 }

 public function Where( $array, $include_where=TRUE ) {
  if ( count($array) === 0 ) return '';
  if ( $include_where === TRUE ) $clause=' WHERE ';
  else $clause='';
  $i=0;
  foreach ( $array as $k=>$v ) {
   $clause.=($i!=0 ? ' AND ' : '') . $k . '=' . $this->quote($v);
   $i++;
  }
  return $clause;
 }

 public function Latest( $table, $where, $prepared="", $columns="*" ) {
  $results = $this->Select($table, $where, $prepared, $columns, "ORDER BY ID DESC" );
  if ( false_or_null($results) ) return FALSE;
  if ( is_array($results) && count($results) > 0 && is_sequent($results) ) return array_pop($results);
  return FALSE;
 }

 public function SelectGroup( $table, $id_array, $prepared="", $columns="*", $order_by='', $limit='' ) {
  $where="";
  $count=count($id_array);
  if ( !is_sequent($id_array) ) return FALSE;
  if ( $count === 0 ) return array();
  $i=0;
  foreach ( $id_array as $id ) {
   $where.='($table.ID=$id)'.($i<$count ? " OR " : "");
   $i++;
  }
  return $this->Select( $table, $where, $prepared, $columns, $order_by, $limit );
 }

 public function SelectBetweenEqual($table, $field, $low, $high, $columns='*', $order_by='', $limit='' ) {
  return $this->Select(
    $table,
    (' ( ('.$field.' >= '.$low.') AND ('.$field.' <= '.$high.') ' ),
    '',$columns,$order_by,$limit);
 }

 public function SelectBetween($table, $field, $low, $high, $columns='*', $order_by='', $limit='' ) {
  return $this->Select(
    $table,
    (' ( ('.$field.' > '.$low.') AND ('.$field.' < '.$high.') ) ' ),
    '',$columns,$order_by,$limit);
 }

 public function SelectWhereBetweenEqual($table, $field, $low, $high, $where_clause='', $columns='*', $order_by='', $limit='' ) {
  if ( is_array($where_clause) ) $where=Database::Where($where_clause,FALSE);
  else if(!empty($where_clause)) $where=$where_clause;
  return $this->Select(
    $table,
    (' ( ('.$field.' >= '.$low.') AND ('.$field.' <= '.$high.') AND ('.$where.') ) ' ),
    '',$columns,$order_by,$limit);
 }

 public function SelectWhereBetween($table, $field, $low, $high, $where_clause='', $columns='*', $order_by='', $limit='' ) {
  if ( is_array($where_clause) ) $where=Database::Where($where_clause,FALSE);
  else if(!empty($where_clause)) $where=$where_clause;
  return $this->Select(
    $table,
    (' ( ('.$field.' > '.$low.') AND ('.$field.' < '.$high.') AND ('.$where.') ) ' ),
    '',$columns,$order_by,$limit);
 }

 public function Select($table, $where_clause="", $prepared="", $columns="*", $order_by='', $limit='') {
  $query = "SELECT " . $columns . " FROM " . $table;
  if ( is_array($where_clause) ) $query.=Database::Where($where_clause);
  else if(!empty($where_clause)) $query .= " WHERE " . $where_clause;
  $query .= (strlen($order_by)>0?(' ' . $order_by):'');
  if ( is_numeric($limit) && intval($limit>0) || strlen(trim($limit)) > 0 ) $query .= ' LIMIT '.$limit;
  $query .= ";";
  $this->result = $this->Run($query, $prepared);
  return $this->result;
 }
 public function SelectOR($table, $where_clause="", $prepared="", $columns="*") {
  $query = "SELECT " . $columns . " FROM " . $table;
  if ( is_array($where_clause) ) $query.=Database::Where($where_clause);
  else if(!empty($where_clause)) $query .= " WHERE " . $where_clause;
  $query .= ";";
  $this->result = $this->Run($query, $prepared);
  return $this->result;
 }
 
  public function Join( $values, $tableA, $tableB, $order_by=FALSE, $columns='*', $limit=FALSE, $offset=0, $where=FALSE, $type='INNER', $on_or_using="ON" ) {
  if ( is_array($tableA) ) $tableA=implode(',',$tableA);
  if ( is_array($tableB) ) $tableB=implode(',',$tableB);
  if ( is_array($values) ) {
   $value=array();
   foreach ( $values as $a=>$b ) {
    $value[]=$a.'='.$b;
   }
   $value=implode(',',$value);
  } else $value=$values;
  $ending='';
  if ( !false_or_null($where) ) {
   if ( is_array($where) ) $ending.=$this->where;
   else $ending.=' WHERE ('.$where.')';
  }  
  if ( !false_or_null($order_by) && strlen($order_by)>0 ) {
   $ending.=' ORDER BY '.$order_by;
  }
  if ( !false_or_null($limit) ) {
   $limit=intval($limit);
   $ending.=' LIMIT '.$limit;
   if ( $offset > 0 ) $ending.=' OFFSET '.$offset;
  }
  $query='SELECT '.$columns.' FROM '.$tableA.' '.$type.' JOIN '.$tableB.' '.$on_or_using.' '.$value.$ending.';';
  $result=$this->Run($query);
  if ( false_or_null($result) ) return array();
  if ( count($result) == 1 ) return array_shift($result);
  return $result;
 }

 public function RunCountQuery($query, $prepared="") {
  plog("Query (counter): ".$query);
  $this->query = trim($query);
  $this->prepared = $this->Clean($prepared);
  if ( defined('TEST') ) {
   return $this->query;
  }
  try {
   $pdo = $this->prepare($this->query);
   if($pdo->execute($this->prepared) !== false) {
    $count=$result->fetch(PDO::FETCH_NUM);
    return $count[0];
   }
  } catch (PDOException $e) {
   $this->errors[] = $e->getMessage();
   $this->Debug();
   return false;
  }
 }
 public function Count($table, $where_clause="", $prepared="", $columns="count(*)" ) {
  $query = "SELECT " . $columns . " FROM " . $table;
  if ( is_array($where_clause) ) $query.=Database::Where($where_clause);
  else if(!empty($where_clause)) $query .= " WHERE " . $where_clause;
  $query .= ";";
  $this->result = $this->RunCountQuery($query, $prepared);
  return $this->result[0];
 }

 public function setErrorCallback($Function) {
  if ( in_array(strtolower($Function), array("echo", "print")) ) $Function = "print_r";
  if ( function_exists($Function) ) $this->errorFunction = $Function;
 }

 public function Update($table, $data, $where_clause, $prepared="") {
  $columns = array_keys($data);
  $size = count($columns);
  $query = "UPDATE " . $table . " SET ";
  for ( $f = 0; $f < $size; ++$f) {
   if($f > 0) $query .= ", ";
   $query .= $columns[$f] . " = :update_" . $columns[$f];
  }
  if ( is_array($where_clause) ) $query.=Database::Where($where_clause);
  else if(!empty($where_clause)) $query .= " WHERE " . $where_clause;
  $prepared = $this->Clean($prepared);
  foreach ($columns as $field) $prepared[":update_$field"] = $data[$field];
  $this->result = $this->Run($query, $prepared);
  plog("Prepared: ".str_replace("\n","",vars($prepared)));
  return $this->result;
 }

 // Delete from a table using a where clause
 public function Delete($table, $where_clause, $prepared="") {
  if ( !is_array($where_clause) ) { plog("HEY YOU DIDN'T MEAN TO DELETE EVERYTHING, RIGHT? (->Delete() requires array())"); return FALSE; }
  $query = "DELETE FROM " . $table;
  if ( is_array($where_clause) ) $query.=Database::Where($where_clause);
  else if(!empty($where_clause)) $query .= " WHERE " . $where_clause;
  $this->result = $this->Run($query, $prepared);
  return $this->result;
 }

  public function last_query_count() {
   return $this->query("SELECT FOUND_ROWS()")->fetchColumn();
  }

}

