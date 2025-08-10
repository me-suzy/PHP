-- $Id: install.sql,v 1.4 2003/05/16 19:36:27 don Exp $

CREATE TABLE mod_phatfile_files (
  id int NOT NULL PRIMARY KEY,
  owner varchar(20) NULL,
  editor varchar(20) NULL,
  ip text,
  label text NOT NULL,
  groups text NULL,
  created int NOT NULL DEFAULT '0',
  updated int NOT NULL DEFAULT '0',
  hidden smallint NOT NULL DEFAULT '1',
  approved smallint NOT NULL DEFAULT '0',
  size int NOT NULL,
  type varchar(50) NOT NULL,
  description text NULL
);
