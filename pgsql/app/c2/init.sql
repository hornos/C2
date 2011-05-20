--
-- Simple Postgres Security:
-- postgres user is the defacto root for postgres
--
-- 1. Step: Set password for postgres user
-- 1.1 modify pg_hba.conf
-- host		all		postgres	127.0.0.1/32	trust
--
-- 1.2 restart postgres
--
-- 1.3 connect to postgres
-- psql -U postgres -h 127.0.0.1
--
-- 1.4 set the password for postgres user
-- ALTER USER postgres WITH PASSWORD 'secret'
--
-- 2. Step: secure the database
-- 2.1 modify pg_hba.conf (the same line as above: trust -> md5)
-- host		all		postgres	127.0.0.1/32	md5
--
-- 2.2 restart postgres
--
-- 3. Step: Allow cobra users to connect
-- 3.1 modify pg_hba.conf
-- host		c2		se			127.0.0.1/32	md5
-- host		c2		gc			127.0.0.1/32	md5
-- host		c2		sys			127.0.0.1/32	md5
-- # Optional
-- host		c2		usr			127.0.0.1/32	md5
-- # Optional lock down
-- host		c2		postgres	127.0.0.1/32	reject
-- 3.2 restart postgres
--
-- 4. Step: Reset the database (run this file on the db)
-- psql -f dbinit.sql -U postgres -h localhost
--

-- clean
DROP DATABASE IF EXISTS c2;

DROP FUNCTION IF EXISTS _sec( varchar ) CASCADE;
DROP FUNCTION IF EXISTS _time() CASCADE;
DROP FUNCTION IF EXISTS _msec() CASCADE;
DROP FUNCTION IF EXISTS _ts() CASCADE;

-- users
DROP USER IF EXISTS se;
DROP USER IF EXISTS gc;
DROP USER IF EXISTS sysop;
DROP USER IF EXISTS usrop;

-- create the database
CREATE DATABASE C2 WITH OWNER = postgres ENCODING = 'UTF8';
-- LOCATION = '/path/to/db'

-- create the users, change the passwords
-- session user
CREATE USER se WITH PASSWORD '64WEzzaK4W3p' NOCREATEDB NOCREATEUSER;
-- VALID UNTIL ''

-- session garbage collector
CREATE USER gc WITH PASSWORD 'tVK12eXwbnRy' NOCREATEDB NOCREATEUSER;
-- VALID UNTIL ''

-- cobra admin user
CREATE USER sysop WITH PASSWORD 'VkNUJ4BcBx8f' NOCREATEDB NOCREATEUSER;
-- VALID UNTIL ''

-- cobra general user
CREATE USER usrop WITH PASSWORD '3EGeB7GnhxHF' NOCREATEDB NOCREATEUSER;
-- VALID UNTIL ''

-- groups
DROP GROUP IF EXISTS sys;
DROP GROUP IF EXISTS app;
DROP GROUP IF EXISTS usr;

CREATE GROUP sys WITH USER sysop, se;
CREATE GROUP app WITH USER sysop, usrop;
CREATE GROUP usr WITH USER sysop, usrop;
