<?php
require_once( "../php/h.php" );

c2Req::jex( new Exception( "nini" ) );

$uac = new c2UAC();
$uac->rpc( 'stop' );

?>
