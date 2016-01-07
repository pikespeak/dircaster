<?php
//============================================================================
// DirCaster specific variables (CHANGE the items below to match YOUR feed)
// Required by dircaster.php
//============================================================================
/*
 * DirCaster specific variables (Change the items below to match YOUR feed)
 * This file is required by dircaster.php
 * It contains both configuration and RSS defaults
 * Media item overrides are not managed here
*/

 /*
  * ======================
  * CONFIGURATION SECTION
  * ======================
 */

  /* Remote media storage
   * or 'The Cloud Option'
   * 09-01-2012
   *
   * By default, DirCaster looks for media files in the directory/folder
   * described in the variable $mediaDir for allowable file types.
   *
   * If your media files are instead in a remote location, such as cloud
   * storage, this $remoteMedia variable allows you to cause DirCaster
   * to look elsewhere the media location and tag information.
   * The same .txt override format is used as is used for the traditional
   * override.
   * The override files are identified with $overrideFileType and
   * $overrideFolder.
   * Media can be where-ever the [link], [enclosureURL] and [quid] values
   * point to by fully qualified URL.
   *
   * Possible values
   *  VALUE = 0 (false) scan for local media files in $mediaDir (see below)
   *  VALUE = 1 (true) ignore local media files.
   *           Scan for media file overrides in the locatin specified by
   *           by $overrideFileType and $overrideFolder.
   *           NOTE: With this value set to 1
   *            . All local media files will be ignored
   *            . There must be a override test file for any remote media
   *            . the [link], [enclosureURL] and [quid] values
   *              must have valid values
   *            . Any stray file with the $overrideFileType value may cause
   *              problems
  */
  $remoteMedia = 0;

  /* If the $remoteMedia variable above is set to 0
   *  this is the location of your media (audio or video files).
   *
   * A value of "." is the directory where you installed DirCaster
   * You can place you files in a subdirctory
   *  for example "./my_media"
   * This is ignored if $remoteMedia = 1
  */
  $mediaDir = "."; //do not include the trailing /

  /*
   * If you choose to use override files
   *  If $remoteMedia = 0, these must be valued only if you use 1 or more
   *    override files
   *  If $remoteMedia = 1, These must be must have valid values
   *
   * file extension of the media override file
   */
   $overrideFileType = '.txt';

   /*
    * location of media files
    *  '.'           = where the dircaster.php is located
    *  'mySubFolder' = some subdirectory to dircaster.php
    *  Do not remove the  . '/'  at the end
    * Example
    * $overrideFolder = './override_files'. '/';
    * $overrideFolder = '.' . '/';
   */
   $overrideFolder = './override_files'. '/';

  /*
   *
  */

  /*
   * Delimiter used to separate allowable file types
  */
  $delim1 = ' '; // a space - Do NOT change unless you reprogram dircaster.php


  /* The following settings can be used to increase, or decrease
   * The date/time rendered for pubDate and buildDate
   * Possible use would be to compensate for a faulty web server time
   * as it relates to annual time shifts.
   * NOTE: Only one should be set to TRUE
   * If both are FALSE, then normal current server time is used (default)
  */
  $timeAdjstMinus  = FALSE; // reduces the two dates by 1 hour, if TRUE
  $timeAdjstPlus   = FALSE; // increases the two dates by 1 hour, IF TRUE

  /* Set the "maxFeeds" variable to the number of podcasts you want displayed
   * in the feed when it is accessed... in this way, you can limit the number
   * shown, speeding up execution of the access.
   * Positive value required
  */
  $maxFeeds         = "24";

  /* You can set the "enclosurePrefix" variable to allow redirect via another site
   * Such as a stats analysis via an external provider, such as
   * Raw Voice Media's TechPodcasts
   * or Blubrry Stats System.  Leave as '' for no enclosure.
   * Otherwise leave blank
  */
  $enclosurePrefix  = '' ;

  //$enclosurePrefix  = '' ; // for no redirect
  // Allowed File Types (file extensions) in Feed URL, make sure your type is listed
  $sftypes = "(.mp3 .m4a .asf .wma .wav .avi .mov .m4b .m4v)";

  // location of the "getid3" library - should be the default unless you
  //  changed it in your directory structure.

  $id3LibPath     = "./lib_getid3/getid3/getid3.php";

 /*
  * ====================================
  * SECTION RSS (non media 'item' data)
  * ====================================
 */
  // Podcast Title Variable
  $titleTAG       = "Your Title";
  //
  // Podcast Discription
  $descriptionTAG = "Your Description";
  // Your Podcast Website URL
  $linkTAG        = "Your Link";
  // Atom link tag - This should be the exact name of the URL
  // (including the script name) due to the rel="self" attribute
  // 'http://.../dircaster.php' or 'http://.../some folder/dircaster.php'
  $linkAtomTAG    = "$linkTAG";
  // The following settings can be used to increase or decrease
  // The date/time rendered for pubDate and buildDate
  // Possible use would be to compensate for faulty web server time
  // as it relates to annual time shifts.
  // NOTE: Only one should be set to TRUE
  // If both are FALSE, then normal current server time is used (default)
  $timeAdjstMinus  = FALSE; // reduces the two dates by 1 hour, if TRUE
  $timeAdjstPlus   = FALSE; // increases the two dates by 1 hour, IF TRUE
  // For iTunes "New Feed" feature - to change your URL for the feed - should it
  // ever be necessary - also set the "$NEWfeedURL_ON" value to "yes" to
  // activate it.
  $NEWfeedURL     = "Your Feed";
  $NEWfeedURL_ON  = "no";
  // Your Copyright String
  $copyrightTAG   = "Your Name";
  // Podcast Language Tag
  $languageTAG    = "en-us";
  // Site Webmaster E-mail Address
  $webMasterTAG   = "Your Email";
  // Give The DirCaster Project some "props!"
  $generatorTAG   = "DirCaster V0.9j - www.DirCaster.org";
  // "Time to Live"
  $ttlTAG         = "60";
  // Your image should be 1400 by 1400 pixels for iTunes - (gif, jpg, or png)
  $rssImageUrlTAG   = "Your Image URL";
  // Image Tag
  $rssImageTitleTAG = "$titleTAG";
  // Image Link
  $rssImageLinkTAG = $linkTAG;
  //
  //---------------------------------------------------------------------------
  // iTunes specific tags below - See iTunes RSS feed tech info for details at:
  // http://www.apple.com/itunes/store/podcaststechspecs.html#_Toc526931674
  // iTunes specific tags
  //---------------------------------------------------------------------------
  //
  $iTunesNameSpace = "xmlns:itunes=\"http://www.itunes.com/dtds/podcast-1.0.dtd\"";
  $atomNameSpace   = "xmlns:atom=\"http://www.w3.org/2005/Atom\"";
  // To satisfily the feed validator. Make $atomNameSpace = '' to drop Atom
  $nameSpaceTAG = $iTunesNameSpace . ' ' . $atomNameSpace;
  //
  //-- Explict Tag options: "yes" = IS Explicit, "no" = Not saying one way or
  //-- another, "clean" = Is a "G" rated clean feed suitable for kids.
  $explicitTAG    = "clean";
  $summaryTAG     = "Your Summary";
  $authorTAG      = "Your Name";
  $ownerNameTAG   = "Your Name";
  $ownerEmailTAG  = "Your Email ($ownerNameTAG)";
  $topCategoryTAG = "Your Show Tag";
  $subCategoryTAG = "Your Show Tag";
  $keywordTAG     = "Your Keywords";
  $imageUrlTAG    = "Your Image URL";
  $imageTitleTAG  = "Your Title";
  $imageLinkTAG   = "Your Link";
// Set this tag value to "yes" if you want to use individual images in the .txt
// Override File  
  $imageItemTAG   = "no";
//
//============================================================================
//
// end of DirCaster Variables.
//
//============================================================================
?>
