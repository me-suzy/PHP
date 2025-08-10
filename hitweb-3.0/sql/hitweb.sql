# MySQL dump 8.13
#
# Host: 127.0.0.1    Database: hitwebpackage
#--------------------------------------------------------
# Server version	3.23.36

#
# Table structure for table 'CATEGORIES'
#

DROP TABLE IF EXISTS CATEGORIES;
CREATE TABLE CATEGORIES (
  CATEGORIES_ID int(11) NOT NULL auto_increment,
  CATEGORIES_NOM text,
  CATEGORIES_PARENTS int(11) NOT NULL default '0',
  PRIMARY KEY  (CATEGORIES_ID),
  KEY THEMES_ID (CATEGORIES_ID)
) TYPE=MyISAM;

#
# Dumping data for table 'CATEGORIES'
#

LOCK TABLES CATEGORIES WRITE;
INSERT INTO CATEGORIES VALUES (1,'Annuaire',0);
UNLOCK TABLES;

#
# Table structure for table 'COMMENTAIRES'
#

DROP TABLE IF EXISTS COMMENTAIRES;
CREATE TABLE COMMENTAIRES (
  COMMENTAIRES_ID int(11) NOT NULL auto_increment,
  COMMENTAIRES_TEXTE text,
  PRIMARY KEY  (COMMENTAIRES_ID),
  KEY COMMENTAIRES_ID (COMMENTAIRES_ID)
) TYPE=MyISAM;

#
# Dumping data for table 'COMMENTAIRES'
#

LOCK TABLES COMMENTAIRES WRITE;
INSERT INTO COMMENTAIRES VALUES (1,'Non valide'),(2,'A Revoir'),(3,'Valide'),(4,'Le site de la semaine');
UNLOCK TABLES;

#
# Table structure for table 'DATE'
#

DROP TABLE IF EXISTS DATE;
CREATE TABLE DATE (
  DATE_ID int(11) NOT NULL auto_increment,
  DATE_JOUR text,
  DATE_MOIS text,
  DATE_ANNEE text,
  PRIMARY KEY  (DATE_ID),
  KEY LIENS_ID (DATE_ID)
) TYPE=MyISAM;

#
# Dumping data for table 'DATE'
#

LOCK TABLES DATE WRITE;
INSERT INTO DATE VALUES (1,'01','01','2001'),(2,'01','02','2001'),(3,'01','03','2001'),(4,'01','04','2001'),(5,'01','05','2001'),(6,'01','06','2001');
UNLOCK TABLES;

#
# Table structure for table 'LIENS'
#

DROP TABLE IF EXISTS LIENS;
CREATE TABLE LIENS (
  LIENS_ID int(11) NOT NULL auto_increment,
  LIENS_CATEGORIES_ID int(11) default NULL,
  LIENS_ADRESSE text,
  LIENS_DESCRIPTION text,
  LIENS_COMMENTAIRES_ID int(11) default NULL,
  LIENS_RECHERCHE text,
  LIENS_PROTOCOL_ID tinyint(4) NOT NULL default '1',
  PRIMARY KEY  (LIENS_ID),
  KEY LIENS_ID (LIENS_ID)
) TYPE=MyISAM;

#
# Dumping data for table 'LIENS'
#

LOCK TABLES LIENS WRITE;
INSERT INTO LIENS VALUES (1,1,'www.hitweb.org','Annuaire de recherche sous license libre GPL.',3,'Annuaire, catalog, référencement, liens, le meilleur du web, the best of the web, FRAVAL, Brian ',1);
UNLOCK TABLES;

#
# Table structure for table 'POINT'
#

DROP TABLE IF EXISTS POINT;
CREATE TABLE POINT (
  POINT_ID int(11) NOT NULL auto_increment,
  POINT_LIENS_ID int(11) default NULL,
  POINT_DATE_ID int(11) default NULL,
  POINT_NB int(11) default NULL,
  PRIMARY KEY  (POINT_ID),
  KEY POINT_ID (POINT_ID)
) TYPE=MyISAM;

#
# Dumping data for table 'POINT'
#

LOCK TABLES POINT WRITE;
INSERT INTO POINT VALUES (1,1,6,1);
UNLOCK TABLES;

#
# Table structure for table 'PROTOCOL'
#

DROP TABLE IF EXISTS PROTOCOL;
CREATE TABLE PROTOCOL (
  PROTOCOL_ID int(11) NOT NULL auto_increment,
  PROTOCOL_NOM text,
  PRIMARY KEY  (PROTOCOL_ID),
  KEY PROTOCOL_ID (PROTOCOL_ID)
) TYPE=MyISAM;

#
# Dumping data for table 'PROTOCOL'
#

LOCK TABLES PROTOCOL WRITE;
INSERT INTO PROTOCOL VALUES (1,'http'),(2,'ftp');
UNLOCK TABLES;

#
# Table structure for table 'VOTE'
#

DROP TABLE IF EXISTS VOTE;
CREATE TABLE VOTE (
  VOTE_ID int(11) NOT NULL auto_increment,
  VOTE_TEXT text,
  VOTE_NB int(11) default NULL,
  PRIMARY KEY  (VOTE_ID),
  KEY VOTE_ID (VOTE_ID)
) TYPE=MyISAM;

#
# Dumping data for table 'VOTE'
#

LOCK TABLES VOTE WRITE;
INSERT INTO VOTE VALUES (1,'Un peu',0),(2,'Beaucoup',0),(3,'A la folie',1),(4,'Pas du tout',0);
UNLOCK TABLES;

#
# Table structure for table 'WEBMASTER'
#

DROP TABLE IF EXISTS WEBMASTER;
CREATE TABLE WEBMASTER (
  WEBMASTER_ID int(11) NOT NULL auto_increment,
  WEBMASTER_LIENS_ID int(11) default NULL,
  WEBMASTER_NOM text,
  WEBMASTER_PRENOM text,
  WEBMASTER_EMAIL text,
  WEBMASTER_MAILING int(2) default NULL,
  PRIMARY KEY  (WEBMASTER_ID),
  KEY WEBMASTER_ID (WEBMASTER_ID)
) TYPE=MyISAM;

#
# Dumping data for table 'WEBMASTER'
#

LOCK TABLES WEBMASTER WRITE;
INSERT INTO WEBMASTER VALUES (1,1,'FRAVAL','Brian','webmaster@hitweb.org',1);
UNLOCK TABLES;

