# dircaster

 DirCaster 0.9j, released 02/01/2013. http://www.DirCaster.org
 Dr. Bill Bailey, DirCaster Maintainer - DrBill@DrBillBailey.NET
============================================================================

NOTES:

 Open Source code but please leave all references to prior work intact
 when making changes.

 Current maintainer: Dr. Bill Bailey - DrBill@DrBillBailey.NET

 Based on the original DirCaster by Ryan King (http://www.shadydentist.com)
 ID3v2.x tag support added by Warren Stone <fasttr@gmail.com> and
 utilizing getid3 library by James Heinrich <info@getid3.org>,
 http://www.getid3.org. iTunes specific tag support by Warren Stone
 need to include the code to read id3 tags also reads lots of different formats
 we'll implement mp3, mpa (quicktime), asf (wma) and riff (wav/avi)
 alter the path to your getid3 directory location

INSTALLATION:

UnZip the contents of the file DirCasterV09g.zip (which you MUST have already done to see this file!)

Retain the directory structure of the Zip archive and FTP all the files and directory structure to your web server.  Your web server must be running at least PHP 4.

Modify the contents of the file "config_inc.php" to reflect YOUR podcast information.  Please use the info included in the file ONLY as a guide to the structure of the information!  PLEASE FULLY READ ALL COMMENTS!

If you are using Apache as your web server, and it is set up to use .htaccess files, you can create a .htaccess file for your directory on the server that points to either the dircaster.php file or to the index.php file, depending on how you want to present the MP3 files to the web.

Now simply FTP your MP3 files (podcasts or VODcasts) in this same directory, and they will be "served out" via a valid RSS file.  Point your subscriptions to the directory on your web server and the dircaster.php file.  For example, my files are in a directory that the URL http://broadcast.wofm.org points to, so my RSS feed filename would be: http://broadcast.wofm.org/dircaster.php

Your RSS feed may be checked by entering your URL into:

http://feedvalidator.org

BE SURE THAT YOUR MP3 FILES HAVE VALID ID3 INFORMATION!

There is an excellent FREE tool called "MP3tag" located at:
http://www.mp3tag.de/en/

or, can use the MP3 Tag Tools available at:
http://sourceforge.net/projects/massid3lib
to fill in the appropriate information in your MP3 files.  

Also, be sure to set up an .htaccess file in the directory (that is a file that controls Apache, you can get more information on it by Googling "htaccess")
The contents of the .htaccess file should be:
----------------------------------------
DirectoryIndex dircaster.php
php_value memory_limit 16M
php_flag register_globals 1
AddType audio/x-m4a m4a M4A
AddType video/mp4 mp4 MP4
AddType video/x-m4v m4v M4V
AddType video/webm webm
AddType audio/mpeg mp3 MP3
AddType audio/playlist m3u M3U
AddType audio/x-scpls pls PLS
AddType application/x-ogg ogg OGG
AddType audio/wav wav WAV
----------------------------------------
(Without the dashed lines)  Alternatively, you can add these Mime types to your web servers mime.types file.

Enjoy podcasting to the web!  More information and a tutorial can be found at:

http://www.DirCaster.org