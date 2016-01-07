<?php
  /*  override.php
   *  Used to maintain override text files for Dircaster, if such are used.
   *  Individual override values are not always required, so there is
   *  no forced requirements. (ie: you may save a file with validation errors)
   *  Instead some helper validation has been provided.
   *
   *  This program uses AJAX (JSON format).
   *   'Form submit' is suspended: data and validation are obtained via AJAX.
   *   The connection between the PHP program, JavaScript and CSS is
   *   tight.
   *     . Changing a 'class' name may break this interaction.
   *     . Changing an 'id' will most certainly break the program.
   *
   *  This program set:
   *    override.php
   *      override.js
   *      override.css
   *    override_ajax_json.php
   *    override_ajax_util.php
   *  This program must be in the same folder as it's parent program
   *  set:
   *    dircaster.php
   *    config_inc.php.
   *
   *  Additionally:
   *    . In config_inc.php, the variable $overrideFolder must be valued.
   *    . There must be a sub-folder 'templates' under the folder
   *      referenced by $overrideFolder in config_inc.php
   *    . Static dropdowns are controlled by functions at the end of
   *       the program. You can maintain such things as mine types and
   *       time zone ( select-options) by adding or deleting from the
   *       array at the top of each function.
   *
   *  At the initial writing:
   *     . Development was done with EPHP error level _ALL. There should be no notices or warnings.
   *     . The CSS validates
   *     . The HTML validates
   *       The screen layout was designed for 800x600.
   *    Any future maintenance should try to keep this level of validation.
  */

  /*
   *  get info from config_inc.php used by dircaster.php
   */
  require( 'config_inc.php' );
  $remoteMediaFlag       = $remoteMedia;
  $newOverrideFolder     = $overrideFolder;
  $newOverrideFileType   = $overrideFileType;
  $newOverrideFileName   = '';
  $RSS_link              = $linkTAG;

  if ( $remoteMediaFlag ){
    $remoteMediaFlagValue = 'Remote media files';
    $reqFlag       = '*';
  }else{
    $remoteMediaFlagValue = 'Non Remote Mode (local media files)';
    $reqFlag       = '';
  }

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">

 <head>
  <title>Dircaster Override Text File Generator</title>

  <meta http-equiv="content-type" content="text/html;charset=utf-8" />
  <meta name="description" content="Override.php - Companion to Dircaster.php ) for generating override text files." />
  <meta name="keywords" content="dircaster, poscasts" />
  <meta name="author" content="H.L.Ratliff" />
  <meta name="revised" content="<?php echo date( 'Y-m-d h:m:s', time());?>" scheme="YYYY-MM-DD H:M:S"/>
  <meta name="language" content="en" />


  <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js"></script>
  
<!--
   Replace the line above with this if you move the file local...
   <script type="text/javascript" src="../js/jquery-1.7.min.js"></script>
-->

  <script type="text/javascript" src="./override.js"></script>
  <link href="./override.css" type="text/css" rel="stylesheet" />

 </head>

 <body>

 <h1>DirCaster Override Generator</h1>
  
  <h3>REMEMBER: You MUST have the remoteMedia variable set to '1' in config_inc.php!</h3>

  <?php
    if ( $remoteMediaFlag ){
      $rText = 'Working with remote/Cloud operations for file ';
    }else{
      $rText = 'Working with non remote/local operations for file ';
    }
    echo "\n" . '<div class="this-operation" id="this-operation">' .
         $rText . '<span id="operation-file"></span>' . '</div>';
  ?>

  <!-- start form data -->
  <form class="frm" name="form-fields" id="form-fields" action="" method="post" >

   <div class="file-info" id="file-info" >
    <!-- start override file -->
     <div class="sect-head">The Override file</div>
     <div class="col-right"></div>
     <div class="clear"></div>

     <div class="col-left">Remote media flag: </div>
     <div class="col-right"><?php echo $remoteMediaFlag . ' = ' . $remoteMediaFlagValue;?> (from config_inc.php)</div>
     <div class="clear"></div>

     <div class="col-left">Override Folder: </div>
     <div class="col-right"><span id="newOverrideFolder"><?php echo $newOverrideFolder;?></span> (from config_inc.php)</div>
     <div class="clear"></div>

     <div class="col-left"><span class="requirements-flag"><?php echo $reqFlag;?> </span>Override File Name: </div>
     <div class="col-right"><input name="newOverrideFileName"  id="newOverrideFileName" type="text" value="" title="File name of the override file, excluding extension." />
      &lt;&lt;&lt;
      <select class="file-select" id="file-select" title="Current override files. Edit (keep the name unchanged) or new (enter a new name).">
       <?php genFileOptions( $newOverrideFolder, $newOverrideFileType, 'e' );?>
      </select>
      &lt;&lt;&lt;
      <select class="template-select" id="template-select" title="Start new override file with data from this template.">
       <?php genFileOptions( $newOverrideFolder . '/templates', '.otf', 't' );?>
      </select>
     </div>
     <div class="clear"></div>

     <div class="col-left">Override File Type: </div>
     <div class="col-right"><span id="newOverrideFileType"><?php echo $newOverrideFileType;?></span> (from config_inc.php)</div>
     <div class="clear"></div>
     <button class="file-choice-btn" id="file-choice-btn" type="button" title="You may create a new override file (enter a  file name), edit an existing file (from the dropdown) or start with the contents of a template file (from the dropdown)">Enter an Override File Name to proceed</button>
     <button class="reset-file-area-btn" id="reset-file-area-btn" type="button" title="Clear and start again.">Reset</button>
     </div>
    <!-- end file-info -->

    <!-- start form input -->
    <div id="show-fields" style="display: none;">
      <div class="requirements-note">
       Required fields are shown with a preceeding *. These are program
       requirements and along with the config_inc.php defaults will
       supply most RSS reader requirements. iTunes tags generated are
       valid, but you should consider which ones you want iTunes to see.
       <br />
      </div>

      <!-- media file -->
      <div class="sect-head">Media file</div>
      <div class="sect-right"></div>
      <div class="clear"></div>


      <?php
      $lt  = "&lt;";
      $gt  = "&gt;";
      $dft = " Dircaster default: ";
      ?>


      <?php
      $tag  = $lt . 'item' . $gt . $lt . 'enclosure' . $gt . ' href attribute. ';
      $tagS = '';
      $hT   = $tag . $dft . "The 'link' value below. However it is best to code the enclosureURL and it's components, leaving the link available to point to a web page.";
      $iT   = $tag . "Media link (http://...)";
      ?>
      <div class="col-left">
       <span class="requirements-flag"> <?php echo $reqFlag;?></span> [enclosureURL]:
       <span class="help-q" title="<?php echo $hT;?>"> Help </span>
      </div>
      <div class="col-right">
       <input name="enclosureURL" id="enclosureURL" type="text" value=""        title="<?php echo $iT;?>" size="70" />
       <button type="button" class="enclosureURL-verify-btn" id="enclosureURL-verify-btn" title="Format test link.">&lt; Test</button>
      </div>
      <div class="clear-2"></div>

      <!-- message -->
      <div class="col-left">&nbsp;</div>
      <div class="col-right">
        <div class="enclosureURL-verify-msg" id="enclosureURL-verify-msg"></div>
      </div>
      <div class="clear-2"></div>
      <!-- end enclosure URL -->


      <?php
      $tag  = $lt . 'item' . $gt . $lt . 'enclosure' . $gt . ' length attribute. ';
      $tagS = '';
      $hT   = $tag . $dft . "None. If you enter an enclosureURL, enter the length.";
      $iT   = $tag . "Length in bytes of the media.";
      ?>
      <!-- start enclosure length -->
      <div class="col-left">
       <span class="requirements-flag"> <?php echo $reqFlag;?></span> [enclosureLength]:
       <span class="help-q" title="<?php echo $hT;?>"> Help </span>
      </div>
      <div class="col-right">
       <input name="enclosureLength" id="enclosureLength" type="text" value="" title="<?php echo $iT;?>" />
       <button type="button" class="enclosure-length-verify-btn" id="enclosure-length-verify-btn" title="Verify length, also removes commas.">&lt; Verify</button>
      </div>
      <div class="clear"></div>

      <!-- message -->
      <div class="col-left">&nbsp;</div>
      <div class="col-right">
       <div class="enclosure-length-verify-msg" id="enclosure-length-verify-msg"></div>
      </div>
      <div class="clear-2"></div>
      <!-- end enclosure length -->


      <?php
      $tag  = $lt . 'item' . $gt . $lt . 'enclosure' . $gt . ' type attribute. ';
      $tagS = '';
      $hT   = $tag . $dft . "None. A mime type. ex: video/m4v";
      $iT   = $tag . "A mime type. ex: video/m4v";
      ?>
      <!-- start enclosure type -->
      <div class="col-left">[enclosureType]:
       <span class="help-q" title="<?php echo $hT;?>"> Help </span>
      </div>
      <div class="col-right">
       <input name="enclosureType" id="enclosureType" type="text" value="" title="<?php echo $iT;?>" />
       &lt;&lt;&lt;
       <select class="mime-select" id="mime-select"  title="Choose one.">
         <?php echo genMimeTypes();?>
       </select>
      </div>
      <div class="clear-2"></div>
      <!-- end enclosure type -->



      <?php
      $tag  = $lt . 'item' . $gt . $lt . 'itunes:duration' . $gt;
      $tagS = '';
      $hT   = $tag . $dft . "00:00:00. iTunes time.";
      $iT   = $tag . "iTunes time. Format: hh:mm:ss, h:mm:ss, mm:ss, m:ss, ss.";
      ?>
      <!-- start enclosure duration -->
      <div class="col-left">[duration]:
       <span class="help-q" title="<?php echo $hT;?>"> Help </span>
      </div>
      <div class="col-right">
       <input name="duration" id="duration" type="text" value="" size="6" maxlength="8" title="<?php echo $iT;?>" />
       <button type="button" class="duration-verify-btn" id="duration-verify-btn" title="Verify duration.">&lt; Verify</button>
      </div>
      <div class="clear"></div>

      <!-- message -->
      <div class="col-left">&nbsp;</div>
      <div class="col-right">
       <div class="duration-verify-msg" id="duration-verify-msg"></div>
      </div>
      <div class="clear"></div>
      <!-- end enclosure duration -->


      <!-- Podcast info -->
      <div class="sect-head">Podcast info</div>
      <div class="clear-2"></div>


      <?php
      $tag  = "&lt;item&gt;&lt;title&gt;: ";
      $tagS = '';
      $hT   = $tag . $dft . "For non-remote: the file name. For remote: it is required.";
      $iT   = $tag . "A text name for the media/podcast.";
      ?>
      <!-- start title -->
      <div class="col-left">
       <span class="requirements-flag"><?php echo $reqFlag;?></span>[title]:
       <span class="help-q" title="<?php echo $hT;?>"> Help </span>
      </div>
      <div class="col-right">
       <input name="title" id="title" type="text" value="" title="<?php echo $iT;?>" size="90" />
      </div>
      <div class="clear-2"></div>
      <!-- end title -->


      <?php
      $tag  = $lt  . 'item' . $gt . $lt . 'link' . $gt;
      $tagS = '';
      $hT   = $tag . $dft . "Dircaster default: For non-remote: Your current host name plus the file name. For remote: it is required. May be a web page (Home Page or Blog with the media) or the media link. This is the action that takes place when the title above is clicked.";
      $iT   = $tag . "Web page URL or media link.";
      ?>
      <!-- start link -->
      <div class="col-left">
       <span class="requirements-flag"> <?php echo $reqFlag;?></span>[link]:
       <span class="help-q"
             title="&lt;item&gt;&lt;link&gt;: Dircaster default: For non-remote: Your current host name plus the file name, for remote, it is required. May be a web page (Home Page or Blog witht the media) or the media link. This is the action that takes place when the title above is clicked."> Help </span>
      </div>
      <div class="col-right">
       <input name="link" id="link" type="text" value="" title="<?php echo $iT;?>" size="70" />
       <button type="button" class="link-same-as-btn" id="link-same-as-btn" title="Use enclosureURL value."> &lt; enclosureURL</button>
      </div>
      <div class="clear-2"></div>
      <!-- end link -->


      <?php
      $tag  = $lt  . 'item' . $gt . $lt . 'pubDate' . $gt;
      $tagS = '';
      $hT   = $tag . $dft . "For non-remote: The media file date. For remote: Date of this file. Best to always code.";
      $iT   = $tag . "In the form: Mon, 03 Sep 2012 04:15:00 CDT.";
      ?>
      <!-- start pubdate -->
      <div class="col-left">[pubDate]
       <span class="help-q" title="<?php echo $hT;?>"> Help </span>
      </div>
      <div class="col-right"><input name="pubDate" id="pubDate" type="text" value="" title="<?php echo $iT;?>" size="31" maxlength="31" />
       <button type="button" class="pubdate-now-btn" id="pubdate-now-btn" title="Use today's date and time as the pubdae." >&lt; Now</button>
       <button type="button" class="pubdate-verify-btn" id="pubdate-verify-btn" title="Verify pubdae.">&lt; Verify</button>
       Help <button type="button" class="pubdate-select-div-btn" id="pubdate-select-div-btn" title="Show/hide pubdate dorpdown helpers.">[+]</button>
      </div>
      <div class="clear"></div>

      <!-- message -->
      <div class="col-left">&nbsp;</div>
      <div class="col-right">
       <div class="pubdate-time-msg" id="pubdate-time-msg"></div>
      </div>
      <div class="clear"></div>

      <!-- selects -->
      <div class="col-left">&nbsp;</div>
      <div class="col-right-pad">
       <div class="pubdate-select-div" id="pubdate-select-div">
         <?php genPubdateSelects();?>
       </div>
      </div>

      <div class="clear-2"></div>
      <!-- end pubdate -->


      <?php
      $tag  = $lt  . 'item' . $gt . $lt . 'itunes:subtitle' . $gt;
      $tagS = '';
      $hT   = $tag . $dft . "For non-remote: D3 subtitle if available, else none. For remote: Code for iTunes.";
      $iT   = $tag . 'itunes:subtitle' . $gt;
      ?>
      <!-- start subtitle -->
      <div class="col-left">[subtitle]:
       <span class="help-q" title="<?php echo $hT;?>"> Help </span>
       <br />Length: <span id="subtitle-length">0</span>
      </div>
       <textarea cols="70" rows="4" name="subtitle" id="subtitle" title="<?php echo $iT;?>"></textarea>
      <div class="clear-2"></div>
      <!-- end subtitle -->


      <?php
      $tag  = $lt  . 'item' . $gt . $lt . 'author' . $gt;
      $tagS = '';
      $hT   = $tag . $dft . "The ownerEmailTAG from config_inc.php.";
      $iT   = $tag . 'Media author.';
      ?>
      <!-- start author -->
      <div class="col-left">[author]:
       <span class="help-q" title="<?php echo $hT;?>"> Help </span>
      </div>
      <div class="col-right">
       <input name="author" id="author" type="text" value="" title="<?php echo $iT;?>" size="60" />
      </div>
      <div class="clear-2"></div>
      <!-- end author -->


      <?php
      $tag  = '';
      $tagS = $lt . 'item' . $gt .  $lt . 'description' . $gt;
      $hT   = $tag . $dft . "For non-remote: ID3 album, if available, else none. For remote: none. Appended to the " . $tagS . ' if valued.';
      $iT   = 'Appended to the ' . $tagS;
      ?>
      <!-- start album -->
      <div class="col-left">[album]:
       <span class="help-q" title="<?php echo $hT;?>"> Help </span>
      </div>
      <div class="col-right">
       <input name="album" id="album" type="text" value="" title="<?php echo $iT;?>" size="60"/>
      </div>
      <div class="clear-2"></div>
      <!-- end album -->


      <?php
      $tag  = '';
      $tagS = $lt . 'item' . $gt .  $lt . 'description' . $gt;
      $hT   = $tag . $dft . "For non-remote: ID3 artist if available, else none. For non-remote and remote: Used for author is no author is valued. Also, if the description and album are not valued, used with title to complement the description.";
      $iT   = 'Appended to the ' . $tagS . ' if album not valued.';
      ?>
      <!-- start artist -->
      <div class="col-left">[artist]:
       <span class="help-q" title="<?php echo $hT;?>"> Help </span>
      </div>
      <div class="col-right">
       <input name="artist" id="artist" type="text" value="" title="<?php echo $iT;?>" size="60"/>
      </div>
      <div class="clear-2"></div>
      <!-- end artist -->


      <?php
      $tag  = $lt  . 'item' . $gt . $lt . 'itunes:summary' . $gt;
      $tagS = '';
      $hT   = $tag . $dft . "For non-remote: ID3 artist if available, else none. For remote none.";
      $iT   = $tag;
      ?>
      <!-- start summary -->
      <div class="col-left">[summary]:
       <span class="help-q"  title="<?php echo $hT;?>"> Help </span>
       <br />Length: <span id="summary-length">0</span>
      </div>
       <textarea cols="70" rows="4" name="summary" id="summary" title="<?php echo $iT;?>"></textarea>
      <div class="clear-2"></div>
      <!-- end summary -->


      <?php
      $tag  = $lt  . 'item' . $gt . $lt . 'itunes:keywords' . $gt;
      $tagS = '';
      $hT   = $tag . $dft . "The keywordTAG from config_inc.php. Keywords separated by commas.";
      $iT   = $tag . "Keywords separated by commas.";
      ?>
      <!-- start keywords -->
      <div class="col-left">[keywords]:
       <span class="help-q" title="<?php echo $hT;?>"> Help </span>
      <br />Length: <span id="keywords-length">0</span>
      </div>
       <textarea cols="70" rows="4" name="keywords" id="keywords" title="<?php echo $iT;?>" ></textarea>
      <div class="clear-2"></div>
      <!-- end keywords -->


      <?php
      $tag  = $lt  . 'item' . $gt . $lt . 'guid' . $gt;
      $tagS = '';
      $hT   = $tag . $dft . 'EnclosureURL.';
      $iT   = $tag . '';
      ?>
      <!-- start guid -->
      <div class="col-left">[guid]:
       <span class="help-q" title="<?php echo $hT;?>"> Help </span>
      </div>
      <div class="col-right">
       <input name="guid" id="guid" type="text" value="" title="<?php echo $iT;?>" size="70"/>
       <button type="button" class="guid-same-as-btn" id="guid-same-as-btn" title="Use enclosureURL value."> &lt; enclosureURL</button>
      </div>
      <div class="clear-2"></div>
      <!-- end guid -->


      <!-- commentTextor commentCDATA -->
      <?php echo "\n<br />";?>
      <?php echo "\nCode one, but not both for &lt;description&gt;";?>
      <?php echo "\n<br />";?>


      <?php
      $tag  = $lt  . 'item' . $gt . $lt . 'description' . $gt;
      $tagS = '';
      $hT   = $tag . $dft . "Text only (Use commentCDATA for HTML). Code only one. commentCDATA overrides commentText";
      $iT   = $tag . "Text only (Use commentCDATA for HTML).";
      ?>
      <!-- start commentText -->
      <div class="col-left">[commentText]:
       <span class="help-q" title="<?php echo $hT;?>"> Help </span>
      <br />Length: <span id="commentText-length">0</span>
      </div>
       <textarea cols="70" rows="4" name="commentText" id="commentText" title="<?php echo $iT;?>"></textarea>
      <div class="clear"></div>
      <!-- end commentText -->


      <?php
      $tag  = $lt  . 'item' . $gt . $lt . 'description' . $gt;
      $tagS = '';
      $hT   = $tag . $dft . '';
      $iT   = $tag . '';
      ?>
      <!-- start commetnCDATA -->
      <div class="col-left">[commentCDATA]:
       <span class="help-q" title="<?php echo $hT;?>"> Help </span>
      <br /> Length: <span id="commentCDATA-length">0</span>
      </div>
      <div class="col-right">
       <textarea cols="70" rows="4" name="commentCDATA" id="commentCDATA" title="<?php echo $iT;?>&lt;item&gt; &lt;description&gt; Text or HTML. Overlays commentText if present."></textarea> </div>
      <div class="clear-2"></div>
      <!-- end commetnCDATA -->


      <?php
      $tag  = $lt  . 'item' . $gt . $lt . 'itunes:link' . $gt;
      $tagS = '';
      $hT   = "The next four fields concern the item itunes image tag. " .
              "It is necessary to indicate it's use, since the Feed Validator " .
              "gives an error for this tag. " .
              "The imageItemTAG in confi_inf.php must be 'yes'. " .
              "It is currently '" . $imageItemTAG . "'. " .
              "You may wish to use an " .
              'HTML img tag in the comment CDATA, after moveing any commentText data.';
      $iT = '';
      ?>
      <!-- start image link set -->
      <?php echo "\n<br />";?>
      <?php echo "\n $tag for image.";?>
      <span class="help-q" title="<?php echo $hT;?>"> Help </span>
      <?php echo "\n<br />";?>


      <?php
      $tag  = $lt  . 'item' . $gt . $lt . 'itunes:link' . $gt;
      $tagS = '';
      $hT   = $tag . $dft . "no. 'yes' to use item itues:link tag. The itemImageTAG in config_inc.php must also equal 'yes'";
      $iT   = $tag . "'yes' or 'no'.";
      ?>
      <!-- start allowItemImage -->
      <div class="col-left">[allowItemImage]:
       <span class="help-q" title="<?php echo $hT;?>"> Help </span>
      </div>
      <div class="col-right">
       <input name="allowItemImage" id="allowItemImage" type="text" value="" title="<?php echo $iT;?>" size="3"/>
       &lt;&lt;&lt;
       <select class="allow-image-select" id="allow-image-select"  title="Choose one.">
        <?php echo genYesNo();?>
      </select>
      </div>
      <div class="clear-2"></div>
      <!-- end  allowItemImage -->
      <?php
      $tag  = $lt  . 'item' . $gt . $lt . 'itunes:link' . $gt . ' href attribute. ';
      $tagS = '';
      $hT   = $tag . $dft . 'imageUrlTAG from config_inc.php.';
      $iT   = $tag . 'Link to the image. (http://....)';
      ?>
      <!-- start imageURL -->
      <div class="col-left">[imageURL]:
       <span class="help-q" title="<?php echo $hT;?>"> Help </span>
      </div>
      <div class="col-right">
       <input name="imageURL" id="imageURL" type="text" value="" title="<?php echo $iT;?>>" />
      </div>
      <div class="clear-2"></div>
      <!-- end imageURL -->


      <?php
      $tag  = $lt  . 'item' . $gt . $lt . 'itunes:link' . $gt . ' href text. ';
      $tagS = '';
      $hT   = $tag . $dft . ' imageTitleTAG from config_inc.php.';
      $iT   = $tag . 'Link text.';
      ?>
      <!-- start imageTitle -->
      <div class="col-left">[imageTitle]:
       <span class="help-q" title="<?php echo $hT;?>"> Help </span>
      </div>
      <div class="col-right">
       <input name="imageTitle" id="imageTitle" type="text" value="" title="<?php echo $iT;?>" />
      </div>
      <div class="clear-2"></div>
      <!-- end imageTitle -->


      <?php
      $tag  = $lt  . 'item' . $gt . $lt . 'itunes:link' . $gt . ' type attribute. ';
      $tagS = '';
      $hT   = $tag . $dft . 'None.';
      $iT   = $tag . "File type. Format: 'video/jpeg'";
      ?>
      <!-- start imageFileType -->
      <div class="col-left">[imageFileType]:
       <span class="help-q" title="<?php echo $hT;?>"> Help </span>
      </div>
      <div class="col-right">
       <input name="imageFileType" id="imageFileType" type="text" value="" title="<?php echo $iT;?>" />
       &lt;&lt;&lt;
       <select class="image-type-select" id="image-type-select"  title="Choose one.">
        <?php echo genImageTypes();?>
      </select>
      </div>
      <div class="clear-2"></div>
      <!-- end imageFileType -->
      <!-- end image link set -->


      <!-- start control buttons -->
      <div class="col-submit"><button class="show-preview-btn" id="show-preview-btn" type="button" title="Preview before saving">Preview</button></div>
      <div class="col-buttons-right" >
        <button class="override-save-file-btn" id="override-save-file-btn" type="button" title="Save new file or overwrite edited file.">Save</button>
        <span class="override-save-file-msg" id="override-save-file-msg">Not yet saved: </span>
        <?php
          if ( $remoteMediaFlag ){
            $rText = 'Override file';
          }else{
            $rText = 'Override new file';
          }
          echo "\n" . $rText . '<span id="override-save-file-name">: </span>';
          echo "\n" . '<span class="override-save-file-link" id="override-save-file-link"></span>';
        ?>
        <br />
        <div class="template-save-file-div" id="template-save-file-div">
          <button class="template-save-file-btn" id="template-save-file-btn" type="button" title="Write as templage file. This DOES NOT alos act as the Save button.">Writetemplate</button>
          <input class="template-save-file-input" id="template-save-file-input" type="text" title="Name for template file." />
          <span class="template-save-file-msg" id="template-save-file-msg">Not yet saved: </span>
          <span class="template-save-file-name" id="template-save-file-name"></span>
          <span class="template-save-file-link" id="template-save-file-link"></span>
        </div>
      </div>
      <div class="clear"></div>

      <div>
        <button class="close-fields-btn" id="close-fields-btn" type="button" title="Close/enter new file, does not save current file">Close</button>
      </div>
      <div class="clear"></div>
      <!-- end control buttons -->

    </div>
   <!-- end show-fields -->

  </form>
  <!-- end form data -->


  <?php
  // Preview
  // populate when button is clicked
  echo "\n" . '<!-- start div preview -->';
  echo "\n" . '<div class="preview" id="preview" style="display: none;">';

    echo "\n" . '<div class="r-col"><b>#Media file</b></div>';
    echo "\n" . '<div class="clear"></div>';

    echo "\n" . '<div class="r-col"><b>[enclosureURL]</b></div><div class="r-col" id="renclosureURL"></div>';
    echo "\n" . '<div class="clear"></div>';

    echo "\n" . '<div class="r-col"><b>[enclosureLength]</b></div><div class="r-col" id="renclosureLength"></div>';
    echo "\n" . '<div class="clear"></div>';

    echo "\n" . '<div class="r-col"><b>[enclosureType]</b></div><div class="r-col" id="renclosureType"></div>';
    echo "\n" . '<div class="clear"></div>';

    echo "\n" . '<div class="r-col"><b>[duration]</b></div><div class="r-col" id="rduration"></div>';
    echo "\n" . '<div class="clear"></div>';

    echo "\n" . '<div class="r-col"><b>#Podcast info</b></div>';
    echo "\n" . '<div class="clear"></div>';

    echo "\n" . '<div class="r-col"><b>[title]</b></div><div class="r-col" id="rtitle"></div>';
    echo "\n" . '<div class="clear"></div>';

    echo "\n" . '<div class="r-col"><b>[link]</b></div><div class="r-col" id="rlink"></div>';
    echo "\n" . '<div class="clear"></div>';

    echo "\n" . '<div class="r-col"><b>[pubDate]</b></div><div class="r-col" id="rpubDate"></div>';
    echo "\n" . '<div class="clear"></div>';

    echo "\n" . '<div class="r-col"><b>[subtitle]</b></div><div class="r-col" id="rsubtitle"></div>';
    echo "\n" . '<div class="clear"></div>';

    echo "\n" . '<div class="r-col"><b>[author]</b></div><div class="r-col" id="rauthor"></div>';
    echo "\n" . '<div class="clear"></div>';

    echo "\n" . '<div class="r-col"><b>[album]</b></div><div class="r-col" id="ralbum"></div>';
    echo "\n" . '<div class="clear"></div>';

    echo "\n" . '<div class="r-col"><b>[artist]</b></div><div class="r-col" id="rartist"></div>';
    echo "\n" . '<div class="clear"></div>';

    echo "\n" . '<div class="r-col"><b>[summary]</b></div><div class="r-col" id="r"></div>';
    echo "\n" . '<div class="clear"></div>';

    echo "\n" . '<div class="r-col"><b>[keywords]</b></div><div class="r-col" id="rkeywords"></div>';
    echo "\n" . '<div class="clear"></div>';

    echo "\n" . '<div class="r-col"><b>[guid]</b></div><div class="r-col" id="rguid"></div>';
    echo "\n" . '<div class="clear"></div>';

    echo "\n" . '<div class="r-col"><b>[commentText]</b></div><div class="r-col" id="rcommentText"></div>';
    echo "\n" . '<div class="clear"></div>';

    echo "\n" . '<div class="r-col"><b>[commentCDATA]</b></div><div class="r-col" id="rcommentCDATA"></div>';
    echo "\n" . '<div class="clear"></div>';

    echo "\n" . '<div class="r-col"><b>[allowItemImage]</b></div><div class="r-col" id="rallowItemImage"></div>';
    echo "\n" . '<div class="clear"></div>';

    echo "\n" . '<div class="r-col"><b>[imageURL]</b></div><div class="r-col" id="rimageURL"></div>';
    echo "\n" . '<div class="clear"></div>';

    echo "\n" . '<div class="r-col"><b>[imageTitle]</b></div><div class="r-col" id="rimageTitle"></div>';
    echo "\n" . '<div class="clear"></div>';

    echo "\n" . '<div class="r-col"><b>[imageFileType]</b></div><div class="r-col" id="rimageFileType"></div>';
    echo "\n" . '<div class="clear"></div>';

    echo "\n" . '<br />';


    echo "\n" . '<button class="cancel-preview-btn" id="cancel-preview-btn" type="button">Cancel</button>';
    echo "\n" . '<br />';

    echo "\n" . '</div><!-- end div-preview -->';
    echo "\n" . '<br />';

  ?>
  <br />
  Validation
  <br />
  <a href="http://feedvalidator.org?url=<?php echo $RSS_link;?>" target="blank" title="Validates this Dircaster feed. ">RSS Validator</a> Using $linkTAG from config_inc.php
  <br />
  <a href="<?php echo $RSS_link;?>" target="blank" title="View this Dircaster feed.">Test feed in browser (Raw XML can be seen via a 'View Page Source' on that page.)</a>
  <br />
  <br />

  Reference
  <br />
  <?php
    $c = strRpos( $RSS_link, '/' );
    if ( $c !== false ){
      $notes = substr( $RSS_link, 0, $c +1 ) . 'override_notes.php.htm';
      echo '<a href="' . $notes . '" target="_blank" title="Program notes.">Help for this program</a>';
    }
  ?>
  <a href="<? echo $notes;?>" target="_blank" title="">Help for this program</a>
  <br />
  <a href="http://www.apple.com/itunes/podcasts/specs.html#_Toc526931674" target="_blank" title="Making a podcast">iTunes Reference</a>
  <br />
  <a href="http://dircaster.org/" target="_blank" title="DirCaster Home Page">DirCaster.org</a>
  <br />
  <a href="http://www.timeanddate.com/library/abbreviations/timezones/" title="Time zones can be maintained in the array at the end of override.php..">Time Zone Table</a>

 </body>
</html>

<?php

  function genFileOptions( $folder, $extension, $type ) {
    // options are sorted bia JavaScript
    $folder   = $folder . '/';
    if ( is_dir( $folder ) ){
      $dh = opendir( $folder );
      if ( $type == 'e' ){
        echo "\n" . '  <option value="-">-- Use Current --</option>' . "\n";
      }else{
        echo "\n" . '  <option value="-">-- Use Template --</option>' . "\n";
      }
      while ( ( $file = readdir($dh) ) !== false ) {
        if ( filetype( $folder . $file ) == "file" && $file[0]  != ".") {
          $fext = strrchr(strtolower($file), ".");
          if ( strpos( $extension, $fext ) !== FALSE) {
            $ftime = filemtime( $folder . $file );
            $a = pathinfo( $file );
            $val = $a['filename'];
            echo '  <option value="'. $val . '">' . $val . "</option>\n";
          }
        }
      }// end while
      closedir($dh);
    }
  }// end function genFileOptions


  function genPubdateSelects(){
  ?>
    Day <select class="pubdate-select" id="pubdate-select-day" title="The alphabetic day of the week, in the pubdate.">
      <?php echo genDaySelect();?>
    </select><b>, </b>
    Day # <select class="pubdate-select" id="pubdate-select-nnn" title="The numeric day of the week,in the pubdate.">
       <?php echo genNumericOptions( 1, 31 );?>
    </select>
    Month <select class="pubdate-select" id="pubdate-select-month" title="The alphabetic month of the year, in the pubdate.">
      <?php echo genMonthSelect();?>
    </select>
    Year <select class="pubdate-select" id="pubdate-select-year" title="The 4 digit numeric year, in the pubdate.">
       <?php echo genYearSelect();?>
    </select>
    <br />
    hh <select class="pubdate-select" id="pubdate-select-hh" title="The numeric hour(s), in the pubdate.">
       <?php echo genNumericOptions( 0, 23 );?>
    </select><b>: </b>
    mm <select class="pubdate-select" id="pubdate-select-mm" title="The numeric minute(s), in the pubdate.">
     <?php echo genNumericOptions( 0, 59 );?>
    </select><b>: </b>
    ss <select class="pubdate-select" id="pubdate-select-ss" title="The numeric second(s), in the pubdate.">
      <?php echo genNumericOptions( 0, 59 );?>
    </select>
    Timezone <select class="pubdate-select" id="pubdate-select-tz" title="The time zone, in the pubdate.">
      <?php echo genTimezoneSelect();?>
    </select>
  <?php
  }//end function genPubdateSelects


  function genNumericOptions( $start, $end ) {
    $rtn = '';

    $cnt = $end;
    $val = $start;
    while ( $start <= $cnt ){
      $show = sprintf("%02d", $val );
      $rtn .= "\n" . '<option value="' . $show . '">' . $show . '</option>';
      $cnt--;
      $val++;
    }

    return $rtn;
  }// end function genNumericOptions


  function genDaySelect(){
    $a = array("Sun","Mon","Tue","Wed","Thu","Fri","Sat" );
    $rtn = '';

    foreach( $a as $val ){
      $rtn .= "\n" . '<option value="' . $val . '">' . $val . '</option>';
    }// end foreach

    return $rtn;
  }// end function<?php genDaySelect


  function genMonthSelect(){
    $a = array( "Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec" );

    $rtn = '';

    foreach( $a as $val ){
      $rtn .= "\n" . '<option value="' . $val . '">' . $val . '</option>';
    }// end foreach

    return $rtn;
    }// end function genMonthSelect


  function genYearSelect(){
    $rtn = '';

    $yC = date( 'Y', time() );
    $yP = $yC -1;
    $yN = $yC +1;

    $rtn .= "\n" . ' <option value="' . $yP . '">' . $yP . '</option>';
    $rtn .= "\n" . ' <option value="' . $yC . '">' . $yC . '</option>';
    $rtn .= "\n" . ' <option value="' . $yN . '">' . $yN . '</option>';

    return $rtn;
  }// end function genYearSelect


  function genMimeTypes(){
    $rtn = '';
    $a = Array(
           ""                 => "-- Choose --",
           "audio/mpeg"       => ".mp3  - audio/mpeg",
           "audio/x-m4a"      => ".m4a  - audio/x-m4a",
           "video/mp4"        => ".mp4  - video/mp4",
           "video/x-m4v"      => ".m4v  - video/x-m4v",
           "video/quicktime"  => ".mov  - video/quicktime",
           "application/pd"   => ".pdf  - application/pdf",
           "document/x-epub"  => ".epub - document/x-epub" );

    foreach( $a as $val => $text ){
      $rtn .= "\n" . '<option value="' . $val . '">' .
              $text . '</option>';
    }// end foreach

    return $rtn;
  }// end function genMimeTypes


  function genYesNo(){
    $rtn = '';
    $a = Array(
           ""    => "-- Choose --",
           "yes" => "yes - allow" ,
           "no"  => "no - do not allow" );

    foreach( $a as $val => $text ){
      $rtn .= "\n" . '<option value="' . $val . '">' .
              $text . '</option>';
    }// end foreach

    return $rtn;
  }// end function genYesNo


  function genImagetypes(){
    $rtn = '';
    $a = Array(
           ""            => "-- Choose --",
           "video/jpeg"  => "jpeg - videojpeg" );

    foreach( $a as $val => $text ){
      $rtn .= "\n" . '<option value="' . $val . '">' .
              $text . '</option>';
    }// end foreach

    return $rtn;
  }// end function genImageTypes



  function genTimezoneSelect(){
    $rtn = '';
    $a = array(
      "AKDT" => "Alaska Daylight Time UTC-08",
      "AKDT" => "Alaska Daylight Time UTC-08",
      "AKST" => "Alaska Standard Time UTC-09",
      "CDT"  => "Central Daylight Time (North America) UTC-05",
      "CST"  => "Central Standard Time (North America) UTC-06",
      "EDT"  => "Eastern Daylight Time (North America) UTC-04",
      "EST"  => "Eastern Standard Time (North America) UTC-05",
      "GMT"  => "Greenwich Mean Time UTC",
      "HST"  => "Hawaii Standard Time UTC-10",
      "HADT" => "Hawaii-Aleutian Daylight Time UTC-09",
      "HAST" => "Hawaii-Aleutian Standard Time UTC-10",
      "MDT"  => "Mountain Daylight Time (North America) UTC-06",
      "MST"  => "Mountain Standard Time (North America) UTC-07",
      "PDT"  => "Pacific Daylight Time (North America) UTC-07",
      "PST"  => "Pacific Standard Time (North America) UTC-08" );

    foreach( $a as $val => $title){
      $rtn .= "\n" . '<option value="' . $val . '"' .
              ' title="' . $title . '">' .
              $val . '</option>';
    }// end foreach

    return $rtn;
  }// end function genTimezoneSelect

/*
 * -----------------
 *  end override.php
 * -----------------
*/
?>
