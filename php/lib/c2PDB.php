<?php

__k_def( 'C2_PDB_LIMIT', 100 );
__k_def( 'C2_PDB_LENGTH', 64 );
__k_def( 'C2_PDB_TIME', true );
__k_def( 'C2_PDB_TS', 'Y-m-d H:i:s' );
__k_def( 'C2_PDB_MTS', 'Y-m-d H:i:s.u' );


class c2PDB implements ArrayAccess {
  private $__cfg;    /**< config array */
  private $__con;   /**< database connection object */

  // Constructor
  public function __construct( $cfg = NULL ) {
    if( empty( $cfg ) )
      throw new c2Ex( __METHOD__ );
    /*!
      \param $config System profile which contains DB connection parameters
    */
    $this->__cfg = $cfg;
    $this->_setcon( NULL );
  }

  // begin ArrayAccess interface
  public function offsetSet( $offset, $value ) {
    /*! ArrayAccess interface */
    $this->__cfg[ $offset ] = $value;
  }

  public function offsetExists( $offset ) {
    /*! ArrayAccess interface */
    return isset( $this->__cfg[$offset] );
  }

  public function offsetUnset( $offset ) {
    /*! ArrayAccess interface */
    unset( $this->__cfg[$offset] );
  }

  public function offsetGet( $offset ) {
    /*! ArrayAccess interface */
    if( isset( $this->__cfg[$offset] ) )
      return $this->__cfg[$offset];

    throw new c2Ex( __METHOD__ . '::' . $offset );
  }
  // end ArrayAccess interface


  // begin Time
  public function time() {
    try {
      return C2_PDB_TIME ? $this->Proc( '_time' ) : time();
    } catch( Exception $e ) {
      return time();
    }
  }

  public function microtime() {
    try {
      return C2_PDB_TIME ? $this->Proc( '_msec' ) : date( C2_PDB_MTS );
    } catch( Exception $e ) {
      return date( C2_PDB_MTS );
    }
  }

  public function timestamp() {
    try {
      return C2_PDB_TIME ? $this->Proc( '_ts' ) : date( C2_PDB_TS );
    } catch( Exception $e ) {
      return date( C2_PDB_TS );
    }
  }

  // begin Connection
  protected function _getcon() {
    /*! Checks and returns the valid connection object */
    if( ! $this->__con )
      throw new c2Ex( __METHOD__ );

    return $this->__con;
  }

  private function _setcon( $c = NULL ) {
    /*! Sets the connection object */
    $this->__con = $c;
  }

  public function Connect() {
    try {
      $this->_getcon();
    } catch( Exception $e ) {
      $dsn  = 'pgsql:';
      $dsn .= 'host='.$this['db.host'].';';
      $dsn .= 'port='.$this['db.port'].';';
      $dsn .= 'dbname='.$this['db.name'];
      // connect
      $this->_setcon( new PDO( $dsn, $this['db.user'], $this['db.pass'], $this['db.attr'] ) );
      return true;
    }
    return false;
  }

  public function Disconnect() {
    $this->_setcon( NULL );
  }
  // end Connection

  // begin DB Access
  protected function _Query( $q = NULL, $s = true ) {
    $c = $this->_getcon();

    if( empty( $q ) )
      throw new c2Ex( __METHOD__ );

    $sm = $c->prepare( $q );
    if( ! $sm->execute() )
      throw new c2Ex( __METHOD__ . " " . implode( ",", $sm->errorInfo() ) );

    // SELECT
    if( $s ) {
      $ar = $sm->columnCount();
      if( $ar < 1 )
        throw new c2Ex( __METHOD__ );

      $sm->setFetchMode( PDO::FETCH_ASSOC ); 
      $r = $sm->fetchAll(); 
      if( ! $r )
        throw new c2Ex( __METHOD__ );

      return $r;
    }

    // INSERT, UPDATE, DELETE
    $ar = $sm->rowCount();
    if( $ar < 1 )
      throw new c2Ex( __METHOD__ );

    return $ar;
  } // end _Query

  public function Exec( $q = NULL ) {
    return $this->_Query( $q, false );
  }

  public function Select( $q = NULL, $l = C2_PDB_LIMIT, $o = 0 ) {
    $s  = ( $l > 0 ) ? ' LIMIT '  . $l  : '';
    $s .= ( $o > 0 ) ? ' OFFSET ' . $o : '';
    return $this->_Query( $q . $s );
  }

  public function Row( $q = NULL, $i = 0, $l = C2_PDB_LIMIT, $o = 0 ) {
    $r = $this->Select( $q, $l, $o );
    return $r[$i];
  }

  public function Proc( $p = NULL, $a = NULL ) {
    if( empty( $p ) )
      throw new c2Ex( __METHOD__ );

    $p = __k_str( $p, C2_PDB_LENGTH );

    $q  = 'SELECT ' . $p;
    $q .= '(' . c2Str::a2f( $a, '', true ) . ')';

    $r = $this->Row( $q, 0, 0, 0 );
    if( isset( $r[$p] ) )
      return $r[$p];

    return $r;
  }

  public function ProcSelect( $p = NULL, $a = NULL, $f = NULL, $l = C2_PDB_LIMIT, $o = 0 ) {
    if( empty( $p ) )
      throw new c2Ex( __METHOD__ );

    $p = __k_str( $p, C2_PDB_LENGTH );

    $q  = 'SELECT ' . c2Str::a2f( $f ) . ' FROM ' . $p;
    $q .= '(' . coStr::a2f( $a, '', true ) . ')';

    return $this->Select( $q, $l, $o );
  }

  public function ProcRow( $p = NULL, $a = NULL, $f = NULL, $i = 0, $l = C2_PDB_LIMIT, $o = 0 ) {
    $r = $this->ProcSelect( $p, $a, $f, $l, $o );
    return $r[$i];
  }
}

?>
