<?php

__k_def( 'C2_SES_LENGTH', 128 );

class c2Ses extends c2PDB {
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
      // __k_debug( __METHOD__ . '::' . $e->getMessage() );
      return false;
    }
    return $r;
  }

  // Write session data
  public function write( $s = NULL, $d = NULL ) {
    /*! Session interface */
    $s = $this->_s( $s );
    $x = $this["se.x"];
    try {
      $this->Proc( 'se_wr', array( $s, $x, $d ) );
    } catch( Exception $e ) {
      // __k_debug( __METHOD__ . '::' . $e->getMessage() );
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
      // __k_debug( __METHOD__ . '::' . $e->getMessage() );
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
      throw new c2Ex( __METHOD__ );
    }
    return $r;
  }

  // TODO: add salt
  // TODO: on server
  protected function _gen() {
    return sha1( uniqid( $this->_msec ).$this->_rip().$this->_hua() );
  }

  protected function _new() {
    $x = $this->_time + $this["se.x"];
    unset( $_COOKIE[session_name()] );
    setcookie( session_name(), session_id(), $x);
  }

  // Session
  protected function _chg() {
    $os = session_id();
    $ns = $this->_gen();

    try {
      $r = $this->Proc( 'se_ch', array( $os, $ns ) );
    } catch( Exception $e ) {
      return false;
    }
    session_id( $ns );
    $this->_new();
    $this->save( 'ctm', $this->_time );
    return true;
  }

  protected function _chk() {
    if( $this["se.x"] == 0 )
      return true;
    $dt = $this->_time - $this->load( 'ctm' );
    if( $dt > $this["se.x"] ) {
      return $this->_chg();
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

    throw new c2Ex( __METHOD__ . ' ' . $i );
  }

  // Top Level Read and Write Session Data with Encryption
  public function save( $i = NULL, $d = NULL ) {
    $k = $this["se.k"];
    if( empty( $k ) ) {
      return $this->_set( $i, serialize( $d ) );
    }
    $i = c2Enc::enc( serialize( $i ), $k );
    $d = c2Enc::enc( serialize( $d ), $k );
    return $this->_set( $i, $d );
  }

  public function load( $i = NULL ) {
    $k = $this["se.k"];
    if( empty( $k ) ) {
      return unserialize( $this->_get( $i ) );
    }
    $i = c2Enc::enc( serialize( $i ), $k );
    $d = c2Enc::dec( $this->_get( $i ), $k );
    return unserialize( $d );
  }

  public function erase( $i = NULL ) {
    try {
      $k = $this["se.k"];
    } catch( Exception $e ) {
      return $this->_del( $i );
    }
    $i = c2Enc::enc( serialize( $i ), $k );
    return $this->_del( $i );
  }

  public function start() {
    // connect to the db
    $this->Connect();
    $this->_time = $this->time();
    $this->_msec = $this->microtime();
    // start or continue the session
    session_name( $this["se.s"] );
    session_start();

    try {
      $this->_x();
    } catch( Exception $e ) {
      session_id( $this->_gen() );
      $this->save( 'ctm', $this->_time );
      $this->save( 'rip', $this->_rip() );
      $this->save( 'hua', $this->_hua() );
      // reset session cookie id and time
      $this->_new();
      return true;
    }

    // continue an old session
    // check ip address
    $this->_client();
    // check user agent
    $this->_agent();
    // generate session id
    $this->_chk();
    return true;
  }

  public function stop( $s = true ) {
    $this->Connect();
    session_name( $this["se.s"] );
    if( $s )
      session_start();

    return session_destroy();
  }

}

?>
