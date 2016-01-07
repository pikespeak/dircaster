<?php
  /* override_ajax_util.php
   * Called via AJAX from override.php
   * Receives and returns JSON format
  */

  $todo = $_GET['todo'];
  if ( isSet( $_GET['vpd'] ) ){
    $vpd = $_GET['vpd'];
  }else{
    $vpd  = '';
  }
  $a    = array();
  $aJ   = array();

  switch( $todo ) {
   case 'pd_now':
     echo json_encode( PubDateNowStr() );
     break;
   case 'pd_user':
     echo json_encode( PubDateUserStr( $_GET['value'] ) );
     break;
   case 'pd_verify':
     echo json_encode( PubDateVerify( $vpd, $_GET['typ'] ) );
     break;
   case 'duration_verify':
     echo json_encode( DurationVerify( $vpd ) );
     break;
   case 'length_verify':
     echo json_encode( LengthVerify( $vpd ) );
     break;
   case 'url_verify':
     echo json_encode( EnclosureUrlVerify( $vpd ) );
     break;
  }// end switch


  function PubDateNowStr( ){
    $aJ['pd'] = date( 'D, d M Y H:i:s T', time() );
    $aJ['D']  = date( 'D', time() );
    $aJ['d']  = date( 'd', time() );
    $aJ['M']  = date( 'M', time() );
    $aJ['Y']  = date( 'Y', time() );
    $aJ['H']  = date( 'H', time() );
    $aJ['i']  = date( 'i', time() );
    $aJ['s']  = date( 's', time() );
    $aJ['T']  = date( 'T', time() );
    $aJ['12'] = date( 'l, F jS Y g:i:s A T', time() );

    return $aJ;
  }


  function PubDateUserStr( $val ){
    $aJ['valid'] = 'yes';

    $str = str_replace( ',', '', $val );
    $aD = explode( ' ', $str );
    if ( count( $aD ) != 6 ){
      $aJ['valid'] = 'no';
      return $aJ;
    }
    $aT = explode( ':', $aD[4] );
    if ( count( $aT ) != 3 ){
      $aJ['valid'] = 'no';
      return $aJ;
    }

    $aJ['D']  = $aD[0];
    $aJ['d']  = $aD[1];
    $aJ['M']  = $aD[2];
    $aJ['Y']  = $aD[3];
    $aJ['H']  = $aT[0];
    $aJ['i']  = $aT[1];
    $aJ['s']  = $aT[2];
    $aJ['T']  = $aD[5];

    return $aJ;
  }// end function PubDateUserStr


  function PubDateVerify( $userStr, $type ){

    $userStr = rTrim( $userStr, ' ' );

    $space     = 32;
    $semicolon = 58;
    $comma     = 44;

    $aError = array();
    $aM     = array("Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec");
    $aD     = array("Mon","Tue","Wed","Thu","Fri","Sat","Sun");
    $thisTz = date( 'T', time() );

    $aError['valid'] = 'yes';
    $aError['msg']   = '';

    $aError['Day']    = '';
    $aError['Day #']  = '';
    $aError['month']  = '';
    $aError['year']   = '';
    $aError['tz']     = '';
    $aError['hh']     = '';
    $aError['mm']     = '';
    $aError['ss']     = '';

    $allErrorsMsg =
      'All errors may be cause by a ' .
      'single error in any part of the pubdate. For example: The date ' .
      'is on Tue, not Mon, or a day 32 or misspelled day or month, ';
    $allErrorsMsg = '<span title="' .  $allErrorsMsg  .  '">Note: (more></span>';

    //pubdate is not required
    if ( $userStr == '' ){
      $aError['valid'] = 'yes';
      $aError['msg']  .= '';
      $aError['hd']    = '';
      return $aError;
    }

    //get a working array of the attributes
    $userS = str_replace( ',', '', $userStr );
    $aUser = explode( ' ', $userS );

    //get the date and time that these functions think it is
    $compT = strToTime( $userStr );
    $compS = date( 'D d M Y H:i:s T', $compT );

    $engS = date( 'l, F jS Y g:i:s A T', $compT );
    $aError['hd'] = $engS;

    if ( $userS == $compS ){
      $aError['valid'] = 'yes';
      $aError['msg']   .= 'Valid Pubdate: ' . $userS . '.' .
                          '<br />or ' . $engS .  '.';
      $aError['hd'] = $engS;
      return $aError;
    }


    $tError = 0;
    $aChar = count_chars( $userStr, 1 );

    if ( isSet( $aChar[$space] ) ){
      if ( $aChar[$space] < 5 ){
        $aError['msg']  .= 'There are too few spaces. ' .
          'or the pubdate does not have all six components ' .
          '(DDD, dd MMM YYYY hh:mm:ss TZN).<br />';
        $tError = 1;
      }
      if ( $aChar[$space] > 5 ){
        $aError['msg']  .= 'There are too many spaces.<br />';
        $tError = 1;
      }
    }
    if ( substr( $userStr, 3, 1 ) != ',' ){
      $aError['msg']  .= 'There should be a comma immediately after the 3 character, day abbreviation.<br />';
      $tError = 1;
    }
    if ( isSet( $aChar[$comma] ) ){
      if ( $aChar[$comma] > 1 ){
        $aError['msg']  .= 'There should only be a comma immediately after the 3 character, day abbreviation.<br />';
        $tError = 1;
      }
    }
    if ( isSet( $aChar[$semicolon] ) ){
      if ( $aChar[$semicolon] != 2 ){
        $aError['msg']  .= "The time components should be separated by a semicolon - :.<br />";
        $tError = 1;
      }
    }
    if ( $tError ){
      $aError['valid'] = 'no';
      $aError['msg'] .= '<br />' . $allErrorsMsg;
      $aError['msg'] = cleanMsg( $aError['msg'] );
      return $aError;
    }

    //don't continue if all 6 attributes are not there
    if ( count( $aUser ) != 6 ){
      $aError['valid']  = 'no';
      if ( $type == 'v' ){
        $aError['msg'] .=
        'Invalid Pubdate: This validation only works when all six components of ' .
        'the pubdate are entered (DDD, dd MMM YYYY hh:mm:ss TZN). ' .
        'RSS Feed Validation will fail.';
      }else{
        $aError['msg'] .=
        'Invalid Pubdate: All six components of ' .
        'the pubdate must be present (DDD, dd MMM YYYY hh:mm:ss TZN). ' .
        'RSS Feed Validation will fail.';
      }
      return $aError;
    }

    //gross errors
    $tError = 0;
    if ( !in_array( $aUser[0], $aD ) ){
      $aError['Day'] .= "Invalid day abbreviation '" . $aUser[0] . "'.<br />";
      $tError = 1;
    }else{
      if ( $aUser[0] != ucWords( $aUser[0] ) ){
        $tError = 1;
        $aError['Day'] .= "The day abbreviation '" . $aUser[0] . "' first character must be uppercase. (ex: Mon not MONor mon).<br />";
      }
    }
    if ( $aUser[1] < 1 || $aUser[1] > 31 ){
      $tError = 1;
      $aError['Day #'] .= "The numeric day '" . $aUser[1] . "' is not in the range 01 - 31.<br />";
    }
    if ( strlen( $aUser[1] ) != 2 ){
      $tError = 1;
      $aError['Day #'] .= "The numeric day '" . $aUser[1] . "' must be 2 digits (ex: 01 not 1).<br />";
    }
    if ( !in_array( $aUser[2], $aM ) ){
      $tError = 1;
      $aError['month'] .= "Invalid month abbreviation '" . $aUser[2] . "'.<br />";
    }
    if ( $aUser[2] != ucWords( $aUser[2] ) ){
      $tError = 1;
      $aError['month'] .= "The month abbreviation '" . $aUser[2] . "' first character must be uppercase. (ex: Jan not JAN or jan).<br />";
    }
    if ( strlen( $aUser[3] ) != 4 ){
      $tError = 1;
      $aError['year'] .= "The numeric year '" . $aUser[3] . "' must be 4 digits (ex: 2012 not 12).<br />";
    }
    // time ranges
    $aT = explode( ':', $aUser[4] );
    if ( $aT[0] > 23 ){
      $tError = 1;
      $aError['hh'] .= 'Hour(s) greater than 23' . '.<br />';
    }
    if ( strlen( $aT[0] ) != 2 ){
       $tError = 1;
       $aError['hh'] .= 'Hour(s) ' .  $aT[0] . ' must be 2 digits (ex: 01 not 1)<br />';
    }
    if ( $aT[1] > 59 ){
      $tError = 1;
      $aError['mm'] .= 'Minute(s) greater than 59' . '.<br />';
    }
    if ( strlen( $aT[1] ) != 2 ){
      $tError = 1;
      $aError['mm'] .= 'Minutes(s) ' .  $aT[1] . ' must be 2 digits (ex: 01 not 1)<br />';
    }
    if ( $aT[2] > 59 ){
      $tError = 1;
      $aError['ss'] .= 'Second(s) greater than 59' . '.<br />';
    }
    if ( strlen( $aT[2] ) != 2 ){
      $tError = 1;
      $aError['ss'] .= 'Seconds(s) ' .  $aT[2] . ' must be 2 digits (ex: 01 not 1)<br />';
    }
    if ( strlen( $aUser[5] ) < 2  || strlen( $aUser[5] ) > 5 ){
      $tError = 1;
      $aError['tz'] .= 'The time zone must be from 2 to 5 characters.<br />';
    }
    if ( $aUser[5] != strToUpper( $aUser[5] ) ){
      $tError = 1;
      $aError['tz'] .= 'The time zone must be upper case characters.<br />';
    }

    //check for gross erros
    if ( $tError ){
      $aError['valid'] = 'no';
      $aError['msg'] .= $allErrorsMsg;
      $aError['msg'] = cleanMsg( $aError['msg'] );
      return $aError;
    }

    //check for different timezoe, files may have been moved to serve
    //in a different timezone
    if ( $aUser[5] != $thisTz ){
      $aError['valid'] = 'wrn';
      $tzWrn =
       'The timezone entered ' . $aUser[5] . ' ' .
       'is not equal to this servers timezone ' . $thisTz . '.<br />' .
       'This may be an error or if editing a file, the file may have ' .
       'been moved to this server having a differen timezone.<br />' .
       'Care should be taken before changing the timezone, to avoid ' .
       'republishing.';
      $aError['msg'] .= 'Warning only: This servers TZ is ' .
                        $thisTz . ', not ' . $aUser[5] .
                        '. <span title="' . $tzWrn . '">(more)</span><br />';
    }// ed if for different timezone


    //at this point we have all six attributs and the tiemzone agrees

    $aError['od'] = $userS;
    $aError['nd'] = $compS;
    $aError['hd'] = $engS;

    // check the parts

    //day
    if ( $thisTz == $aUser[5] ){
      $aError['Day'] = '';
      $tmpS = $aUser[1] . ' ' . $aUser[2] . ' ' . $aUser[3] . ' ' . $aUser[4] . ' ' . $aUser[5];
      $tmpT = strToTime( $tmpS );
      $tmpD = date( 'D', $tmpT );
      if ( $aUser[0] != $tmpD ){
        $aError['valid'] = 'no';
        $aError['Day'] = 'The day ' . $aUser[1] . ' ' . $aUser[2] . ' ' . $aUser[3] .
                         ' is on ' . $tmpD  . ' not ' . $aUser[0] . '.<br />';
      }
    }

    //year
    $aError['year'] = '';
    $tmpS = $aUser[1] . ' '. $aUser[2] . ' ' . $aUser[5];
    $tmpT = strToTime( $tmpS );
    $tmpD = date( 'Y', $tmpT );
    if ( $aUser[3] != $tmpD ){
      $aError['valid'] = 'no';
      $aError['year'] = 'The year ' . $aUser[3] .
                        ' is in ' . $tmpD .  ' for ' . $aUser[0] . ' ' .
                        $aUser[1] . ' in ' . $aUser[2] . '.<br />';
    }else if ( $aUser[3] <= date( 'Y', time() )-1 ){
      $aError['valid'] = 'no';
      $aError['year'] = 'Year ' . $aUser[3] . ' is previous to the current year.<br />';
    }

    if ( $aError['valid'] == 'no' ){
      $aError['valid'] = 'no';
      $aError['msg'] .= '<br />' . $allErrorsMsg;
      $aError['msg'] = cleanMsg( $aError['msg'] );
      return $aError;
    }

    return $aError;

  }// end function verifyPubDate


  function cleanMsg( $str ){
    $str = str_replace( '<br /><br />', '<br />', $str );
    if ( substr( $str, 0, 6 ) == '<br />' ){
      $str = substr( $str,  6 );
    }
    return $str;
  }// end function cleanMsg


  function DurationVerify( $userStr ){
    $aError             = array();
    $aError['valid']    = 'yes';
    $aError['newstr']   = '';
    $aError['msg']      = '';
    $aError['inwords']  = '';

    //prelim checks
    $userStr = str_replace( ' ', '', $userStr );

    if ( $userStr == '' || $userStr == '00:00:00' ){
      $aError['valid'] = 'yes';
      $aError['msg'] = 'Valid: Duration is not required';
      return $aError;
    }
    if ( $userStr == ':' || $userStr == '::' ){
      $aError['valid'] = 'no';
      $aError['msg']   = "Invalid: '" .  $userStr . "'";
      return $aError;
    }


    $aUser = explode( ':', $userStr );

    //characters
    if ( isSet( $aUser[0] ) ){
      if ( !is_numeric( $aUser[0] ) ){
       $aError['valid'] = 'no';
       $aError['msg']   = "Invalid characters: '" .  $userStr . "'";
       return $aError;
      }
    }
    if ( isSet( $aUser[1] ) ){
      if ( !is_numeric( $aUser[1] ) ){
       $aError['valid'] = 'no';
       $aError['msg']   = "Invalid characters: '" .  $userStr . "'";
       return $aError;
      }
    }
    if ( isSet( $aUser[2] ) ){
      if ( !is_numeric( $aUser[2] ) ){
       $aError['valid'] = 'no';
       $aError['msg']   = "Invalid characters: '" .  $userStr . "'";
       return $aError;
      }
    }

    //check ranges
    $txt = DurationRanges( $aUser );
    if ( $txt != '' ){
     $aError['valid'] = 'no';
     $aError['msg']   = $txt;
     return $aError;
    }

    if ( count( $aUser ) > 3 ){
      $newStr           = $aUser[0] . ':' . $aUser[1] . ':' . $aUser[2];
      $aError['valid']  = 'no';
      $aError['newstr'] = $newStr;

      $inWords = genDurationInWords( explode( ':', $newStr ) );
      $aError['inwords'] = $inWords;
      $aError['msg']     = "There should only be a maximum of 2 ':' " .
                           'The duration will be considered as: ' . $newStr .
                           ' - ' . $inWords;
      $aError['msg']    = 'Valid: Duration is not required';
    }else{
      $inWords = genDurationInWords( $aUser );
      $aError['msg']    = 'Valid: ' . ' - ' . $inWords;
      $aError['inwords'] = $inWords;
    }

    return $aError;

  }// end function DurationVerify


  function genDurationInWords( $a ){

    $engStr = '';
    $mStr = '';
    $sStr = '';

    $cnt = count( $a );

    //hours
    if ( $cnt == 3 ){
      if ( $a[0] != '' ){
        $n = lTrim( $a[0], '0' );
        If ( $n == 1 ){
          $engStr = $n . ' hour';
        }else if ( $n != 0){
          $engStr = $n . ' hours';
        }
      }
    }

    //minutes
    if ( $cnt == 3 ){
      $i = 1;
    }else if ( $cnt == 2 ){
      $i = 0;
    }
    if ( $a[$i] != '' ){
     $n = lTrim( $a[$i], '0' );
     If ( $n == 1 ){
       $mStr = $n . ' minute';
     }else if ( $n != 0 ) {
       $mStr = $n . ' minutes';
     }
    }

    //seconds
    if ( $cnt == 3 ){
      $i = 2;
    }else if ( $cnt == 2 ){
      $i = 1;
    }else{
      $i = 0;
    }
    if ( $a[$i] != '' ){
      $n = lTrim( $a[$i], '0' );
      If ( $n == 1 ){
        $sStr = $n . ' second';
      }else if ( $n != 0 ){
        $sStr = $n . ' seconds';
      }
    }

    return $engStr . ' ' . $mStr . ' ' . $sStr;

  }// end function genDurationInWords


  function DurationRanges( $a ){

    $txt = '';
    $cnt = count( $a );

    //hours
    if ( $cnt == 3 ){
    $i = 0;
      if ( $a[$i] != '' ){
        if ( $a[$i] > 23 ){
          $txt .= 'Hour ' . $a[$i] . ' out of range.';
        }
      }
    }

    //minutes
    if ( $cnt == 3 ){
      $i = 1;
    }else if ( $cnt == 2 ){
      $i = 0;
    }
    if ( $a[$i] != '' ){
     If ( $a[$i] > 59 ){
       $txt .= ' Minutes ' . $a[$i]. ' our of range';
     }
    }

    //seconds
    if ( $cnt == 3 ){
      $i = 2;
    }else if ( $cnt == 2 ){
      $i = 1;
    }else{
      $i = 0;
    }
    if ( $a[$i] != '' ){
      if ( $a[$i] != '' ){
       If ( $a[$i] > 59 ){
         $txt .= ' Seconds ' . $a[$i]. ' our of range';
       }
      }
    }

    return $txt;

  }// end function DurationRanges


  function LengthVerify( $userStr ){
    $aError             = array();
    $aError['valid']    = 'yes';
    $aError['newstr']   = '';
    $aError['msg']      = '';
    $aError['newvalue'] = '';

    $userStr = str_replace( ',', '', $userStr );
    $userStr = intVal( $userStr );

    if ( $userStr == '' ){
      $aError['valid'] = 'no';
      $aError['msg']   = 'Invalid: Length is required.';
      return $aError;
    }
    if ( !is_numeric( $userStr ) ){
      $aError['valid'] = 'no';
      $aError['msg']   = 'Invalid: Numbers only. No spaces, commas or other characters.';
      return $aError;
    }

    $aError['newvalue'] = $userStr;

    $kb = 1024;
    $mb = 1048576;
    $gb = $mb * 1024;
    $tb = $gb *  1024;

    $kbS = round( ($userStr / $kb), 1);
    $mbS = round( ($userStr / $mb), 1);
    $gbS = round( ($userStr / $gb), 1);
    $tbS = round( ($userStr / $tb), 1);

    if ( $tbS > 1.0 ){
      $answer = round($tbS,0) . ' tb';
    }else if ( $gbS > 1.0 ){
      $answer = round($gbS,0) . ' gb';
    }else if ( $mbS > 1.0){
      $answer = round($mbS,0) . ' mb';
    }else if ( $kbS > 1.0 ){
      $answer = round($kbS,0) . ' kb';
    }else{
      $answer = $userStr . ' bytes';
    }

    $aError['newstr'] = $answer;
    $aError['msg']    = 'Valid: Equates to: ' . number_format( $userStr, 0, '.', ',' ) .
                        ' bytes (' . $answer . ')';

    return $aError;

  }// end function LengthVerify


  function EnclosureUrlVerify( $userStr ){
    $aError = array();
    $aError['msg'] = '';

    if ( substr( $userStr, 0, 7 ) != 'http://' ){
      $aError['msg'] = "The enclosure URL msut start with 'http://'.";
    }else if ( $userStr == '' ){
      $aError['msg'] = '';
    }else if ( $userStr == 'http://' ){
      $aError['msg'] = '';
    }else{
      $aError['msg'] =
         '<a href="' . $userStr . '" title="Click to start the downlaod, which you can ccancel.">Test link.</a>';
    }

    return $aError;

  }// end function EnclosureURL-Verify



  /*
   * end override_ajax_util.php
  */
?>
