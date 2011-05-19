-- function grants
GRANT EXECUTE ON FUNCTION _sec( varchar ) TO GROUP sys;
GRANT EXECUTE ON FUNCTION _sec( varchar ) TO GROUP app;
GRANT EXECUTE ON FUNCTION _sec( varchar ) TO GROUP usr;
--
GRANT EXECUTE ON FUNCTION _time( varchar ) TO GROUP sys;
GRANT EXECUTE ON FUNCTION _time( varchar ) TO GROUP app;
GRANT EXECUTE ON FUNCTION _time( varchar ) TO GROUP usr;
--
GRANT EXECUTE ON FUNCTION _msec( varchar ) TO GROUP sys;
GRANT EXECUTE ON FUNCTION _msec( varchar ) TO GROUP app;
GRANT EXECUTE ON FUNCTION _msec( varchar ) TO GROUP usr;
--
GRANT EXECUTE ON FUNCTION _ts( varchar ) TO GROUP sys;
GRANT EXECUTE ON FUNCTION _ts( varchar ) TO GROUP app;
GRANT EXECUTE ON FUNCTION _ts( varchar ) TO GROUP usr;
