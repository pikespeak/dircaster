<?php

/*
   * DirCaster 0.9j, released 02/01/2013. http://www.DirCaster.org
   *
   * ChangeLog - See "changelog for DirCaster.txt"
   *
   * Do not edit anything in this file, unless you are a programmer
   * All individual site cinfiguration is done in the file: config_inc.php
   *
   * Main Code
  */

  /* Open Source code but please leave all references to prior work intact
   * when making changes.
   *
   * Current maintainer: Dr. Bill Bailey - DrBill@DirCaster.org
   *
   * Based on the original DirCaster by Ryan King (http://www.shadydentist.com)
   * ID3v2.x tag support added by Warren Stone <fasttr@gmail.com> and
   * utilizing getid3 library by James Heinrich <info@getid3.org>,
   * http://www.getid3.org. iTunes specific tag support by Warren Stone
   * need to include the code to read id3 tags
   * also reads lots of different formats
   * we'll implement mp3, mpa (quicktime), asf (wma) and riff (wav/avi)
   * alter the path to your getid3 directory location
  */

  // DirCaster configuration file
  require_once('config_inc.php');

  // ID3 tag lib
  require_once( $id3LibPath );

  ini_set('allow_call_time_pass_reference', 1);
  //error_reporting(0);

  // Post/Get Parameter check
  $goodParam = 1;

  if ( isSet( $_GET ) ){ // for later versions of PHP
    if ( !empty( $_GET[ 'ft' ] ) ){
      $useft = $_GET[ 'ft' ];
      $t = '.' . $useft;
      if ( strPos( strToLower( $sftypes ) , strToLower( $t ) ) === False ){
        $goodParam = 0;
      }
      $sftypes = "(." . strToLower($useft) . ")";
    }else{
      if ( isSet( $HTTP_GET_VARS ) ){ //for earlier versions of php
        if ( !empty( $HTTP_GET_VARS['ft'] ) ){
          $useft = $HTTP_GET_VARS['ft'];
          $t = '.' . $useft;
          if ( strPos( strToLower( $sftypes ) , strToLower( $t ) ) === False ){
            $goodParam = 0;
          }
          $sftypes = "(." . strToLower($useft) . ")";
        }
      }
    }
  }// end test for GET vars


  // lets make all of the user set variables are xml compliant, just in case
  $titleTAG         = escChars ($titleTAG);
  $descriptionTAG   = escChars ($descriptionTAG);
  $copyrightTAG     = escChars ($copyrightTAG);
  $languageTAG      = escChars ($languageTAG);
  $webMasterTAG     = escChars ($webMasterTAG);
  $generatorTAG     = escChars ($generatorTAG);;
  $rssImageTitleTAG = escChars ($rssImageTitleTAG);
  $summaryTAG       = escChars ($summaryTAG);
  $authorTAG        = escChars ($authorTAG);
  $ownerNameTAG     = escChars ($ownerNameTAG);
  $ownerEmailTAG    = escChars ($ownerEmailTAG);
  $topCategoryTAG   = escChars ($topCategoryTAG);
  $subCategoryTAG   = escChars ($subCategoryTAG);
  $keywordTAG       = escChars ($keywordTAG);
  $imageTitleTAG    = escChars ($imageTitleTAG);

  // For override logic
  $aItemsEmpty = array( 'title'           => '',
                        'link'            => '',
                        'author'          => '',
                        'commentText'     => '',
                        'commentCDATA'    => '',
                        'pubDate'         => '',
                        'enclosureURL'    => '',
                        'enclosureLength' => '',
                        'enclosureType'   => '',
                        'subtitle'        => '',
                        'keywords'        => '',
                        'allowItemImage'       => '',
                        'imageURL'        => '',
                        'imageTitle'      => '',
                        'imageFileType'   => '',
                        'duration'        => '',
                        'guid'            => '',
                        'artist'          => '',
                        'album'           => '',
                        'composer'        => '',
                        'genre'           => '',
                        'year'            => '',
                        'track'           => '',
                        'copyright'       => '',
                        'summary'         => ''
                      );

  header('Content-type: text/xml', true);
  // 2007-11-12 - Some media players don't like the http:// in the middle
  if ( empty( $enclosurePrefix ) ){
    $rootMP3URL = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
  }else{
    $rootMP3URL = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
  }
  $rootMP3URL =  substr($rootMP3URL, 0, strrpos ($rootMP3URL, "/")); // Trim off script name itself
  // Add enclosure prefix if valued (ie., TPN stats)
  $rootMP3URL = $enclosurePrefix . $rootMP3URL ;

  print "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
  print "<rss $nameSpaceTAG version=\"2.0\">\n";
  print " <channel>\n";

  // original dircaster channel info
  if ($NEWfeedURL_ON == "yes") {
    print"     <itunes:new-feed-url>$NEWfeedURL</itunes:new-feed-url>\n";
  }
  print"  <title>$titleTAG</title>\n";
  print"  <link>$linkTAG</link>\n";

  // To satisily the feed validator
  print "  <atom:link href=\"$linkAtomTAG\" rel=\"self\" type=\"application/rss+xml\" />\n";

  // Alter description if file tye request is bad
  if ( !$goodParam ){
    print"  <description>Requested type is not supported (sftypes).</description>\n";
  }else{
    print"  <description>$descriptionTAG</description>\n";
  }

  print"  <category>$topCategoryTAG</category>\n";

  $timeAdjust = time();
  if ( $timeAdjstMinus ) {
    $timeAdjust = time() - (60 * 60);  // decrease an hour
  }else if ( $timeAdjstPlus ) {
    $timeAdjust = time() + (60 * 60);  // advance an hour
  }
  print"  <pubDate>" . fixDate( date("r", $timeAdjust ) ) . "</pubDate>\n";
  print"  <lastBuildDate>" . fixDate( date("r", $timeAdjust ) ) . "</lastBuildDate>\n";

  print"  <language>$languageTAG</language>\n";
  print"  <copyright>$copyrightTAG</copyright>\n";
  print"  <generator>$generatorTAG</generator>\n";
  print"  <managingEditor>$ownerEmailTAG</managingEditor>\n";
  print"  <webMaster>$webMasterTAG</webMaster>\n";
  print"  <ttl>$ttlTAG</ttl>\n";

  // new itunes channel stuff
  // itunes author tag
  print "  <itunes:author>$authorTAG</itunes:author>\n";
  // itune subtitle tag
  print "  <itunes:subtitle>$descriptionTAG</itunes:subtitle>\n";
  // itunes category tags
  print "  <itunes:category text=\"".$topCategoryTAG."\">\n";
  if ($subCategoryTAG != "" ) {
    print"    <itunes:category text=\"".$subCategoryTAG."\"/>\n";
  }
  print"  </itunes:category>\n";
  // itunes summary tag
  print "  <itunes:summary>$summaryTAG</itunes:summary>\n";
  // iTunes channel keywords
  echo ("  <itunes:keywords>$keywordTAG</itunes:keywords>\n");
  // itunes owner tags
  print"  <itunes:owner>\n";
  print"  <itunes:name>$ownerNameTAG</itunes:name>\n";
  print"  <itunes:email>$ownerEmailTAG</itunes:email>\n";
  print"  </itunes:owner>\n";
  // itunes explicit tag
  print"  <itunes:explicit>$explicitTAG</itunes:explicit>\n";
  // image tags
  $rssImageUrlTAG =htmlentities(str_replace(" ", "%20", $rssImageUrlTAG));
  print"  <itunes:image href=\"$rssImageUrlTAG\" />\n";
  print"  <image>\n";
  print"    <url>".$rssImageUrlTAG."</url>\n";
  print"    <title>".$rssImageTitleTAG."</title>\n";
  print"    <link>".$rssImageLinkTAG."</link>\n";
  print"  </image>\n";

  // Trailing tags for bad request
  if ( !$goodParam ){
    print "</channel>\n</rss>\n";
    exit();
  }

  // Determine running mode to generate each podcast/ item listing
  if ( $remoteMedia ) {   //$remoteMedia = 1, new as of version .09i
    // Get override text names and dates files pointing to remote media
    $dirArray = getDir( $overrideFolder, $overrideFileType);

    // sort the array by override bubDate or filedate if non
    $dirArray = sortByPubdate( $dirArray );

    // Create the individual items/podcast tags
    remoteMedia( $dirArray, $maxFeeds, $delim1, $sftypes,
                 $overrideFileType, $aItemsEmpty, $rootMP3URL,
                 $ownerEmailTAG, $timeAdjstMinus, $timeAdjstPlus,
                 $keywordTAG, $imageItemTAG, $linkTAG,
                 $enclosurePrefix, $imageUrlTAG, $imageTitleTAG );
  }else{  //$remoteMedia = 0, same logic as older version and ver i
    // process local media and any overrides
    $dirArray = getDir( $mediaDir, $sftypes);

    // sort the array by override pubDate or filedate if non
    $dirArray = nonRemotesortByPubdate( $dirArray, $delim1, $sftypes,
                                        $overrideFolder, $overrideFileType );

    // Create the individual items/podcast tags
    nonRemoteMedia( $dirArray, $maxFeeds, $delim1, $sftypes,
            $overrideFileType, $aItemsEmpty, $rootMP3URL, $ownerEmailTAG,
            $timeAdjstMinus, $timeAdjstPlus, $keywordTAG, $imageItemTAG,
            $linkTAG, $overrideFolder,$imageUrlTAG, $imageTitleTAG  );
  }// end remote or not

  // channel close / rss close
  print "</channel>\n</rss>\n";


  /*
   * Functions and Classes
   */

  // patch for dates returned with 1 digit day of month with leading space
  function fixDate( $filedate ){
    if ( strPos( $filedate, '  ' ) !== FALSE ){
      // version 1 - this dros one space ('  4' = ' 4') and works in the validator
      //$filedate = str_replace( '  ', ' ', $filedate );
      // version 2 replaces the space with a 0 ( '  4' = ' 04')
      $filedate = str_replace( 'Sun,  ', 'Sun, 0', $filedate );
      $filedate = str_replace( 'Mon,  ', 'Mon, 0', $filedate );
      $filedate = str_replace( 'Tue,  ', 'Tue, 0', $filedate );
      $filedate = str_replace( 'Wed,  ', 'Wed, 0', $filedate );
      $filedate = str_replace( 'Thu,  ', 'Thu, 0', $filedate );
      $filedate = str_replace( 'Fri,  ', 'Fri, 0', $filedate );
      $filedate = str_replace( 'Sat,  ', 'Sat, 0', $filedate );
    }
    return $filedate;
  }//end function fixDate


  // strip file extension for allowable types
  function stripType( $delim, $types, $str ){
    // strip file extensions form file name per $sfTypes string
    $types = str_replace( '(', '', $types );
    $types = str_replace( ')', '', $types );
    $aTypes = explode( $delim, $types );

    $cnt = count( $aTypes );
    for ( $i = 0; $i < $cnt; $i++ ){
      $str = str_replace( $aTypes[ $i ], '', $str );
    }

    return $str;
  }// end function stripType


  // Strip non-text characters
  function stripJunk ($text) {
    // Strip non-text characters
    $outText = '';
    for ($c=0; $c<strlen($text); $c++) {
      if ( ord($text[$c] ) >= 32 AND ord( $text[$c] ) <= 122 )
        $outText.=$text[$c];
    }// end for
    return $outText;
  }// end function stripJunk


  // Escape chars
  function escChars ($text) {
    // Strip non-text characters
    $fixed = str_replace("&","&#38;", $text);
    $fixed = str_replace("<","&#60;",$fixed);
    $fixed = str_replace("©","&#169;",$fixed);
    $outText = str_replace(">","&#62;",$fixed);
    return $outText;
  }// end function escChars


  // ID3 Lib class
  class CMP3File {
    //properties
    var $title;
    var $artist;
    var $album;
    var $year;
    var $comment;
    var $genre;
    var $composer;
    var $copyright;
    var $mime_type;
    var $duration;

    function getid3 ($file) {

      $tit  = '';
      $cmp  = '';
      $alb  = '';
      $com  = '';
      $art  = '';
      $mim  = '';
      $dur  = '';

      // Initialize getID3 engine
      if ( file_exists($file) ) {
        //after verifying the file exists,
        $getID3 = new getID3;

        // Analyze file and store returned data in $ThisFileInfo
        $ThisFileInfo = $getID3->analyze($file);

        // Optional: copies data from all subarrays of [tags] into [comments] so
        // metadata is all available in one location for all tag formats
        // meta-information is always available under [tags] even if this is not called
        getid3_lib::CopyTagsToComments($ThisFileInfo);

        // Output desired information in whatever format you want
        // Note: all entries in [comments] or [tags] are arrays of strings
        // See structure.txt for information on what information is available where
        // or check out the output of /demos/demo.browse.php for a particular file
        // to see the full detail of what information is returned where in the array
        //echo @$ThisFileInfo['comments']['artist'][0]; // artist from any/all available tag formats

        $mim = @$ThisFileInfo['mime_type']; // artist from any/all available tag formats
        $dur = @$ThisFileInfo['playtime_string']; // play duration from any/all available tag formats

        switch (strrchr(strtolower($file), ".")) {
          case ".mp3";
            $tit = @$ThisFileInfo['id3v2']['comments']['title'][0]; // artist from any/all available tag formats
            $alb = @$ThisFileInfo['id3v2']['comments']['album'][0]; // artist from any/all available tag formats
            $art = @$ThisFileInfo['id3v2']['comments']['artist'][0]; // artist from any/all available tag formats
            $com = @$ThisFileInfo['id3v2']['comments']['comment'][0];

            $cmp = @$ThisFileInfo['id3v2']['comments']['composer'][0]; // artist from any/all available tag formats
            $gen = @$ThisFileInfo['id3v2']['comments']['genre'][0]; // artist from any/all available tag formats
            break;
          case ".m4a";
            $tit = @$ThisFileInfo['quicktime']['comments']['title'][0]; // artist from any/all available tag formats
            $alb = @$ThisFileInfo['quicktime']['comments']['album'][0]; // artist from any/all available tag formats
            $art = @$ThisFileInfo['quicktime']['comments']['artist'][0]; // artist from any/all available tag formats
            $com = @$ThisFileInfo['quicktime']['comments']['comment'][0]; // artist from any/all available tag formats
            $cmp = @$ThisFileInfo['quicktime']['comments']['writer'][0]; // artist from any/all available tag formats
            // $gen = @$ThisFileInfo['quicktime']['comments']['genre'][0]; // artist from any/all available tag formats
            break;
          case ".m4b";
            $tit = @$ThisFileInfo['quicktime']['comments']['title'][0]; // artist from any/all available tag formats
            $alb = @$ThisFileInfo['quicktime']['comments']['album'][0]; // artist from any/all available tag formats
            $art = @$ThisFileInfo['quicktime']['comments']['artist'][0]; // artist from any/all available tag formats
            $com = @$ThisFileInfo['quicktime']['comments']['comment'][0]; // artist from any/all available tag formats
            $cmp = @$ThisFileInfo['quicktime']['comments']['writer'][0]; // artist from any/all available tag formats
            // $gen = @$ThisFileInfo['quicktime']['comments']['genre'][0]; // artist from any/all available tag formats
            break;
          case ".mov";
            $tit = @$ThisFileInfo['quicktime']['comments']['title'][0]; // artist from any/all available tag formats
            $alb = @$ThisFileInfo['quicktime']['comments']['album'][0]; // artist from any/all available tag formats
            $art = @$ThisFileInfo['quicktime']['comments']['artist'][0]; // artist from any/all available tag formats
            $com = @$ThisFileInfo['quicktime']['comments']['comment'][0]; // artist from any/all available tag formats
            $cmp = @$ThisFileInfo['quicktime']['comments']['director'][0]; // artist from any/all available tag formats
            // $gen = @$ThisFileInfo['quicktime']['comments']['genre'][0]; // artist from any/all available tag formats
            break;
          case ".asf";
            $tit = @$ThisFileInfo['asf']['comments']['title'][0]; // artist from any/all available tag formats
            $alb = @$ThisFileInfo['asf']['comments']['album'][0]; // artist from any/all available tag formats
            $art = @$ThisFileInfo['asf']['comments']['artist'][0]; // artist from any/all available tag formats
            $com = @$ThisFileInfo['asf']['comments']['comment'][0]; // artist from any/all available tag formats
            $cmp = @$ThisFileInfo['asf']['comments']['composer'][0]; // artist from any/all available tag formats
            $gen = @$ThisFileInfo['asf']['comments']['genre'][0]; // artist from any/all available tag formats
            break;
          case ".wma";
            $tit = @$ThisFileInfo['asf']['comments']['title'][0]; // artist from any/all available tag formats
            $alb = @$ThisFileInfo['asf']['comments']['album'][0]; // artist from any/all available tag formats
            $art = @$ThisFileInfo['asf']['comments']['artist'][0]; // artist from any/all available tag formats
            $com = @$ThisFileInfo['asf']['comments']['comment'][0]; // artist from any/all available tag formats
            $cmp = @$ThisFileInfo['asf']['comments']['composer'][0]; // artist from any/all available tag formats
            $gen = @$ThisFileInfo['asf']['comments']['genre'][0]; // artist from any/all available tag formats
            break;
          default;
            $tit = $file; // artist from any/all available tag formats
        }// end switch

        $this->title = stripJunk($tit);
        $this->composer = stripJunk($cmp);
        $this->album = stripJunk($alb);
        $this->comment = stripJunk($com);
        $this->copyright = stripJunk($cmp);
        $this->artist = stripJunk($art);
        $this->mime_type = stripJunk($mim);
        $this->duration = stripJunk($dur);
        return true;
      }else{
        return false; // file doesn't exist
      }// end if
    }//end function
  }// end class CMP3File


  // Returns directory as array[file,filedate]
  // for allowed types, date in newest to oldest order
  function getDir($mp3Dir, $supported_file_types) {
    // Returns directory as array[file]=date in newest to oldest order
    $dirArray = array();
    $diskdir = $mp3Dir . '/';
    if (is_dir($diskdir)) {
      $dh = opendir($diskdir);
      while ( ($file = readdir($dh) ) !== false ) {
        if (filetype( $diskdir . $file ) == "file" && $file[0]  != ".") {
          $fext = strrchr(strtolower($file), ".");
          if (strpos($supported_file_types, $fext) !== FALSE) {
            $ftime = filemtime($mp3Dir."/".$file);
            $dirArray[$mp3Dir."/".$file] = $ftime;
          }
        }
      }// end while
      closedir($dh);
    }
    asort($dirArray);
    $dirArray = array_reverse($dirArray);
    return $dirArray;
  }// end function getDir


  /* Logic for variable $remoteMedia = 1
   * Processes onl override text files
   * The override text files must point to media in [link], [enclosureURRL],
   * [guid] tags, which are required
   * All other tags are processed as in past versions
   * New to version i
   */
  function remoteMedia( $dirArray, $maxFeeds, $delim1, $sftypes,
         $overrideFileType, $aItemsEmpty, $rootMP3URL, $ownerEmailTAG,
         $timeAdjstMinus, $timeAdjstPlus, $keywordTAG, $imageItemTAG, $linkTAG,
         $enclosurePrefix, $imageUrlTAG, $imageTitleTAG ) {

    while ( list($filename, $filedate ) = each($dirArray) AND $maxFeeds > 0) {
      echo "  <item>\n";
      $descriptiveFileName = $filename;

      $aItems = $aItemsEmpty;

      if ( file_exists( $descriptiveFileName ) ){
        getDescriptions( $descriptiveFileName, $aItems ); // alters aItems
      }

      // remote remoteMedia item enclosure
      // keep as first test
      if ( $aItems[ 'enclosureURL' ] != '' ){
        if ( $aItems[ 'enclosureLength' ] == '' ){
          $enclosureLength = "";
        }else{
          $enclosureLength = $aItems[ 'enclosureLength'];
        }
        if ( $aItems[ 'enclosureType' ] == '' ){
          $enclosureType = '';
        }else{
          $enclosureType = $aItems[ 'enclosureType' ];
        }
        if ( !empty( $enclosurePrefix ) ){
          $enclosureUrl = $enclosurePrefix . $aItems[ 'enclosureURL' ];
        }else{
          $enclosureUrl = $aItems[ 'enclosureURL' ];
        }
        $enclosure = ' url="' . $enclosureUrl . '"' .
                     ' length="' . $enclosureLength . '"' .
                     ' type="' . $enclosureType . '"';
        echo "    <enclosure $enclosure />\n";
      }

      // remote item title
      $title = '';
      if ( $aItems[ 'title' ] == '' ){
        if ( empty( $title ) ){
          //use file name, drop file extension
          $title = stripType( $delim1, $sftypes, $filename );
        }
        //handle '&' in names
        $title = htmlentities( $title );
      }else{
        //title from descriptions file
        $title = $aItems[ 'title' ];
      }
      echo ("    <title>".$title."</title>\n"); // title string

      // remote This is the location of the hosting page that has the enclosure
      if ( $aItems[ 'link' ] == '' ){
        $url = $rootMP3URL."/". htmlentities(str_replace(" ", "%20", $filename));
      }else{
        $url = $aItems[ 'link' ];
        echo ("    <link>".$url."</link>\n"); //link string
      }

      // remote item artist
      if ( $aItems[ 'artist' ] == '' ){
        $artist = '';
      }else{
        $artist = $aItems[ 'artist' ];
      }

      // remote item author
      if ( $aItems[ 'author' ] == '' ){
        $author = $ownerEmailTAG;
        $author = htmlentities($author);
      }else{
        $author = $aItems[ 'author' ];
      }
      echo ("    <author>$author</author>\n");

      // remote album
      if ( $aItems[ 'album' ] == '' ){
        $album = '';
      }else{
        $album = $aItems[ 'album' ];
      }

      // remote composer
      if ( $aItems[ 'composer' ] == '' ){
        $composer = '';
      }else{
        $composer = $aItems[ 'composer' ];
      }
      // place saveer, override not currently supported

      //remote genre
      if ( $aItems[ 'genre' ] == '' ){
        $genre = '';
      }else{
        $genre = $aItems[ 'genre' ];
      }
      // place saveer, override not currently supported

      //remote year
      if ( $aItems[ 'year' ] == '' ){
        $year = '';
      }else{
      }
      // place saveer, override not currently supported

      //remote track
      if ( $aItems[ 'track' ] == '' ){
        $track = '';
      }else{
        $track = $aItems[ 'track' ];
      }
      // place saveer, override not currently supported

      //remote coyright
      if ( $aItems[ 'copyright' ] == '' ){
        $copyright ='';
      }else{
        $copyright = $aItems[ 'copyright' ];
      }
      // place saveer, override not currently supported

      // remote item description
      $description = '';
      if ( $aItems[ 'commentText' ] != '' ){
        $description = $aItems[ 'commentText' ];
      }
      if ( $description == '' ){
        if ( !empty( $album ) ){
          $description = $title . " - $album";
        }else{
          $description = $title . " - $artist";
        }
      }
      // use commentCDATA instead, if present
      if ( $aItems[ 'commentCDATA' ] != '' ){
        $description = $aItems[ 'commentCDATA' ];
      }
      echo ("    <description>$description</description>\n");

      // remote item pubDate
      if ( $aItems[ 'pubDate' ] == '' ){
        $itemTimeAdjust = $filedate;
        if ( $timeAdjstMinus ) {
          $itemTimeAdjust = $filedate - (60 * 60);  // decrease an hour
        }else if ( $timeAdjstPlus ) {
          $itemTimeAdjust = $filedate + (60 * 60);  // advance an hur
        }
        $pubdate = fixDate( date("r", $itemTimeAdjust) );
      }else{
        $pubdate = $aItems[ 'pubDate' ];
      }
      echo ("    <pubDate>$pubdate</pubDate>\n");

      // remote item author
      if ( $aItems[ 'author' ] == '' ){
       $author = $artist;
      }else{
       $author = $aItems[ 'author' ];
      }
      echo ("    <itunes:author>$author</itunes:author>\n");

      // remote item summary
      if ( $aItems[ 'summary' ] == '' ){
        $summary = '';
      }else{
        $summary = $aItems[ 'summary' ];
      }
      echo ("    <itunes:summary>$summary</itunes:summary>\n");

      // remote item subtitle
      if ( $aItems[ 'subtitle' ] == '' ){
        $subtitle = '';
      }else{
        $subtitle = $aItems[ 'subtitle' ];
      }
      echo ("    <itunes:subtitle>$subtitle</itunes:subtitle>\n");

      // remote item keywords
      if ( $aItems[ 'keywords' ] == '' ){
        $keywords = $keywordTAG;
        $keywords = htmlentities($keywords);
      }else{
        $keywords = $aItems[ 'keywords' ];
      }
      echo ("    <itunes:keywords>$keywords</itunes:keywords>\n");

      // remote item image
      // check to see if we want image info for each item
      if ( $imageItemTAG == "yes") {
        if ( $aItems[ 'allowItemImage' ] == '' ){
          $imageURL   = $imageUrlTAG;
          $imageTitle = $imageTitleTAG;
        }else{
          $imageURL   = $aItems[ 'imageURL' ];
          $imageTitle = $aItems[ 'imageTitle' ];
        }
        if ( $aItems['imageFileType'] == '') {
          $imageFileType = 'video/jpeg';
        }else{
          $imageFileType = $aItems['imageFileType'];
        }
        $imageURL = htmlentities(str_replace(" ", "%20", $imageURL));
        echo ("    <itunes:link rel=\"image\" type=\"$imageFileType\" href=\"$imageURL\">$imageTitle</itunes:link>\n");
      }

      // remote item duration
      if ( $aItems[ 'duration' ] == '' ){
        $duration = '00:00:00';
      }else{
        $duration = $aItems[ 'duration' ];
      }
      if ( $duration != '00:00:00' ) {
        // item duration - by Jarod 2007=11
        list($dur_hour, $dur_minute, $dur_second) = explode(":", $duration);
        $dur_total_seconds = $dur_second;
        $dur_total_seconds += $dur_minute * 60;
        $dur_total_seconds += $dur_hour * 3600;	
        $duration = gmdate("H:i:s", $dur_total_seconds);
        // end - item duration - by Jarod 2007=11
      }
      echo ("    <itunes:duration>$duration</itunes:duration>\n");

      // remote item guid
      if ( $aItems[ 'guid' ] == '' ){
        $guid = $aItems[ 'enclosureURL' ];
      }else{
        $guid = $aItems[ 'guid' ];
      }
      echo ("    <guid isPermaLink=\"false\">" . $guid . "</guid>\n");

      // remote itme close
      print "  </item>\n";
      $maxFeeds--;
    }// end while

  }//end function remoteMedia


  /*
   * Logic for variable $remoteMedia = 0
   *
   * Reads override files, if any
   * This is unchanged from versions .09i and previous
   *
   * For .mp3 media: These can override internal id3 tags
   *  obtained by reading the id3 info in the mp3 files as
   *  as well as additional rss/iTunes tags
   * For other media, you can add RSS/iTunes tags
   *  since no info from the media file is extracted
   */
  function nonRemoteMedia( $dirArray, $maxFeeds, $delim1, $sftypes,
         $overrideFileType, $aItemsEmpty, $rootMP3URL, $ownerEmailTAG,
         $timeAdjstMinus, $timeAdjstPlus, $keywordTAG, $imageItemTAG,
         $linkTAG, $overrideFolder, $imageUrlTAG, $imageTitleTAG ) {

    while ( list($filename, $filedate ) = each($dirArray) AND $maxFeeds > 0) {

      $mp3file = new CMP3File;
      $mp3file->getid3 ($filename);

      echo "  <item>\n";

      $descriptiveFileName = stripType( $delim1, $sftypes, $filename ) .
                             $overrideFileType;

      $descriptiveFileName = $overrideFolder . basename( $descriptiveFileName);

      $aItems = $aItemsEmpty;
      if ( file_exists( $descriptiveFileName ) ){
        getDescriptions( $descriptiveFileName, $aItems ); // alters aItems
      }

      // nonremote remote This is the location of the hosting page that has the enclosure
      if ( $aItems[ 'link' ] == '' ){
        $url = $rootMP3URL."/". htmlentities(str_replace(" ", "%20", $filename));
      }else{
        $url = $aItems[ 'link' ];
      }
      echo ("    <link>".$url."</link>\n"); //link string

      // nonremote item title
      if ( $aItems[ 'title' ] == '' ){
        // title tag
        $title = str_replace("_", " ", $mp3file->title);
        //handle empty title in id3 tag
        if ( empty( $title ) ){
          //use file name, drop file extension
          $title = stripType( $delim1, $sftypes, $filename );
        }
        //handle '&' in names
        $title = htmlentities( $title );
      }else{
        //title from descriptions file
        $title = $aItems[ 'title' ];
      }
      echo ("    <title>" . $title . "</title>\n"); // title string

      // nonremote item artist
      if ( $aItems[ 'artist' ] == '' ){
        $artist = $mp3file->artist;
        $artist = htmlentities($artist);
      }else{
        $artist = $aItems[ 'artist' ];
      }

      // nonremote item author
      if ( $aItems[ 'author' ] == '' ){
        $author = $ownerEmailTAG;
        $author = htmlentities($author);
      }else{
        $author = $aItems[ 'author' ];
      }
      echo ("    <author>$author</author>\n");

      // nonremote album
      if ( $aItems[ 'album' ] == '' ){
        $album = $mp3file->album;
        $album = htmlentities($album);
      }else{
        $album = $aItems[ 'album' ];
      }

      // nonremote composer
      if ( $aItems[ 'composer' ] == '' ){
        $composer = $mp3file->composer;
        $composer = htmlentities($composer);
      }else{
        $composer = $aItems[ 'composer' ];
      }

      // nonremote genre
      if ( $aItems[ 'genre' ] == '' ){
        $genre = $mp3file->genre;
        $genre = htmlentities($genre);
      }else{
        $genre = $aItems[ 'genre' ];
      }

      // nonremote year
      if ( $aItems[ 'year' ] == '' ){
        $year = $mp3file->year;
        $year = htmlentities($year);
      }else{
        $year = $aItems[ 'year' ];
      }

      // nonremote track
      if ( $aItems[ 'track' ] == '' ){
        $track = '';
      }else{
        $track = $aItems[ 'track' ];
      }

      // nonremote coyright
      if ( $aItems[ 'copyright' ] == '' ){
        $copyright = $mp3file->copyright;
        $copyright = htmlentities($copyright);
      }else{
        $copyright = $aItems[ 'copyright' ];
      }

      // nonremote item description
      $description = '';
      if ( $mp3file->comment != '' ){
        $description = $mp3file->comment;
      }
      if ( $aItems[ 'commentText' ] != '' ){
        $description = $aItems[ 'commentText' ];
      }
      if ( $description == '' ){
        if ( !empty( $album ) ){
          $description = $title . " - $album";
        }else{
          $description = $title . " - $artist";
        }
      }

      // non-remote description override, if commentCDATA is present
      if ( $aItems[ 'commentCDATA' ] != '' ){
        $description = $aItems[ 'commentCDATA' ];
      }
      echo ("    <description>$description</description>\n");

      // nonremote item pubDate
      if ( $aItems[ 'pubDate' ] == '' ){
        $itemTimeAdjust = $filedate;
        if ( $timeAdjstMinus ) {
          $itemTimeAdjust = $filedate - (60 * 60);  // decrease an hour
        }else if ( $timeAdjstPlus ) {
          $itemTimeAdjust = $filedate + (60 * 60);  // advance an hur
        }
        $pubdate = fixDate( date("r", $itemTimeAdjust) );
      }else{
        $pubdate = $aItems[ 'pubDate' ];
      }
      echo ("    <pubDate>$pubdate</pubDate>\n");


      // nonremote item enclosure
      if ( $aItems[ 'enclosureURL' ] == '' ){
        $enclosure = "\"" . $url . "\" length=\"" . filesize($filename) . "\" type=\"$mp3file->mime_type\"";
      }else{
        if ( $aItems[ 'enclosureLength' ] == '' ){
          $enclosureLength = " length=\"" . filesize($filename) . "\"";
        }else{
          $enclosureLength = " length=\"" . $aItems[ 'enclosureLength' ] . "\"";
        }
        if ( $aItems[ 'enclosureType' ] == '' ){
          $enclosureType = " type=\"$mp3file->mime_type\"";
        }else{
          $enclosureType = " type=\"" . $aItems[ 'enclosureType' ] . "\"";
        }
        $enclosure = $aItems[ 'enclosureURL' ] . $enclosureLength . $enclosureType;
      }
      echo ("    <enclosure url=$enclosure />\n");


      // nonremote item author
      if ( $aItems[ 'author' ] == '' ){
       $author = $artist;
      }else{
       $author = $aItems[ 'author' ];
      }
      echo ("    <itunes:author>$author</itunes:author>\n");

      // nonremote item summary
      if ( $aItems[ 'summary' ] == '' ){
        $summary = $mp3file->comment;
        $summary = htmlentities($summary);
      }else{
        $summary = $aItems[ 'summary' ];
      }
      echo ("    <itunes:summary>$summary</itunes:summary>\n");

      // nonremote item subtitle
      if ( $aItems[ 'subtitle' ] == '' ){
        $subtitle = $mp3file->comment;
        $subtitle = htmlentities($subtitle);
      }else{
        $subtitle = $aItems[ 'subtitle' ];
      }
      echo ("    <itunes:subtitle>$subtitle</itunes:subtitle>\n");

      // nonremote item keywords
      if ( $aItems[ 'keywords' ] == '' ){
        $keywords = $keywordTAG;
        $keywords = htmlentities($keywords);
      }else{
        $keywords = $aItems[ 'keywords' ];
      }
      echo ("    <itunes:keywords>$keywords</itunes:keywords>\n");

      // non-remote item image
      // check to see if we want image info for each item
      if ($imageItemTAG == "yes") {
        if ( $aItems[ 'allowItemImage' ] == '' ){
          $imageURL   = $imageUrlTAG;
          $imageTitle = $imageTitleTAG;
        }else{
          $imageURL   = $aItems[ 'imageURL' ];
          $imageTitle = $aItems[ 'imageTitle' ];
        }
        if ( $aItems['imageFileType'] == '') {
          $imageFileType = 'video/jpeg';
        }else{
          $imageFileType = $aItems['imageFileType'];
        }
        $imageURL = htmlentities(str_replace(" ", "%20", $imageURL));
        echo ("    <itunes:link rel=\"image\" type=\"$imageFileType\" href=\"$imageURL\">$imageTitle</itunes:link>\n");
      }

      // non-remote item duration
      $fix_duration = $mp3file->duration;
      if (strlen($fix_duration) < 3) {
        $fix_duration = "00:00:".$fix_duration;
      }
      if (strlen($fix_duration) < 6) {
        $fix_duration = "00:".$fix_duration;
      }
      // item duration
      if ( $aItems[ 'duration' ] == '' ){
        $duration = $fix_duration;
      }else{
        $duration = $aItems[ 'duration' ];
      }
      // non-remote item duration - by Jarod 2007=11
      list($dur_hour, $dur_minute, $dur_second) = explode(":", $duration);
      $dur_total_seconds = $dur_second;
      $dur_total_seconds += $dur_minute * 60;
      $dur_total_seconds += $dur_hour * 3600;	
      $duration = gmdate("H:i:s", $dur_total_seconds);
      // end - item duration - by Jarod 2007=11
      echo ("    <itunes:duration>$duration</itunes:duration>\n");

      // remote item guid
      if ( $aItems[ 'guid' ] == '' ){
        $guid = $url; // from link logic
      }else{
        $guid = $aItems[ 'guid' ];
      }
      echo ("    <guid isPermaLink=\"false\">" . $guid . "</guid>\n");

      // item close
      print "  </item>\n";
      $maxFeeds--;
    }// end while

  }//end function nonRemoteMedia


  // get the values from the override file and put
  // them in an array which will become aItems
  function getDescriptions( $filename, &$aItems ){

    $delm = '`';

    $aLines = file( $filename );
    $cdata = 0;
    foreach ( $aLines as $line_num => $line ) {
      $line = trim( $line );

      //ignore comments
      if ( subStr( $line, 0, 1 ) == '#' ){
        continue;
      }

      if ( ( $foundAt = strPos( $line, ']' ) ) !== False ){
        $type = subStr( $line, 0, $foundAt +1 );
        $str  = subStr( $line, $foundAt +1 );

        switch ( $type ){
          case '[title]':
            $aItems[ 'title' ] = $str;
            break;
          case '[link]':
            $aItems[ 'link' ] = $str;
            break;
          case '[author]':
            $aItems[ 'author' ] = $str;
            break;
          case  '[commentText]':
            $aItems[ 'commentText' ] = $str;
            break;
          case '[pubDate]':
            $aItems[ 'pubDate'] = $str;
            break;
          case '[enclosureURL]':
            $aItems[ 'enclosureURL' ] = $str;
            break;
          case '[enclosureLength]':
            $aItems[ 'enclosureLength' ] = $str;
            break;
          case '[enclosureType]':
            $aItems[ 'enclosureType' ] = $str;
            break;
          case '[subtitle]':
            $aItems[ 'subtitle' ] = $str;
            break;
          case '[keywords]':
            $aItems[ 'keywords' ] = $str;
            break;
          case '[allowItemImage]':
            $aItems[ 'allowItemImage' ] = $str;
            break;
          case '[imageURL]':
            $aItems[ 'imageURL' ] = $str;
            break;
          case '[imageTitle]':
            $aItems[ 'imageTitle' ] = $str;
            break;
          case '[imageFileType]':
            $aItems[ 'imageFileType' ] = $str;
            break;
          case '[duration]':
            $aItems[ 'duration' ] = $str;
            break;
          case '[guid]':
            $aItems[ 'guid' ] = $str;
            break;
          case '[artist]':
            $aItems[ 'artist' ] = $str;
            break;
          case '[album]':
            $aItems[ 'album' ] = $str;
            break;
          case '[composer]':
            $aItmes[ 'composer' ] = $str;
            break;
          case '[genre]':
            $aItems[ 'genre' ] = $str;
            break;
          case '[year]':
            $aItmes[ 'year' ] = $str;
            break;
          case  '[track]':
            $aItmes[ 'track' ] = $str;
            break;
          case '[copyright]':
            $aItems[ 'copyright' ] = $str;
            break;
          case '[summary]':
            $aItems[ 'summary' ] = $str;
            break;
          case '[commentCDATA]':
            if ( !$cdata ){
              $cdata = 1;
              $aItems[ 'commentCDATA' ] = $str;
            }
            break;
          default:
            if ( $cdata ){
      $aItems[ 'commentCDATA' ] = $str;
            }
        }// end switch
      }else{
        if ( $cdata ){
          $aItems[ 'commentCDATA' ] .= $line;
        }
      }// if

    }//foreach

    if ( $aItems[ 'commentCDATA' ] != '' ){
      $aItems[ 'commentCDATA' ] = '<![CDATA[' .
                                  $aItems[ 'commentCDATA' ] . "]]>";
    }

  }//end function getDescriptions

  // For $remoteMedia = 1, read all override text files and sor the array
  // by pubdate, if missing use override text file file date
  function sortByPubdate( $dirArray ) {

    $newArray = array();

    while ( list( $filename, $filedate ) = each( $dirArray ) ){

      $descriptiveFileName = $filename;
      if ( file_exists( $descriptiveFileName ) ){
        $filedate = getPubDate( $descriptiveFileName, $filedate );
      }else{
        continue;
      }

      $newArray[$filename] = $filedate;

    }//end while
    asort( $newArray );
    $newArray = array_reverse( $newArray );

    return $newArray;

  }// end function sortByPubdate

  // nonRemote For $remoteMedia = 01, read all override text files and sort the array
  // by pubdate, if missing use override text file file date
  function nonRemotesortByPubdate( $dirArray, $delim1, $sftypes,
           $overrideFolder, $overrideFileType ) {

    $newArray = array();

    while ( list( $filename, $filedate ) = each( $dirArray ) ){

      $descriptiveFileName = stripType( $delim1, $sftypes, $filename ) .
                                        $overrideFileType;
      $descriptiveFileName = $overrideFolder . basename( $descriptiveFileName);

      if ( file_exists( $descriptiveFileName ) ){
        $filedate = getPubDate( $descriptiveFileName, $filedate );
      }else{
        $newArray[$filename] = $filedate;
        continue;
      }

      $newArray[$filename] = $filedate;

    }//end while
    asort( $newArray );
    $newArray = array_reverse( $newArray );

    return $newArray;

  }// end function nonRemotesortByPubdate

  /*
   * find pubDate tag in given override text file
   * otherwise return override text file date
  */
  function getPubDate( $filename, $filedate ) {
    $rtn = $filedate;
    $delm = '`';
    $aLines = file( $filename );
    foreach ( $aLines as $line_num => $line ) {
      $line = trim( $line );

      //ignore comments
      if ( subStr( $line, 0, 1 ) == '#' ){
        continue;
      }

      if ( ( $foundAt = strPos( $line, ']' ) ) !== False ){
        $type = subStr( $line, 0, $foundAt +1 );

        if ( $type == '[pubDate]' ){

          $rtn = subStr( $line, $foundAt +1 );

          $rtn = strToTime( $rtn);
        }
      }
    }//foreach

    return $rtn;

  }//end function getPubDate

  /*
   * end dirCaster.php
  */
?>