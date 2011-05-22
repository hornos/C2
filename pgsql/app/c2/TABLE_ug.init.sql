
CREATE TABLE g (
  id varchar(128) PRIMARY KEY NOT NULL,
  v  bool NOT NULL DEFAULT 'f',
  vb int NOT NULL DEFAULT _time() CHECK ( vb  >= 0 ),
  ve int NOT NULL DEFAULT _sec( '3 years' ) CHECK ( vb >= 0 AND ve > vb ),
  d  varchar(256)
);

CREATE TABLE u (
  id varchar(128) PRIMARY KEY NOT NULL,
  -- group
  g   varchar(128) REFERENCES g(id) ON DELETE RESTRICT NOT NULL DEFAULT 'user',
  -- valid
  v  bool NOT NULL DEFAULT 'f',
  vb int  NOT NULL DEFAULT _time() CHECK ( vb  >= 0 ),
  ve int  NOT NULL DEFAULT _sec('3 years') CHECK ( ve >= 0 AND ve > vb ),
  -- description
  d  varchar(256),
  -- password
  p  varchar(512) NOT NULL,
  -- online
  o  bool NOT NULL DEFAULT 'f',
  -- application
  a  varchar(128),
  -- grace
  gt  int NOT NULL DEFAULT '300' CHECK ( gt  >= 0 ),
  -- login time
  lit int NOT NULL DEFAULT '0' CHECK ( lit  >= 0 ),
  -- last action time
  lat int NOT NULL DEFAULT '0' CHECK ( lat >= 0 ),
  -- logout time
  lot int NOT NULL DEFAULT '0' CHECK ( lot >= 0 ),
  -- login tries
  ltr int NOT NULL DEFAULT '0',
  -- last try time
  ltt int NOT NULL DEFAULT '0' CHECK ( ltt >= 0 ),
  -- locale - ISO 639
  loc char(8) NOT NULL DEFAULT 'EN'
);
