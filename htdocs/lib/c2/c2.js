var _g_post = new Array();

function _post( pl, cb, d, t ) {
  if( typeof( pl ) == 'undefined' || typeof( cb ) == 'undefined' ) {
    return false;
  }
  d = typeof( d ) == 'undefined' ? 'rpc.php' : d;
  t = typeof( t ) == 'undefined' ? 'json' : t;
  $.post( d, pl, cb, t );
  _g_post.push( cb );
  return false;
}

function _start() {
  _post( { 'c' : 'start' }, _cb_se );
}

function _stop() {
  _post( { 'c' : 'stop' }, _cb_se );
}

function _test() {
  _post( { 'c' : 'test' }, _cb_se );
}

function _cb_se( data, textStatus ) {
  _ex( data );
}

function _ex( d ) {
  if( typeof( d ) == 'undefined' )
    return false;
  if( d['t'] == 'e' ) {
    alert( d['d'] );
    return false;
  }
  return true;
}

function _cb_login( data, textStatus ) {
  var i = _g_post.indexOf( this.success );
  if( i > -1 ) {
    _g_post.splice( i, 1 );
  }
  if( _ex( data ) ) {
    window.location = "site.php";
  }
  return false;
}

function _cb_logout( data, textStatus ) {
  if( _ex( data ) ) {
    window.location = "index.php";
  }
  return false;
}
