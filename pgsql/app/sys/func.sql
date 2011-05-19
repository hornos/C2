-- Language
CREATE LANGUAGE 'plpgsql';


-- Return seconds from now shifted by a time string
CREATE OR REPLACE FUNCTION _sec( _shift varchar ) RETURNS int AS $$
  BEGIN
    RETURN extract( epoch FROM now() + _shift::interval )::integer;
  END;
$$ LANGUAGE plpgsql;
-- access grants
REVOKE ALL PRIVILEGES ON FUNCTION _sec( varchar ) FROM PUBLIC;


-- Return time from epoch in seconds
CREATE OR REPLACE FUNCTION _time() RETURNS int AS $$
  BEGIN
    RETURN extract( EPOCH FROM CURRENT_TIMESTAMP(0) );
  END;
$$ LANGUAGE plpgsql;
-- access grants
REVOKE ALL PRIVILEGES ON FUNCTION _time() FROM PUBLIC;


-- Return time from epoch in seconds with microseconds
CREATE OR REPLACE FUNCTION _msec() RETURNS timestamp AS $$
  BEGIN
    RETURN CURRENT_TIMESTAMP(6);
  END;
$$ LANGUAGE plpgsql;
-- access grants
REVOKE ALL PRIVILEGES ON FUNCTION _msec() FROM PUBLIC;


-- Return the current timestamp
CREATE OR REPLACE FUNCTION _ts() RETURNS timestamp AS $$
  BEGIN
    RETURN CURRENT_TIMESTAMP(0);
  END;
$$ LANGUAGE plpgsql;
-- access grants
REVOKE ALL PRIVILEGES ON FUNCTION _ts() FROM PUBLIC;
