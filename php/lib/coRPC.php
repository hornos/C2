<?php

class coRPC {
  public function __construct() { }

  protected function _rpc( $m = NULL, $argv = NULL ) {
    if( ! method_exists( $this, $m ) )
      throw new c2Ex( __CLASS__ . '::' . $method );

    return empty( $argv ) ? $this->$method() : $this->$method( $argv );
  }

  protected function _rpc_test() { return __METHOD__ . ' OK'; }


  public function rpc( $method = NULL, $argv = NULL ) {
    if( empty( $method ) ) throw new coRPCEx( __METHOD__ );

    return $this->_rpc( '_rpc_' . $method, $argv );
  }
}

?>
