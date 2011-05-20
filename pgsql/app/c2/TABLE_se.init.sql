CREATE TABLE se (
  -- session id
  s		varchar(512)	PRIMARY KEY NOT NULL,
  -- expires
  x		int				NOT NULL DEFAULT '0' CHECK ( x >= 0 ),
  -- data
  d		text			NOT NULL
);
