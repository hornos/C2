<?php
require_once( "../php/h.php" );

__k_def( 'C2_REQ_TYPE', 'POST' );

$uac = new c2UAC();
$uac->rpc( c2Req::get( 'c' ) );

?>
