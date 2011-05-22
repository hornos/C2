<?php

__k_def( 'C2_SYS_ERR', true );

class c2Sys extends c2Ses {
  public function __construct( $cfg = NULL ) {
    parent::__construct( $cfg );
  }

  private function __ac( $p = NULL, $a = NULL, $s = true ) {
    try {
      if( $s )
        $r = $this->ProcRow( $p, $a );
      else
        $r = $this->Proc( $p, $a );
    } catch( Exception $e ) {
      throw new c2Ex( __METHOD__ . ( C2_SYS_ERR ? "\n" . $e->getMessage() : "" ) );
    }
    return $r;
  }

  protected function _u_rd( $id = NULL ) {
    return $this->__ac( 'u_rd', array( $id ), true );
  }

  protected function _g_rd( $id = NULL ) {
    return $this->__ac( 'g_rd', array( $id ), true );
  }

  protected function _u_iltr( $u = NULL ) {
    if( $this['sys.ml'] < 1 )
      return true;
    return $this->__ac( 'u_iltr', array( $u ), false );
  }

  protected function _u_rltr( $u = NULL ) {
    return $this->__ac( 'u_rltr', array( $u ), false );
  }

  protected function _u_login( $u = NULL, $p = NULL ) {
    $p = c2Enc::pas( $p );
    return $this->__ac( 'u_login', array( $u, $p ), false );
  }

  protected function _u_logout( $u = NULL ) {
    return $this->__ac( 'u_logout', array( $u ), false );
  }

  protected function _u_cltr( $u = NULL ) {
    if( $this['sys.ml'] < 1 )
      return true;
    if( $u['ltr'] > $this['sys.ml'] - 1 ) {
      throw new c2Ex( __METHOD__ );
    }
    return true;
  }

  protected function _u_cgt( $u = NULL ) {
    $dt = $this->_time - $u['lat'];
    if( $dt > $u['gt'] ) {
      return false;
    }
    return true;
  }

  public function login( $u = NULL, $p = NULL ) {
    $this->Connect();
    // 1. valid
    // TODO: read view
    try {
      $r = $this->_u_rd( $u );
    } catch( Exception $e ) {
      throw new c2Ex( "Invalid user!" . ( C2_SYS_ERR ? "\n" . $e->getMessage() : "" ) );
    }
    // 3. login tries
    try {
      $this->_u_cltr( $r );
    } catch( Exception $e ) {
      throw new c2Ex( "Login tries exceeded!" . ( C2_SYS_ERR ? "\n" . $e->getMessage() : "" ) );
    }
    // 2. online
    if( c2Str::tof( $r['o'] ) ) {
      // online, within grace
      if( $this->_u_cgt( $r ) )
        throw new c2Ex( "Online!" );
    }
    // 2. login
    try {
      $this->_u_login( $u, $p );
    } catch( Exception $e ) {
      $this->_u_iltr( $u );
      throw new c2Ex( "Invalid password!" . ( C2_SYS_ERR ? "\n" . $e->getMessage() : "" ) );
    }
    return $this->save( 'u', $u );
  }

  public function logout() {
    $this->Connect();
    $this->_u_logout( $this->load( 'u' ) );
  }
}

?>
