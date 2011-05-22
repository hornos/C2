<?php

class c2Enc {
  public function __construct() { }

  public static function enc( $s = '', $k = NULL ) {
    $iv = mcrypt_create_iv( mcrypt_get_iv_size( MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC ), MCRYPT_RAND );
    return bin2hex( mcrypt_encrypt( MCRYPT_RIJNDAEL_128, trim( $k ), $s, MCRYPT_MODE_ECB, $iv ) );
  }

  public static function dec( $e = '', $k = NULL ) {
    $iv = mcrypt_create_iv( mcrypt_get_iv_size( MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC ), MCRYPT_RAND );
    return trim( mcrypt_decrypt( MCRYPT_RIJNDAEL_128, trim( $k ), pack( "H*", $e ), MCRYPT_MODE_ECB, $iv ) );
  }

  public static function pas( $s = '' ) {
    return sha1( $s );
  }

  public static function des( $s = '', $sv = array( 0 ) , $ss = 0 ) {
    if( ! is_array( $sv ) || $ss < 1 )
      return $s;

    $s_len = strlen( $s );
    sort( $sv );

    $psv = 0;
    $csv = $psv;
    $s_des = '';
    $s_end = 0;

    $i = 0;
    foreach( $sv as $v ) {
      $csv = $v + $ss * $i;
      if( $csv > $s_len ) {
        $s_end += $ss;
        continue;
      }

      $s_sub = substr( $s, $psv, $csv - $psv );
      // coHTML::out( $sv . ' ' . $psv . ' ' . $csv . ' |' . $str_sub . '|' , true );
      $s_des .= $s_sub;
      $psv = $csv + $ss;
      ++$i;
    }
    $s_des .= substr( $s, $psv, -$s_end );
    return $s_des;
  }


  public static function ens( $s = '', $sv = array( 0 ) , $ss = 0 ) {
    if( ! is_array( $sv ) || $salt_size < 1 )
      return $s;

    $salt = sha1( uniqid( microtime() ) );
    $salt_len = strlen( $salt );
    $s_len = strlen( $s );

    sort( $sv );

    $psv = 0;
    $csv = $psv;

    $str_salted = '';
    foreach( $sv as $v ) {
      $csv = $v;
      $s_sub = substr( $s, $psv, $csv - $psv );
      $salt_sub = substr( $salt, ( $csv > $ss ? $csv - $ss : $csv ) % $salt_len , $ss );
      $s_ens  .= $s_sub . $salt_sub;
      // $str_salted  .= '|' . $str_sub . '|' . $salt_str_sub;
      $psv = $csv;
    }
    $s_ens .= substr( $s, $csv, $s_len );
    return $s_ens;
  }
}

?>
