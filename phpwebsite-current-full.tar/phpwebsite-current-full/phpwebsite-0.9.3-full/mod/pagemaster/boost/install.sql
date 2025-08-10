CREATE TABLE mod_pagemaster_pages (
     id int PRIMARY KEY,
     title text NOT NULL,
     template varchar(50) NOT NULL,
     section_order text,
     new_page smallint NOT NULL DEFAULT '1',
     advanced smallint NOT NULL DEFAULT '0',
     approved smallint NOT NULL DEFAULT '0',
     mainpage smallint NOT NULL DEFAULT '0',
     active smallint NOT NULL DEFAULT '0',
     created_username varchar(20) NOT NULL,
     updated_username varchar(20) NOT NULL,
     created_date datetime NOT NULL,
     updated_date datetime NOT NULL,
     comments smallint NOT NULL DEFAULT '0',
     anonymous smallint NOT NULL DEFAULT '0'
);

INSERT INTO mod_pagemaster_pages VALUES ('PageMaster Demo Page', 'default.tpl', 'a:1:{i:0;s:1:"1";}', 0, 0, 1, 1, 1, 'install', 'install', '2003-02-04 10:24:27', '2003-02-04 10:24:27');

CREATE TABLE mod_pagemaster_sections (
     id int PRIMARY KEY,
     page_id int,
     title text,
     text text,
     image text,
     template text
);

INSERT INTO mod_pagemaster_sections VALUES ('1', 'PageMaster Demo Section', 'Thank you for choosing PageMaster!\r\n\r\nThis is a default page that is created when installing a fresh copy of PageMaster. This section is using the "default" template which shows all available options for a template.  See TEMPLATE_README in the docs directory for more information.\r\n\r\nTo get started using PageMaster, proceed to the <a href="index.php?module=pagemaster&amp;MASTER_op=main_menu">administrative menu</a> and edit this page.  you may want to remove it all together but HEY, it\'s your web site!\r\n\r\nEnjoy!\r\nAdam\r\n', 'a:0:{}', 'default.tpl');
