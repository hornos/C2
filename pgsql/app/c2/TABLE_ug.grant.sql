-- tables
-- GRANT SELECT, INSERT, UPDATE, DELETE ON u TO sys;

GRANT EXECUTE ON FUNCTION g_v( _id varchar )    TO GROUP sys;
GRANT EXECUTE ON FUNCTION g_rd( _id varchar )   TO GROUP sys;
GRANT EXECUTE ON FUNCTION u_v( _id varchar )    TO GROUP sys;
GRANT EXECUTE ON FUNCTION u_rd( _id varchar )   TO GROUP sys;
GRANT EXECUTE ON FUNCTION u_iltr( _id varchar ) TO GROUP sys;
GRANT EXECUTE ON FUNCTION u_rltr( _id varchar ) TO gc;
GRANT EXECUTE ON FUNCTION u_rltr( _id varchar ) TO sysop;
GRANT EXECUTE ON FUNCTION u_login( _id varchar, _p varchar ) TO GROUP sys;
GRANT EXECUTE ON FUNCTION u_logout( _id varchar ) TO GROUP sys;
GRANT EXECUTE ON FUNCTION u_o( _id varchar )      TO GROUP sys;
GRANT EXECUTE ON FUNCTION u_lat( _id varchar )    TO GROUP sys;
GRANT EXECUTE ON FUNCTION u_gc() TO gc;
GRANT EXECUTE ON FUNCTION u_gc() TO sysop;
