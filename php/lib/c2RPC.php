<?php

class c2RPC {
  public function __construct() { }

  protected function _rpc( $m = NULL, $a = NULL ) {
    if( ! method_exists( $this, $m ) )
      throw new c2Ex( __CLASS__ . '::' . $m );

    return empty( $a ) ? $this->$m() : $this->$m( $a );
  }

  protected function _rpc_test() {
    return 'OK';
  }

  public function rpc( $m = NULL, $a = NULL ) {
    if( empty( $m ) )
      throw new c2Ex( __METHOD__ );

    return $this->_rpc( '_rpc_' . $m, $a );
  }
}

?>
