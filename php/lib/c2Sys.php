<?php

class c2Sys extends c2Ses {
  public function __construct( $cfg = NULL ) {
    parent::__construct( $cfg );
  }

  private function __ac( $p = NULL, $a = NULL, $s = true ) {
    try {
      if( $s )
        $r = $this->ProcRow( $p, $a ); 
      $r = $this->Proc( $p, $a ); 
    } catch( Exception $e ) {
      throw new c2Ex( __METHOD__ );
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
    $p = c2Enc::pass( $p );
    return $this->_ac( 'u_login', array( $u, $p ), false );
  }

  protected function _u_logout( $u = NULL ) {
    return $this->_ac( 'u_logout', array( $u ), false );
  }

  protected function _u_cltr( $u = NULL ) {
    if( $this['sys.ml'] < 1 )
      return true;
    if( $u['ltr'] > $this['sys.ml'] ) {
      throw new c2Ex( __METHOD__ );
    }
    return true;
  }

  protected function _u_cgt( $u = NULL ) {
    $dt = $this->_time - $lat = $u['lat'];
    if( $dt > $u['gt'] ) {
      throw new c2Ex( __METHOD__ );
    }
    return true;
  }

  public function login( $u = NULL, $p = NULL ) {
    // 1. valid
    $r = $this->_u_rd( $u );
    // 2. login tries
    $this->_u_cltr( $r );
    // 3. online
    if( c2Str::tof( $r['o'] ) ) {
      try {
        $this->_u_cgt( $r );
      } catch( Exception $e ) {
        // grace expired, new login
        $this->start();
        $this->_u_login( $u, $p );
        return $this->save( 'u', $u );
      }
      // online, within grace
      return $this->_u_lat( $this->_time );
    }
    // 4. new login
    $this->start();
    $this->_u_login( $u, $p );
    return $this->save( 'u', $u );
  }

  public function logout() {
    $this->start();
    return $this->_logout( $this->load( 'u' ) );
  }
}

?>
