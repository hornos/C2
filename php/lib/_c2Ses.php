<?php

__k_def( 'C2_SES_LENGTH', 128 );
# __k_define( 'COBRA_SESSION_DEBUG_LEVEL', 9 );
# __k_define( 'COBRA_SESSION_ID_ON_CLIENT', true );
# __k_define( 'COBRA_SESSION_STORE_EXTINFO', false );


class c2Ses extends c2DB {
  // time cache
  protected $_time;
  protected $_msec;

  public function __construct( $cfg = NULL ) {
    parent::__construct( $cfg );
    $this->_time = time();
    $this->_msec = microtime();

    // register session handlers
    session_set_save_handler( 
      array( &$this, 'open' ), 
      array( &$this, 'close' ),
      array( &$this, 'read' ),
      array( &$this, 'write' ),
      array( &$this, 'destroy' ),
      array( &$this, 'gc' )
    );
  } // end construct

  protected function _s( $s = NULL ) {
    return __k_str( $s, C2_SES_LENGTH );
  }

  public function cookie() {
    // outdate the cookie
    if( session_id() != "" || isset( $_COOKIE[session_name()] ) ) {
      setcookie( session_name(), '', 0 );
    }
  }

  // Open the session
  public function open( $sp = NULL, $sn = NULL ) {
    /*! Session interface */
    global $sess_save_path;
    $sess_save_path = $sp;
    return true;
  }

  // Close the session
  public function close() {
    /*! Session interface */
    try {
      $this->Disconnect();
    } catch( Exception $e ) {
      return false;
    }
    return true;
  }

  // Read session data
  public function read( $s = NULL ) {
    /*! Session interface */
    try {
      $r = $this->Proc( 'se_rd', array( $this->_s( $s ) ) );
    } catch( Exception $e ) {
      __k_debug( __METHOD__ . '::' . $e->getMessage() );
      return false;
    }
    return $r;
  }

  // Write session data
  public function write( $s = NULL, $d = NULL ) {
    /*! Session interface */
    $s = $this->_s( $s );
    $x = $this->["se.x"];
    $a = array( $s, $x, $d );
    try {
      $this->Proc( 'se_wr', $a );
    } catch( Exception $e ) {
      __k_debug( __METHOD__ . '::' . $e->getMessage() );
      return false;
    }
    return true;
  }

  // Destroy session data
  public function destroy( $s = NULL ) {
    /*! Session interface */
    $s = $this->_s( $s );
    $this->cookie();
    try {
      $this->Proc( 'se_de', array( $s ) );
    } catch( Exception $e ) {
      __k_debug( __METHOD__ . '::' . $e->getMessage() );
      return false;
    }
    return true;
  }

  // Garbage collector
  public function gc() {
    /*! Session interface */
    return true;
  }
  // end session handlers

  // begin session
  protected function _rip() {
    return $_SERVER['REMOTE_ADDR'];
  }

  // TODO: handle proxy connection
  protected function _sip() {
    return $_SERVER['SERVER_ADDR'];
  }

  protected function _hua() {
    return $_SERVER['HTTP_USER_AGENT'];
  }

  // user must contain a time field for session expiration for logout scripts
  protected function _x() {
    $s = session_id();
    try {
      $r = $this->Proc( 'se_x', array( $s ) );
    } catch( Exception $e ) {
      throw new coSesEx( __METHOD__ );
    }
    return $r;
  }

  // TODO: add salt
  // TODO: on server
  protected function _gen() {
    return sha1( uniqid( $this->_msec ).$this->_rip().$this->_hua() );
  }

  protected function _new() {
    $x = $this->_time + $this->_x();
    unset( $_COOKIE[session_name()] );
    setcookie( session_name(), session_id(), $x );
  }

  // Session
  protected function _change() {
    $os = session_id();
    $ns = $this->_gen();

    try {
      $r = $this->Proc( 'se_ch', array( $os, $ns ) );
    } catch( Exception $e ) {
      return false;
    }
    session_id( $ns );
    $this->_new();
    $this->save( 'ctime', $this->_time );
    return true;
  }

  protected function _check() {
    if( $this->_x() == 0 )
      return true;
    $dtime = $this->_time - $this->load( 'ctime' );
    if( $dtime > $this->_x() ) {
      return $this->_change();
    }
    return false;
  }

  // Strict Client Check
  protected function _client() {
    if( $this->load( 'rip' ) != $this->_rip() ) {
      throw new c2Ex( __METHOD__ );
    }
    return true;
  }

  protected function _agent() {
    if( $this->load( 'hua' ) != $this->_hua() ) {
      throw new c2Ex( __METHOD__ );
    }
    return true;
  }

  // Low Level Read and Write Session Data
  protected function _set( $i, $v ) {
    $_SESSION[$i] = $v;
    return true;
  }

  protected function _del( $i ) {
    if( isset( $_SESSION[$i] ) ) {
      unset( $_SESSION[$i] );
      return true;
    }
    throw new c2Ex( __METHOD__ );
  }

  protected function _get( $i ) {
    if( isset( $_SESSION[$i] ) )
      return $_SESSION[$i];

    throw new coSessionException( __METHOD__ . ' ' . $i );
  }


  // Top Level Read and Write Session Data with Encryption
  public function save( $id, $data = false ) {
    try {
      $key = $this->_session_key();
	} catch( Exception $e ) {
	  // store data unencrypted
	  return $this->_set( $id, serialize( $data ) );
	}
	// store data encrypted
	$id   = coCrypt::encrypt( serialize( $id ), $key );
	$data = coCrypt::encrypt( serialize( $data ), $key );
	return $this->_set( $id, $data );
  }


  public function load( $id ) {
    try {
      $key = $this->_session_key();
	} catch( Exception $e ) {
	  // load data unencrypted
	  $result = unserialize( $this->_get( $id ) );
	  return $result;
	}
	// load data encrypted
	$id   = coCrypt::encrypt( serialize( $id ), $key );
	$data = coCrypt::decrypt( $this->_get( $id ), $key );
	$result = unserialize( $data );
	return $result;
  }


  public function erase( $id ) {
    try {
      $key = $this->_session_key();
	} catch( Exception $e ) {
	  return $this->_del( $id );
    }
	$id = coCrypt::encrypt( serialize( $id ), $key );
	return $this->_del( $id );	
  }
  
  
  //
  // Start and Stop the Cobra Session
  //

  public function start() {
    // connect to the db
    $this->Connect();
	// get the time
	$this->_time      = $this->time();
	$this->_microtime = $this->microtime();
	
	// set session name in the cookie
    session_name( $this->_session_name() );
	// start or continue the session
    session_start();

    try {
	  $this->_expired();
	} catch( Exception $e ) {
	  // coDebug::message( 'Start a new session' );
	  // expired or no such sessions therefore
	  // start a new session, generate and set the session id
	  session_id( $this->_generate_id() );
	  // store session data
	  $this->save( 'last_id_change_time', $this->_time );

      // store date for strict session checking
      if( $this->_strict_client_check() ) {	  
	    $this->save( 'ip_address', $this->_remote_addr() );
	    $this->save( 'user_agent', $this->_http_user_agent() );
	  }
	  // reset session cookie id and time
	  $this->_renew_cookie();
	  return true;
	}

    // continue an old session
	// coDebug::message( 'Continue old session' );
	if( $this->_strict_client_check() ) {
      // check ip address
	  $this->_check_client_ip();	  
	  // check user agent
	  $this->_check_client_user_agent();
    }
	// if needed regenerate session id
	$this->_check_change_id();
	return true;
  } // end start


  public function stop( $start_session = true ) {
    // connect to the db
    $this->Connect();	
	// set session name in the cookie
    session_name( $this->_session_name() );
	// start or continue the session
	
    if( $start_session ) session_start();

    try {
	  $this->_expired();
	} catch( Exception $e ) {
      session_destroy();
      return false;
    }
    return session_destroy();
  }  

} // end coSession

?>
