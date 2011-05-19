<?php

class c2RPC {
  public function __construct() { }

  protected function _rpc( $m = NULL, $argv = NULL ) {
    if( ! method_exists( $this, $m ) )
      throw new c2Ex( __CLASS__ . '::' . $method );

    return empty( $argv ) ? $this->$m() : $this->$m( $argv );
  }

  protected function _rpc_test() {
    return __METHOD__ . ' OK';
  }

  public function rpc( $m = NULL, $argv = NULL ) {
    if( empty( $m ) )
      throw new c2Ex( __METHOD__ );

    return $this->_rpc( '_rpc_' . $m, $argv );
  }
}

?>
