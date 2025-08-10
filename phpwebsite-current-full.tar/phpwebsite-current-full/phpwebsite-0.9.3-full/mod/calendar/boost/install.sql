CREATE TABLE mod_calendar_events (
  id int NOT NULL default '0',
  title varchar(100) NOT NULL default '',
  description text,
  startTime int NOT NULL default '0',
  endTime int NOT NULL default '0',
  startDate int NOT NULL default '0',
  endDate int NOT NULL default '0',
  eventType varchar(8) NOT NULL default '',
  groups text,
  pmChoice varchar(6) default NULL,
  pmID int NOT NULL default '0',
  template varchar(100) NOT NULL default '',
  image varchar(50) default NULL,
  active smallint NOT NULL default '0',
  endRepeat int NOT NULL default '0',
  repeatMode varchar(10) default NULL,
  monthMode varchar(5) default NULL,
  repeatWeekdays varchar(13) default NULL,
  every varchar(30) default NULL,
  PRIMARY KEY  (id)
);


CREATE TABLE mod_calendar_repeats (
  id int NOT NULL default '0',
  startDate int NOT NULL default '0',
  endDate int NOT NULL default '0',
  active SMALLINT DEFAULT '1' NOT NULL,
  index (id)
);

CREATE TABLE mod_calendar_settings (
  cacheView smallint NOT NULL default '1',
  minimonth smallint NOT NULL default '1',
  today smallint NOT NULL default '1',
  daysAhead smallint NOT NULL default '4',
  userSubmit smallint NOT NULL default '1'
);


INSERT INTO mod_calendar_settings VALUES (1, 1, 1, 4, 1);
