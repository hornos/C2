-- group
CREATE OR REPLACE FUNCTION g_v( _id varchar ) RETURNS bool AS $$
  DECLARE
    _ex  varchar := 'g_v';
    _t   integer := _time();
    _tmp varchar := '';
  BEGIN
    SELECT INTO _tmp g_id FROM g 
           WHERE v = 't' 
           AND id = _id 
           AND vb < _t 
           AND ve > _t;
    IF NOT FOUND THEN
      RAISE EXCEPTION '%', _ex;
    END IF;
    RETURN FOUND;
  END;
$$ LANGUAGE plpgsql SECURITY DEFINER;
-- access grants
REVOKE ALL PRIVILEGES ON FUNCTION g_v( varchar ) FROM PUBLIC;


CREATE OR REPLACE FUNCTION g_rd( _id varchar ) RETURNS SETOF g AS $$
  DECLARE
    _ex varchar := 'g_rd';
    _t  integer := _time();
    _r  g%ROWTYPE;
  BEGIN
    FOR _r IN SELECT * FROM g 
           WHERE v = 't' 
           AND id = _id 
           AND vb < _t 
           AND ve > _t
    LOOP
      RETURN NEXT _r;
    END LOOP;
    IF NOT FOUND THEN
      RAISE EXCEPTION '%', _ex;
    END IF;
  END;
$$ LANGUAGE plpgsql SECURITY DEFINER;
-- access grants
REVOKE ALL PRIVILEGES ON FUNCTION g_rd( varchar ) FROM PUBLIC;


-- user
CREATE OR REPLACE FUNCTION u_v( _id varchar ) RETURNS bool AS $$
  DECLARE
    _ex  varchar := 'u_v';
    _t   integer := _time();
    _g   varchar(128) := '';
    _chk bool := false;
  BEGIN
    -- user check
    SELECT INTO _g g FROM u 
           WHERE v = 't' 
           AND id = _id 
           AND vb < _t 
           AND ve > _t;
    IF NOT FOUND THEN
      RAISE EXCEPTION '%', _ex;
    END IF;
    -- group check
    SELECT INTO _chk g_v( _g );
    RETURN FOUND;
  END;
$$ LANGUAGE plpgsql SECURITY DEFINER;
-- access grants
REVOKE ALL PRIVILEGES ON FUNCTION u_v( varchar ) FROM PUBLIC;


CREATE OR REPLACE FUNCTION u_rd( _id varchar ) RETURNS SETOF u AS $$
  DECLARE
    _ex  varchar := 'u_rd';
    _t   integer := _time();
    _r   u%ROWTYPE;
    _chk bool := false;
  BEGIN
    -- check
    SELECT INTO _chk u_v( _id );
    FOR _r IN SELECT * FROM u 
           WHERE id = _id 
    LOOP
      RETURN NEXT _r;
    END LOOP;
    IF NOT FOUND THEN
      RAISE EXCEPTION '%', _ex;
    END IF;
  END;
$$ LANGUAGE plpgsql SECURITY DEFINER;
-- access grants
REVOKE ALL PRIVILEGES ON FUNCTION u_rd( varchar ) FROM PUBLIC;


CREATE OR REPLACE FUNCTION u_iltr( _id varchar ) RETURNS int AS $$
  DECLARE
    _ex  varchar := 'u_litr';
    _t   integer := _time();
    _ltr integer := 0;
    _chk bool    := false;
  BEGIN
    -- check
    SELECT INTO _chk u_v( _id );
    SELECT INTO _ltr ltr FROM u 
           WHERE id = _id;
    UPDATE u SET ltr = (_ltr + 1), ltt = _t 
           WHERE id = _id;
    IF NOT FOUND THEN
      RAISE EXCEPTION '%', _ex;
    END IF;
    RETURN _ltr;
  END;
$$ LANGUAGE plpgsql SECURITY DEFINER; 
-- access grants
REVOKE ALL PRIVILEGES ON FUNCTION u_iltr( varchar ) FROM PUBLIC;


CREATE OR REPLACE FUNCTION u_rltr( _id varchar ) RETURNS bool AS $$
  DECLARE
    _ex  varchar := 'u_rltr';
    _t   integer := _time();
    _chk bool    := false;
  BEGIN
    -- check
    SELECT INTO _chk u_v( _id );
    UPDATE u SET ltr = '0', ltt = '0' WHERE id = _id;
    IF NOT FOUND THEN
      RAISE EXCEPTION '%', _ex;
    END IF;
    RETURN FOUND;
  END;
$$ LANGUAGE plpgsql SECURITY DEFINER; 
-- access grants
REVOKE ALL PRIVILEGES ON FUNCTION u_rltr( varchar ) FROM PUBLIC;


CREATE OR REPLACE FUNCTION u_login( _id varchar, _p varchar ) RETURNS bool AS $$
  DECLARE
    _ex  varchar := 'u_login';
    _t   integer := _time();
    _chk bool    := false;
    __p  varchar(512) := '';
  BEGIN
    -- check
    SELECT INTO _chk u_v( _id );
    -- password
    SELECT INTO __p p FROM u WHERE id = _id;
    IF NOT FOUND THEN
      RAISE EXCEPTION '%', _ex;
    END IF;
    IF NOT __p = _p THEN
      RAISE EXCEPTION '%', _ex;
    END IF;
    -- register
    UPDATE u SET o = 't', lot = _t, lat = _t, ltr = 0 
            WHERE id = _id;
    IF NOT FOUND THEN
      RAISE EXCEPTION '%', _ex;
    END IF;
    RETURN FOUND;
  END;
$$ LANGUAGE plpgsql SECURITY DEFINER;
-- access grants
REVOKE ALL PRIVILEGES ON FUNCTION u_login( varchar, varchar ) FROM PUBLIC;


CREATE OR REPLACE FUNCTION u_logout( _id varchar ) RETURNS bool AS $$
  DECLARE
    _ex  varchar := 'u_logout';
    _t   integer := _time();
    _chk bool    := false;
  BEGIN
    -- check
    SELECT INTO _chk u_v( _id );
    UPDATE u SET o = 'f', lot = _t 
             WHERE id = _id;
    IF NOT FOUND THEN
      RAISE EXCEPTION '%', _ex;
    END IF;
    RETURN FOUND;
  END;
$$ LANGUAGE plpgsql SECURITY DEFINER;
-- access grants
REVOKE ALL PRIVILEGES ON FUNCTION u_logout( varchar ) FROM PUBLIC;


CREATE OR REPLACE FUNCTION u_o( _id varchar ) RETURNS bool AS $$
  DECLARE
    _ex  varchar := 'u_o';
    _t   integer := _time();
    _o   bool    := false;
    _chk bool    := false;
  BEGIN
    -- check
    SELECT INTO _chk u_v( _id );
    SELECT INTO _o o FROM u 
                     WHERE o = 't' 
                     AND id = _id;
    IF NOT FOUND THEN
      RAISE EXCEPTION '%', _ex;
    END IF;
    RETURN FOUND;
  END;
$$ LANGUAGE plpgsql SECURITY DEFINER;
-- access grants
REVOKE ALL PRIVILEGES ON FUNCTION u_o( varchar ) FROM PUBLIC;


CREATE OR REPLACE FUNCTION u_lat( _id varchar ) RETURNS int AS $$
  DECLARE
    _ex  varchar := 'u_lat';
    _t   integer := _time();
    _chk bool    := false;
  BEGIN
    -- check
    SELECT INTO _chk u_v( _id );
    UPDATE u SET lat = _t WHERE id = _id;
    IF NOT FOUND THEN
      RAISE EXCEPTION '%', _ex;
    END IF;
    RETURN _t;
  END;
$$ LANGUAGE plpgsql SECURITY DEFINER;
-- access grants
REVOKE ALL PRIVILEGES ON FUNCTION u_lat( varchar ) FROM PUBLIC;


CREATE OR REPLACE FUNCTION u_gc() RETURNS int AS $$
  DECLARE
    _ex varchar := 'u_gc';
    _t  integer := _time();
    _ar integer := 0;
  BEGIN
    UPDATE u SET online = 'f', lot = _t 
             WHERE v = 't' 
             AND vb < _t 
             AND ve > _t 
             AND o = 't' 
             AND lat + gt < _t;
    GET DIAGNOSTICS _ar = ROW_COUNT;
    RETURN _ar;
  END;
$$ LANGUAGE plpgsql SECURITY DEFINER;
-- access grants
REVOKE ALL PRIVILEGES ON FUNCTION u_gc() FROM PUBLIC;



