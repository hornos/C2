<?php
require_once( "../php/h.php" );

c2Req::jex( new Exception( "nini" ) );

/*
try {
  $site = new coSite();
  $site->rpc( coRequest::request( 'cmd', 'disabled' ) );
} catch( Exception $e ) {
  coRequest::jexception( $e );
}
*/
?>
