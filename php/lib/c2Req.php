<?php

__k_def( 'C2_REQ_LENGTH', 128 );
__k_def( 'C2_REQ_TYPE', 'POST' );
__k_def( 'C2_REQ_OBC', C2_JSON_OBC );

class c2Req {
  public function __construct() { }

  public static function req( $id = NULL, $def = NULL, $c = true, $s = true, $l = C2_REQ_LENGTH ) {
    $v = ( ! isset($_{C2_REQ_TYPE}[$id] ) or $_{C2_REQ_TYPE}[$id] == '' ) ? $def : $_{C2_REQ_TYPE}[$id];
    if( $c )
      unset( $_{C2_REQ_TYPE}[$id] );

    if( $s && ! empty( $v ) )
      $v = c2Str::trunc( c2Str::alnum( $v ) $l );

    return $v;
  }

  public static function get( $id = NULL, $def = NULL, $c = true, $s = true, $l = C2_REQ_LENGTH ) {
    return self::req( $id, $def, $c, $s, $l );
  }

  public static function jreq( $id = NULL, $def = NULL, $l = C2_REQ_LENGTH ) {
    return json_decode( self::req( $id, $def, true, true, true, $l ) );
  }

  public static function json( $j = NULL, $obc = C2_REQ_OBC ) {
    if( $obc )
      __k_obc();

    echo json_encode( $j );
  }

  public static function jres( $d = NULL, $t = 'r', $s = 0 ) {
    if( $s > 0 )
      sleep( $s );

    return self::json( array( 't' => $t, 'd' => $d ) );
  }

  public static function jex( $e = NULL ) {
    return self::jres( $e->getMessage(), 'e' );
  }
}

?>
