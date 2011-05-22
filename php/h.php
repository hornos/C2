<?php

// get out global home path
$__home = dirname( __FILE__ );

// system paths  
$c2['path.home']   = $__home;
$c2['path.lib']    = $__home.'/lib';
$c2['path.app']    = $__home.'/app';
$c2['path.cache']  = $__home.'/cache';
$c2['path.log']    = $__home.'/log';
$c2['path.err']    = $__home.'/err';
$c2['path.htdocs'] = $__home.'/../htdocs';

// system files
$c2['path.h'] = __FILE__;
$c2['path.k'] = $__home.'/k.php';
$c2['path.c'] = $__home.'/se.php';

// load kernel
if( ! is_readable( $c2['path.k'] ) )
  die( __FILE__ . '::' . $c2['path.k']  ) ;
// else
require_once( $c2['path.k'] );

// load config
if( ! is_readable( $c2['path.c'] ) )
  die( __FILE__ . '::' . $c2['path.c']  ) ;
// else
require_once( $c2['path.c'] );

// custom defines
__k_def( 'C2_OB', true );
__k_def( 'C2_SYS_ERR', true );
__k_init( 'UTF-8', '__k_exc' );
__k_store( "c2", new c2( $c2, $cfg ) );
// clean up
unset( $__home, $c2, $cfg );
?>
