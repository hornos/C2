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

  public function rpc( $m = NULL, $a = NULL ) {
    try {
      $d = parent::rpc( $m, $a );
    } catch( Exception $e ) {
      return c2Req::jex( $e );
    }
    return c2Req::jre( $d );
  }
}

?>
