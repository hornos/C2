<?php

class c2UAC extends c2RPC {
  public function __construct() {
    parent::__construct();
  }

  protected function _rpc_start() {
    $c2 = __k_fetch( 'c2' );
    $se = new c2Ses( $c2['sys.c'] );
    return $se->start();
  }

  protected function _rpc_stop() {
    $c2 = __k_fetch( 'c2' );
    $se = new c2Ses( $c2['sys.c'] );
    return $se->stop();
  }

  protected function _rpc_login() {
    $u = c2Req::get( 'u' );
    $p = c2Req::get( 'p' );
    if( empty( $u ) )
      throw new c2Ex( 'Empty user!' );
    if( empty( $p ) )
      throw new c2Ex( 'Empty password!' );

    $c2 = __k_fetch( 'c2' );
    $se = new c2Sys( $c2['sys.c'] );
    $se->start();
    try {
      return $se->login( $u, $p );
    } catch( Exception $e ) {
      $se->stop( false );
      throw $e;
    }
  }

  protected function _rpc_logout() {
    $c2 = __k_fetch( 'c2' );
    $se = new c2Sys( $c2['sys.c'] );
    $se->start();
    try {
      $se->logout();
    } catch( Exception $e ) {
      $se->stop( false );
      throw $e;
    }
    return $se->stop( false );
  }

  public function rpc( $m = NULL, $a = NULL, $s = true ) {
    // TODO: auth ( s - secure )
    try {
      $d = parent::rpc( $m, $a );
    } catch( Exception $e ) {
      return c2Req::jex( $e );
    }
    return c2Req::jre( $d );
  }
}

?>
