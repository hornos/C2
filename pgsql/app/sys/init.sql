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
-- host		C2		se			127.0.0.1/32	md5
-- host		C2		gc			127.0.0.1/32	md5
-- host		C2		sys			127.0.0.1/32	md5
-- # Optional
-- host		C2		usr			127.0.0.1/32	md5
-- # Optional lock down
-- host		C2		postgres	127.0.0.1/32	reject
-- 3.2 restart postgres
--
-- 4. Step: Reset the database (run this file on the db)
-- psql -f dbinit.sql -U postgres -h localhost
--

-- clean
DROP DATABASE C2;

-- users
DROP USER se;
DROP USER gc;
DROP USER sys;
DROP USER usr;

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
CREATE USER sys WITH PASSWORD 'VkNUJ4BcBx8f' NOCREATEDB NOCREATEUSER;
-- VALID UNTIL ''

-- cobra general user
CREATE USER usr WITH PASSWORD '3EGeB7GnhxHF' NOCREATEDB NOCREATEUSER;
-- VALID UNTIL ''

-- groups
DROP GROUP sys;
DROP GROUP app;
DROP GROUP usr;

CREATE GROUP sys WITH USER sys, se;
CREATE GROUP app WITH USER sys, usr;
CREATE GROUP usr WITH USER sys, usr;
