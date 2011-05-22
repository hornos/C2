<?php
require_once( "../php/h.php" );
$uac = new c2UAC();
$uac->rpc( c2Req::get( 'c' ) );
?>
