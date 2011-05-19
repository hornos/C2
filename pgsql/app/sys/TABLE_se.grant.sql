-- tables
-- GRANT SELECT, INSERT, UPDATE, DELETE ON se TO sys;

-- functions
GRANT EXECUTE ON FUNCTION se_rd( varchar )            TO GROUP sys;
GRANT EXECUTE ON FUNCTION se_x( varchar )             TO GROUP sys;
GRANT EXECUTE ON FUNCTION se_wr( varchar, int, text ) TO GROUP sys;
GRANT EXECUTE ON FUNCTION se_ch( varchar, varchar )   TO GROUP sys;
GRANT EXECUTE ON FUNCTION se_de( varchar )            TO GROUP sys;
GRANT EXECUTE ON FUNCTION se_gc()                     TO sys;
GRANT EXECUTE ON FUNCTION se_gc()                     TO gc;
