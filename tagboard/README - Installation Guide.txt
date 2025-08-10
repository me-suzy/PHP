CJ Tag Board V1.0  -  James Crooke © 2002 - webmaster@cj-design.com
CJ Web Design http://www.cj-design.com ===============================
===============================================================


Files Just Downloaded
~~~~~~~~~~~~~

The contents of "CJ Tag Board.zip" ........

1.	tag.php
2.	htmlcode.dat
3.	display.php
4.	config.php
5.	stats.txt
6.	tag.txt
7.	tagcount.txt
8.	stylesheet.css
9.	ip.gif
10.	Readme.txt
11.	Copying.txt (GPL)
12.	and just incase you forgot where it came from.... an Internet Shortcut  :D

Files Required
~~~~~~~~

1.	tag.php		(comes in zip file)
2.	display.php		(comes in zip file)
3.	config.php		(comes in zip file)
4.	stats.txt		(comes in zip file)
5.	tag.txt		(comes in zip file)
6.	tagcount.txt	(comes in zip file)
7.	stylesheet.css	(comes in zip file)
8.	ip.gif			(comes in zip file)
9.	a page to display the htmlcode.dat - usually your homepage!

Installation Help
~~~~~~~~~~

1.  	Variables to edit in "config.php": (only edit the following!)

	$MAX_LENGTH = 100;			« the maximum length of each 'tag'
	$NUM_COMMENTS = 30;  		« the maximum comments to appear
	$print_how_many = 1;  			« set at "1" prints how many total 'tag's' - set at "0" disables this feature
	$print_how_many = 1;  			« set to "1" displays number of tags on the tag board, "0" turns feature off
	$stats = 1;					« set to "1" displays poster stats on hover over square image (ip.gif), "0" turns feature off
	$meta_refresh = 1;			« set to "1" tells the tag board to refresh, "0" turns feature off
	$meta_refresh_rate = 120;		« if you want to refresh the tag board, enter the time in seconds to wait per refresh

	Feel free to add to the bad word filter too!	

2.	"stylesheet.css"

	If you want to edit the colour of the scroll bar and the colour of the "tags", open up the stylesheet and edit the following:

	BODY	{	font-family: Verdana;				« change to your websites font
			color: 000000;					« change to your desired font colour
			font-size: 8pt;					« change to the font size you want
			SCROLLBAR-BASE-COLOR: #F8A527;	« the colour of the scrollbar
			SCROLLBAR-ARROW-COLOR: #FFFFFF;	« the colour of the scrollbars arrows
		}

	
3.	IMPORTANT NOTE:
	To edit rest of the colours and styles, open up htmlcode.dat in notepad and read what is written within the <!-- comment --> tags!


4.  	Upload all the required files and CHMOD "tag.txt" and "tagcount.txt" to 777

5.	Go to the page you inserted the "htmlcode.dat" and test it works!

6.	OPTIONAL:  If you would like to store your tag files in a /tag directory - you must link the <form action="tag.php">
	to <form action="tag/tag.php"> and the <iframe src="display.php"> to <iframe src="tag/display.php">


Thats All!
~~~~~~

Thank you for downloading this script, I hope you like it - feedback would be appreciated
(nothing to harsh please) Send it to webmaster@cj-design.com

If you need any help with this script, try going to the FAQ's page on the website:
http://www.cj-design.com/?id=faq&cat=downloads/tagboard or log on to the forums @
http://www.cj-design.com/?cat=forum : )

James - CJ Web Designer

==========================================================================