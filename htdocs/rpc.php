<?php
require_once( "../php/h.php" );

c2Req::jex( new Exception( "nini" ) );

$uac = new c2UAC();
$uac->rpc( 'test', "ss" );
/*
try {
  $site = new coSite();
  $site->rpc( coRequest::request( 'cmd', 'disabled' ) );
} catch( Exception $e ) {
  coRequest::jexception( $e );
}
*/
?>
