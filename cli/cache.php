#!/usr/bin/php -q
<?php
require_once( "../php/h.php" );

function __c_dir( $root = NULL, $rec = true ) {
  $c = array();

  if( empty( $root ) )
    return false;

  switch( $rec ) {
    case true:
      foreach( glob( $root . '/' . '*', GLOB_ONLYDIR ) as $dir ) {
        $c = array_merge( $c, __c_dir( $dir, $rec ) );
      }
    case false:
      foreach( glob( $root . '/' . '*.php' ) as $f ) {
        if( is_readable( $f ) ) {
          $bn = basename( $f );
          $cn = str_replace( '.php', '', $bn );
          $c  = array_merge( $c, array( $cn => $f ) );
        }
      }
      break;
  }
  return $c;
}

function __c_cache( $root = NULL, $cf = NULL, $rec = true, $v = true ) {
  $c = __c_dir( $root, $rec );
  if( empty( $c ) )
    return false;

  if( $v ) {
    print_r( $c );
  }

  if( ! $ch = fopen( $cf, 'w' ) )
    return false;

  fwrite( $ch, serialize( $c ) );
  fclose( $ch );
}

// main
if( ! $c2 = __k_fetch( "c2" ) )
  die( __FILE__ . '(' . __LINE__ . ')' );

foreach( $c2['sys.cache'] as $c => $d ) {
  __c_cache( $d['path'], $c2['path.cache'] . '/' . $c . '.php', true );
}

?>
