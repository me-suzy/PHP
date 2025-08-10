CREATE TABLE mod_search_register (
  id int NOT NULL, 
  module text NOT NULL,
  search_class text NOT NULL,
  search_function text NOT NULL,
  search_cols text NOT NULL,
  view_string text NOT NULL,
  show_block smallint NOT NULL default '0',
  PRIMARY KEY  (id)
);
