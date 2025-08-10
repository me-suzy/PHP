CREATE TABLE usergroups (groupid int(5) NOT NULL auto_increment, groupname varchar(50) NOT NULL default '', cpaccess smallint(3) NOT NULL default '0', uploads smallint(3) NOT NULL default '1', comments smallint(3) NOT NULL default '1', diskspace int(10) default NULL, uploadsize int(10) default NULL, editpho int(3) NOT NULL default '0', editposts int(3) NOT NULL default '0', PRIMARY KEY (groupid)) TYPE=MyISAM;
INSERT INTO usergroups VALUES (5,'Administrator',1,1,1,'','',1,1);
INSERT INTO usergroups VALUES (1,'Banned','','','','','',0,0);
INSERT INTO usergroups VALUES (3,'Unregistered','','','','','',0,0);
INSERT INTO usergroups VALUES (4,'Registered','',1,1,'','',1,1);
INSERT INTO usergroups VALUES (2,'COPPA','','','','','',0,0);
