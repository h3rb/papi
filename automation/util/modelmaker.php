<?php

 $models=array(
  '_User',
  '_Session',
  '_Role',
  'User',
  'company',
  'contacts',
  'notes',
  'settings'
 );


 foreach ( $models as $m ) {
  if ( !file_exists( "../../model/$m.php" ) ) {
   $content='<?php'.PHP_EOL.' class $m extends Model {'.PHP_EOL.' };';
   file_put_contents("../../model/$m.php",$content);
  }
 }
