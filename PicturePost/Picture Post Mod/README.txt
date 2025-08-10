 # README.txt
###############################################################################
#
# Picture Post Mod by BizDesign, Inc.
# written by Greg Raaum, webmaster@imageFolio.com
# Available from http://www.ImageFolio.com
# ---------------------------------------------------------------------------
# PROGRAM NAME : Picture Post Mod for ImageFolio Pro
# VERSION : 1.4
# LAST MODIFIED : 02/03/2001
# ===========================================================================
# COPYRIGHT NOTICE :
#
# Copyright (c) 1999-2001 BizDesign, Inc. All Rights Reserved.
# Selling the code for this program without prior written consent is
# expressly forbidden.
#
# Obtain written permission before redistributing this software over the 
# Internet or in any other medium.  In all cases copyright and header must
# remain intact.
#
# Feel free to modify the code of this program to suit your likings. 
#
# Although this program has been thoroughly tested on BizDesign's servers, we
# do not warrant that it works on all servers and will not be held liable
# for anything, including but not limited to, misusage, error, or loss of data.
#
# Use at your own risk!
###############################################################################

###############################################################################
#  INSTRUCTIONS - PRINT THIS OUT FOR BETTER LEGIBILITY!
###############################################################################

   1.  Unzip your 'PicturePost*.zip' file to a directory on your computer

       You should see the following files and directories:

       cgi-bin      this contains the scripts, your 'templates' directory and
                    the configuration file, 'post_config.pl'

       README.txt   this file

   2.  FTP (login) to your Web server.

   3.  Upload all of the files in the 'cgi-bin' directory to the directory where
       you store imageFolio.cgi and imageSearch.cgi
       Unix Users: Set permissions on all '*.cgi' files to 755 and set permissions
       on 'post.db' to 666
       Make sure you transfer all files in ASCII TEXT mode!

   4.  Upload the two files in the 'templates' directory to the directory where
       you store your ImageFolio templates.
       Make sure you transfer all files in ASCII TEXT mode!

   5.  Open up all the "*.cgi" files in a text editor (such as EditPlus,
       Notepad, or BBEdit, and change the path to Perl at the top to match
       that of your system, unless yours is '/usr/bin/perl'

   6.  Open 'post_config.pl' (from the 'cgi-bin' directory) and answer all of the
       questions.  This is your configuration file and all variables regarding
       your server will need to be defined within this file.

   7.  Save, then Upload all of the files you modified, back to your server.

   8.  Open your Web browser and type in the URL to 'imagePost.cgi'.  You should see
       a form that asks you to upload a picture.  Go ahead and upload a picture to
       any category.  If it works, you're in business, otherwise, try again.
