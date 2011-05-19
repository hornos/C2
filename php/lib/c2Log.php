<?php

class c2Log {
  private $__log;

  public function __construct( $log = NULL ) {
    if( empty( $log ) )
      throw new coEx( __METHOD__ );

    $log = __k_str( $log );
    $c2  = __k_fetch( "c2" );
    $this->__log = $c2['path.log'] . '/' . $log . '.log';
  }

  public function log( $s = '', $a = true ) {
    return file_put_contents( $this->__log, $s, $a ? FILE_APPEND | FILE_TEXT : FILE_TEXT );
  }
}

?>
