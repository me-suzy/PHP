CREATE TABLE usergroups (groupid int(5) NOT NULL auto_increment, groupname varchar(50) NOT NULL default '', cpaccess smallint(3) NOT NULL default '0', uploads smallint(3) NOT NULL default '1', comments smallint(3) NOT NULL default '1', diskspace int(10) default NULL, uploadsize int(10) default NULL, editpho int(3) NOT NULL default '1', editposts int(3) NOT NULL default '1', PRIMARY KEY (groupid)) TYPE=MyISAM;
INSERT INTO usergroups VALUES ('1','Anonymous','','','','','',0,0);
INSERT INTO usergroups VALUES ('2','Admin',1,1,1,'','',1,1);
INSERT INTO usergroups VALUES ('3','Regular Users','',1,1,'','',1,1);

