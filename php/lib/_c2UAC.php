<?php

class c2UAC extends c2RPC {
  public function __construct() {
    parent::__construct();
  }

  protected function _rpc_rpc() {
    $app       = c2Req::req( 'a', 'sys' );
    $mod    = c2Req::req( 'm', 'sysChk' );
    $method    = c2Req::req( 'f', 'test' );
    $argv  = c2Req::jreq( 'v', NULL,  );

    $class = __k_str( $m );

    cobra_autoload( $module_id, $app_id );
    $module = new $module_class();
    return $module->rpc( $method_id, $method_argv );
  }

  protected function _rpc_start() {
    $cobra   = cobra_cache_fetch( 'cobra' );
    $session = new coSession( $cobra['sys.config'] ); 
    return $session->start();
  }
  
  protected function _rpc_stop() {
    $cobra   = cobra_cache_fetch( 'cobra' );
    $session = new coSession( $cobra['sys.config'] );
    return $session->stop();
  }

  protected function _rpc_login() {
    $app_id   = coRequest::request( 'app_id', '' );
    $user_id  = coString::email( coRequest::unsafe_request( 'user_id', '' ) );
    $passcode = coRequest::unsafe_request( 'passcode', '' );

    $cobra  = cobra_cache_fetch( 'cobra' );
    $system = new coSystem( $cobra['sys.config'] );
    $system->login( $user_id, $passcode, $app_id );
    // cobra_redirect( $app_id, 'index.php', false );
    return true;
  }

  protected function _rpc_logout() {
    $cobra  = cobra_cache_fetch( 'cobra' );
    $system = new coSystem( $cobra['sys.config'] );
    $system->logout();
    cobra_redirect( $app_id, 'logout.php', false );
    return true;
  }

  protected function _rpc_user() {
    $cobra  = cobra_cache_fetch( 'cobra' );
    $system = new coSystem( $cobra['sys.config'] );
    // $system->start();
    // print_r($_SESSION);
    // die();
    // return $system->load( 'user' );
    return "undef";
  }

  protected function _rpc_authenticate() {
    $cobra  = cobra_cache_fetch( 'cobra' );
    $system = new coSystem( $cobra['sys.config'] );
    return $system->authenticate();
  }

  public function rpc( $command = NULL, $argv = NULL, $force = false ) {
    $return = $force ? $force : coString::tof( coRequest::request( 'return', COBRA_SITE_SEND_RETURN ) );
    $type = 'return';
    $data = "";
    try {
      $data = parent::rpc( $command, $argv );
    } catch( Exception $e ) {
      $type = 'exception';
      $data = $e->getMessage();
      if( ! $return ) throw $e;
    }
    // cobra_ob_clean();
    if( $return ) {
      return coRequest::jresponse( $type, $data );
    }
    return $data;
  }
  
}

?>
