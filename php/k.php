<?php
// Globals
$__k_ca = array();

// Kernel functions
function __k_def( $id = NULL, $v = NULL ) {
  return defined( $id ) ? false : define( $id, $v );
}

// begin Output buffering
function __k_obf() {
  if( C2_OB && ! C2_CLI )
    ob_flush();
}

function __k_obs() {
  if( C2_OB && ! C2_CLI )
    ob_start();
}

function __k_obc() {
  if( C2_OB && ! C2_CLI )
    ob_clean();
}

function __k_obr( $obf = true ) {
  if( $obf )
    __k_obf();
  __k_obc();
  __k_obs();
}
// end Output buffering

// begin Output & Strings
function __k_enc( $enc = 'UTF-8' ) {
  if( C2_MB ) {
    mb_internal_encoding( $enc );
    mb_regex_encoding( $enc );
  }
}

function __k_str( $s = NULL, $l = C2_STR_LENGTH ) {
  if( C2_MB )
    return mb_substr( mb_ereg_replace( C2_STR_REGEXP, '', $s ), 0, $l );
  return substr( ereg_replace( C2_STR_REGEXP, '', $s ), 0, $l );
}

function __k_prn( $s = '', $obc = true ) {
  if( $obc )
    __k_obc();
  echo $s . C2_EOL;
}

function __k_json( $s = '', $t = 'e', $obc = C2_JSON_OBC ) {
  if( $obc )
    __k_obc();

  echo json_encode( array( 't' => $t, 'd' => $s ) );
}

function __k_r( $v = NULL, $obc = true ) {
  if( $obc )
    __k_obc();
  if( C2_CLI )
    print_r( $v );
  else {
    echo "<pre>";
    print_r( $v );
    echo "</pre>";
  }
}

function __k_debug( $m = '', $l = 9 ) {
  if( $l >= C2_DEBUG_LEVEL ) {
    if( C2_CLI )
      __k_prn( $m );
    else
      __k_json( $m );
  }
}
// end Output & Strings

// begin Cache
function __k_fetch( $id = NULL ) {
  global $__k_ca;

  if( empty( $id ) || ! isset( $__k_ca[$id] ) )
    throw new c2Ex( __FUNCTION__ . '::' . $id );

  return $__k_ca[$id];
}

function __k_store( $id = NULL, $v = NULL ) {
  global $__k_ca;

  if( empty( $id ) || empty( $v ) )
    throw new c2Ex( __FUNCTION__ );

  $__k_ca[$id] = $v;

  // hook app cache
  if( isset( $v['sys.cache'] ) ) {
    foreach( $v['sys.cache'] as $c => $d ) {
      $cf = $c . '.php';
      $cp = $v['path.cache'] . '/' . $cf;
      if( is_readable( $cp ) )
        $__k_ca[$cf] = file( $cp );
    }
  }
  return true;
}

function __k_require( $class = NULL, $id = NULL ) {
  $c = __k_fetch( $id . '.php' );
  $c = unserialize( $c[0] );

  if( ! isset( $c[$class] ) )
    throw new c2Ex( __FUNCTION__ . '::' . $class . '::' . $id );

  return require_once( $c[$class] );
}

function __k_autoload( $class = NULL, $id = NULL ) {
  $class = __k_str( $class );
  $id    = __k_str( $id );

  try {
    return __k_require( $class, $id );
  } catch( Exception $e ) {
    $c2 = __k_fetch( "c2" );
    foreach( $c2['sys.cache'] as $id => $desc ) {
      try {
        return __k_require( $class, $id );
      } catch( Exception $e ) {}
    }
    throw new c2Ex( __FUNCTION__ . '::' . $class . '::' . $id );
  }
}

function __autoload( $class = NULL ) {
  try {
    return __k_autoload( $class, "c2" );
  } catch( Exception $e ) {
    return false;
  }
}
// end Cache

// begin Exception handling
function __k_die( $s = '', $obc = true ) {
  if( C2_CLI )
    __k_prn( $s, $obc );
  else {
    try {
      $c2 = __k_fetch( "c2" );
    } catch( Exception $e )  {
      __k_json( $s, "e", $obc );
      exit( 1 );
    }
    $er = $c2['path.err'] . '/' . __k_str( $s ) . ".html";
    if( is_readable( $er ) )
      readfile( $er );
    else
      __k_json( $s, "e", $obc );
  }
  exit( 1 );
}

function __k_exc( $e = NULL ) {
  __k_die( $e->getMessage() );
}

// http://php.net/manual/en/function.set-error-handler.php
function __k_err( $errno = NULL, $errstr = NULL, $errfile = NULL, $errline = NULL ) {
  if( ! ( error_reporting() & $errno ) ) {
    // This error code is not included in error_reporting
    return;
  }

  $m  = "Error on line $errline in file $errfile\n";
  $m .= "PHP " . PHP_VERSION . " (" . PHP_OS . ")\n";

  switch( $errno ) {
    case E_USER_ERROR:
      __k_die( $m . "USER [$errno] $errstr\n" );
      break;

    case E_USER_WARNING:
      __k_die( $m . "WARNING [$errno] $errstr\n" );
      break;

    case E_USER_NOTICE:
      __k_die( $m . "NOTICE [$errno] $errstr\n" );
      break;

    default:
      __k_die( $m . "UNKNOWN [$errno] $errstr\n" );
      break;
  }
  /* Don't execute PHP internal error handler */
  return true;
}

// http://stackoverflow.com/questions/277224/how-do-i-catch-a-php-fatal-error
function __k_sd() {
  if( ( $e = error_get_last() ) ) {
    $m  = 'Fatal error on line ' . $e['line'] . ' in file ' . $e['file'] . "\n";
    $m .= "PHP " . PHP_VERSION . " (" . PHP_OS . ")\n";
    $m .= $e['message'] . "\n";
    __k_die( $m );
  }
}
// end Exception handling


// begin Kernel init
function __k_init( $enc = 'UTF-8', $exc = NULL ) {
  __k_def( 'C2_STR_LENGTH', 32 );
  __k_def( 'C2_STR_REGEXP', '[^[:alnum:]_.-]' );
  __k_def( 'C2_JSON_OBC', false );
  __k_def( 'C2_DEBUG_LEVEL', 9 );
  __k_def( 'C2_CLI', ( PHP_SAPI == 'cli' ? true : false ) );
  __k_def( 'C2_OB', true );
  __k_def( 'C2_EOL', C2_CLI ? PHP_EOL : '<br>'.PHP_EOL );
  __k_def( 'C2_MB', extension_loaded( 'mbstring' ) );
  __k_def( 'C2_TZ', 'CET' );
  __k_def( 'C2_ERR', true );
  date_default_timezone_set( C2_TZ );
  // encoding
  __k_obs();
  __k_enc( $enc );
  // errors
  if( C2_ERR ) {
    register_shutdown_function( '__k_sd' );
    set_error_handler( '__k_err' );
  }
  // exceptions
  if( function_exists( $exc ) )
    set_exception_handler( $exc );

  return true;
}
// end Kernel init


class c2Ex extends Exception {
  public function __construct( $e = NULL ) {
    parent::__construct( empty( $e ) ? __METHOD__ : $e );
  }
}


class c2 implements ArrayAccess {
  private $__c2 = NULL;

  public function __construct( $c2 = NULL, $cfg = NULL ) {
    if( empty( $c2 ) || empty( $cfg ) )
      throw new c2Ex( __METHOD__ );

    $this->__c2 = $c2;
    $this->cfg( $cfg );
    $this->cache( "c2", $this->__c2['path.lib'], true );

    // boot
    if( ! empty( $cfg['app.boot'] ) ) {
      foreach( $cfg['app.boot'] as $app ) {
        $this->boot( $app );
      }
    }
  }

  // begin ArrayAccess interface
  public function offsetSet( $offset, $value ) {
    $this->__c2[$offset] = $value;
  }

  public function offsetExists( $offset ) {
    return isset( $this->__c2[$offset] );
  }

  public function offsetUnset( $offset ) {
    unset( $this->__c2[$offset] );
  }

  public function offsetGet( $offset ) {
    if( isset( $this->__c2[$offset] ) )
      return $this->__c2[$offset];

    throw new c2Ex( __METHOD__ );
  }
  // end ArrayAccess interface

  public function cfg( $cfg = NULL ) {
    if( empty( $cfg ) )
      throw new c2Ex( __METHOD__ );

    if( empty( $this->__c2['sys.c'] ) )
      $this->__c2['sys.c'] = $cfg;
    else
      $this->__c2['sys.c'] = array_merge( $this->__c2['sys.c'], $cfg );
    return true;
  }

  public function cache( $id = NULL, $path = NULL ) {
    if( empty( $id ) || empty( $path ) )
      throw new c2Ex( __METHOD__ . '::' . $id . '::' . $path );

    $ca = array( $id => array( 'path' => $path ) );
    if( isset( $this->__c2['sys.cache'] ) )
      $this->__c2['sys.cache'] = array_merge( $this->__c2['sys.cache'], $ca );
    else
      $this->__c2['sys.cache'] = $ca;
    return true;
  }

  function boot( $app = NULL ) {
    if( empty( $app ) || ! isset( $this->__c2['path.app'] ) )
      throw new c2Ex( __METHOD__ . '::' . $app );

    $app = __k_str( $app );
    $app_h = $this->__c2['path.app'] . '/' . $app . '/h.php';
    if( ! is_readable( $app_h ) )
      throw new c2Ex( __METHOD__ . '::' . $app_h );

    require_once( $app_h );

    $app_sys = isset( ${$app} ) ? ${$app} : array();
    $app_cfg = 'cfg';
    $app_arr = array( $app => array( 'sys.app' => $app_sys,
                                     'sys.c' => isset( ${$app_cfg} ) ? ${$app_cfg} : array() ) );
    if( isset( $this->__c2['sys.app'] ) )
      $this->__c2['sys.app'] = array_merge( $app_arr, $this->__c2['sys.app'] );
    else
      $this->__c2['sys.app'] = $app_arr;

    // set cache
    if( isset( $app_sys['path.class'] ) )
      $this->cache( $app, $app_sys['path.class'], true );

    return true;
  }
}
?>
