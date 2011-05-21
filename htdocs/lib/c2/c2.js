
function _post( pl, cb, d, t ) {
  d = typeof(d) == 'undefined' ? 'rpc.php' : d;
  t = typeof(t) == 'undefined' ? 'json' : t;
  $.post( d, pl, cb, t );
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
  if( d['t'] == 'e' ) {
    alert( d['d'] );
    return true;
  }
  alert( d['d'] );
  return false;
}
