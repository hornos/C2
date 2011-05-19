
-- read session data
CREATE OR REPLACE FUNCTION se_rd( _s varchar ) RETURNS text AS $$
  DECLARE
    _ex varchar := 'se_rd';
    _d  text    := '';
    _t  integer := _time();
  BEGIN
    SELECT INTO _d d FROM se WHERE s = _s;
    IF NOT FOUND THEN
      RAISE EXCEPTION '%', _ex;
    END IF;
    RETURN _d;
  END;
$$ LANGUAGE plpgsql SECURITY DEFINER;
-- access grants
REVOKE ALL PRIVILEGES ON FUNCTION se_rd( varchar ) FROM PUBLIC;

-- check if the session is expired
CREATE OR REPLACE FUNCTION se_x( _s varchar ) RETURNS int AS $$
  DECLARE
    _ex varchar := 'se_x';
    _t  integer := _time();
    _x  integer := 0;
  BEGIN
    SELECT INTO _x x FROM se WHERE s = _s;
    IF NOT FOUND THEN
      RAISE EXCEPTION '%', _ex;
    END IF;
    IF _x < _t THEN
      RAISE EXCEPTION '%', _ex;
    END IF;
    RETURN _x;
  END;
$$ LANGUAGE plpgsql SECURITY DEFINER;
-- access grants
REVOKE ALL PRIVILEGES ON FUNCTION se_x( varchar ) FROM PUBLIC;

-- write session data
-- TODO: consider 35-1 postgres example
CREATE OR REPLACE FUNCTION se_wr( _s varchar, _x int, _d text ) RETURNS bool AS $$
  DECLARE
    _ex varchar := 'se_wr';
    _t  integer := _time();
    __x integer := _time + _x;
  BEGIN
    UPDATE se SET d = _d, x = __x WHERE s = _s AND x > _t;
    IF NOT FOUND THEN
      INSERT INTO se ( s, x, d ) VALUES ( _s, __x, _d );
      IF NOT FOUND THEN
        RAISE EXCEPTION '%', _ex;
      END IF;
    END IF;
    RETURN FOUND;
  END;
$$ LANGUAGE plpgsql SECURITY DEFINER;
-- access grants
REVOKE ALL PRIVILEGES ON FUNCTION se_wr( varchar, int, text ) FROM PUBLIC;

-- change session id
CREATE OR REPLACE FUNCTION se_ch( _s varchar, __s varchar ) RETURNS bool AS $$
  DECLARE
    _ex varchar := 'se_ch';
    _t  integer := _time();
  BEGIN
    UPDATE se SET s = __s WHERE s = _s AND x > _t;
    IF NOT FOUND THEN
      RAISE EXCEPTION '%', _ex;
    END IF;
    RETURN FOUND;
  END;
$$ LANGUAGE plpgsql SECURITY DEFINER;
-- access grants
REVOKE ALL PRIVILEGES ON FUNCTION se_ch( varchar, varchar ) FROM PUBLIC;

-- destroy the session
CREATE OR REPLACE FUNCTION se_de( _s varchar ) RETURNS bool AS $$
  DECLARE
    _ex varchar := 'se_de';
    _t  integer := _time();
  BEGIN
    UPDATE se SET x = '0' WHERE s = _s AND x > _t;
    IF NOT FOUND THEN
      RAISE EXCEPTION '%', _ex;
    END IF;
    RETURN FOUND;
  END;
$$ LANGUAGE plpgsql SECURITY DEFINER;
-- access grants
REVOKE ALL PRIVILEGES ON FUNCTION se_de( varchar ) FROM PUBLIC;

-- garbage collector
CREATE OR REPLACE FUNCTION se_gc() RETURNS int AS $$
  DECLARE
    _ex varchar := 'se_gc';
    _t  integer := _time();
    _ar integer := 0;
  BEGIN
    DELETE FROM se WHERE x < _t;
    GET DIAGNOSTICS _ar = ROW_COUNT;
    RETURN _ar;
  END;
$$ LANGUAGE plpgsql SECURITY DEFINER;
-- access grants
REVOKE ALL PRIVILEGES ON FUNCTION se_gc() FROM PUBLIC;
