<?php
require_once( "../php/h.php" );

echo sha1('test');
$c2 = __k_fetch( "c2" );
#__k_r( $c2 );
# $l = new c2Log( "test" );
# $l->log( "test" );

$db = new c2PDB($c2["sys.c"]);
if( ! $db->Connect() ) {
  __k_die( "connection error" );
}
echo $db->time();
$db->Disconnect();
echo $db->time();
?>
