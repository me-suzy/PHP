Language Files

In the directory "english" you will find the language files required by PhotoPost.

To perform your own translations:

a) Copy the english directory to a new one (called "french" or "german", etc.)
b) Edit the english text in each file - be careful not to change the variable names
c) Upload new directory to your languages directory on your server
d) Edit your cofig-inc.php file and change $pp_lang to the name of the directory you created in (a)

If you do not see any text when you load your index page, that is probably because it cannot find
your language files. Double check your paths (should not contain spaces) and make sure the files are
being read.