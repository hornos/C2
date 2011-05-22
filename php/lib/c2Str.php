<?php

// Defines
__k_def( 'C2_STR_ALPHA', '[^[:alpha:]_:./ -]' );
__k_def( 'C2_STR_ALNUM', '[^[:alnum:]_:./ -]' );
__k_def( 'C2_STR_NUM', '[^[:digit:].-]' );
__k_def( 'C2_STR_EMAIL', '[^[:alnum:]@_./ -]' );

class c2Str {
  public function __construct() { }

  public static function lower( $s = '' ) {
    return C2_MB ? mb_strtolower( $s ) : strtolower( $s);
  }

  public static function trunc( $s = '', $l = C2_STR_LENGTH ) {
    if( $l == 0 )
      return $s;

    return C2_MB ? mb_substr( trim( $s ), 0, $l ) : substr( trim( $s ), 0, $l );
  }

  public static function sql( $s = '' ) {
    return '\''.addslashes( $s ).'\'';
  }

  public static function a2f( $arr = NULL, $def = '*', $sql = false ) {
    if( empty( $arr ) )
      return $def;

    $l = count( $arr );
    if( $l < 1 )
      return $def;

    $i = 1;
    $s = '';
    foreach( $arr as $a ) {
      $s .= ( $sql ? self::sql( $a ) : $a );
      if( $i < $l )
        $s .= ',';
      ++$i;
    }
    return $s;
  }

  // begin Regexp filter
  public static function alpha( $s = '' ) {
    return C2_MB ? mb_ereg_replace( C2_STR_ALPHA, '', $s ) : ereg_replace( C2_STR_ALPHA, '', $s );
  }

  public static function alnum( $s = '' ) {
    return C2_MB ? mb_ereg_replace( C2_STR_ALNUM, '', $s ) : ereg_replace( C2_STR_ALNUM, '', $s );
  }

  public static function num( $s = '' ) {
    return C2_MB ? mb_ereg_replace( C2_STR_NUM, '', $s ) : ereg_replace( C2_STR_NUM, '', $s );
  }

  public static function email( $s = '' ) {
    return C2_MB ? mb_ereg_replace( C2_STR_EMAIL, '', $s ) : ereg_replace( C2_STR_EMAIL, '', $s );
  }
  // end Regexp filter

  // begin conversion
  public static function int( $s = '', $l = 0, $u = 1000 ) {
    $v = intval( self::num( $s ) );
    if( $v < $l )
      return $l;
    if( $v > $u )
      return $u;
    return $v;
  }

  public static function float( $s = '' ) {
    return floatval( self::num( $s ) );
  }

  public static function tof( $s = '' ) {
    if( empty( $s ) )
      return false;

    $s = self::trunc( self::alnum( $s ), 4 );
    if( $s == 1 || $s == 't' || $s == 'true' )
      return true;

    return false;
  }
  // end conversion
}

?>
