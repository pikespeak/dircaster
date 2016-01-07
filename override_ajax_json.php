<?php
  /* override_ajax_json.php
   * Develope array aJ for JSONencoding
   * Called from override.php
  */

  $f   = $_GET['file'];
  $flf = $_GET['from'];
  $a   = array();
  $aJ  = array();

  $aPath = pathinfo( $f );
  $ext = $aPath['extension'];
  $vSufix = $ext;
  if ( $vSufix != 'otf' ){
    $vSufix = 'txt';
  }

  // test file existence
  if ( $_GET['test'] == 'yes' ){
    if ( file_exists( $f ) ){
      $aJ['test'] = 'exists';
    }else{
      if ( $f != $flf ){
        $aJ['test'] = 'borrow';
        $aJ['use']  = $flf;
      }else{
         $aJ['test'] = 'new';
         $aJ['use']  = $fl;
      }
    }
    // see if we want to create a new file from a template fle
    if ( $ext == 'otf' ){
      $aJ['test'] = 'template';
    }
  }// end test file existence


  // read and return file contents
  if ( $_GET['test'] == 'no' ){
    if ( $_GET['file'] != $_GET['from']  ){
      $f = $_GET['from'];  // contents of exstig file but new name given
    }
    if ( file_exists( $f ) ){
      $a = file( $f );
    }

    // create arrey for json encoding
    foreach ( $a as $line_num => $line ){
      if ( substr( $line, 0, 1 ) != '#' && $line != '' ){
        if ( ( $foundAt = strPos( $line, ']' ) ) !== False ){
          $key = subStr( $line, 0, $foundAt +1 );
          switch ( $key ){
            case '[title]':
              $key = '#title';
              break;
            case '[link]':
              $key = '#link';
              break;
            case '[author]':
              $key = '#author';
              break;
            case  '[commentText]':
              $key = '#commentText';
              break;
            case  '[commentCDATA]':
              $key = '#commentCDATA';
              break;
            case '[pubDate]':
              $key = '#pubDate';
              break;
            case '[enclosureURL]':
              $key = '#enclosureURL';
              break;
            case '[enclosureLength]':
              $key = '#enclosureLength';
              break;
            case '[enclosureType]':
              $key = '#enclosureType';
              break;
            case '[subtitle]':
              $key = '#subtitle';
              break;
            case '[keywords]':
              $key = '#keywords';
              break;
            case '[allowItemImage]':
              $key = '#allowItemImage';
              break;
            case '[imageURL]':
              $key = '#imageURL';
              break;
            case '[imageTitle]':
              $key = '#imageTitle';
              break;
            case '[imageFileType]':
              $key = '#imageFileType';
              break;
            case '[duration]':
              $key = '#duration';
              break;
            case '[guid]':
              $key = '#guid';
              break;
            case '[artist]':
              $key = '#artist';
              break;
            case '[album]':
              $key = '#album';
              break;
            case '[summary]':
              $key = '#summary';
              break;
          }// end switch

          $val = subStr( $line, $foundAt +1 );
          $aJ[$key] = rTrim( $val, ' ' );;
        }else{
          // if more than one line in the  tag
          if ( isSet( $key ) ){
            $aJ[$key] .= rTrim( $line, ' ' );
          }
        }// end if override tag line
      }// end if good line
    }// end foreach
  }// end return file contents


  // write file
  if ( $_GET['test'] == 'write' ){
    $msg = '';
    if ( file_exists( $f ) ){
      $msg = 'Existing file';
    }else{
      $msg = 'New file';
    }

    $fp = 0;
    if( !$fp = fopen( $f, "w+b" ) ){
      $msg = 'Failure, could open/create file:';
      $aJ['msg'] = $msg;
      echo json_encode( $aJ );
      exit();
    }else{
      $cnt = 1;
      foreach ( $_GET as $key => $val ){
       if ( $key != 'file' && $key != 'test' ){
         if ( $val == 'tag' ){
           $str = "\n\n#" . str_replace( '_', ' ', $key );
         }else{
           $str = "\n[" . $key . ']'. $val;
         }
         //add headig line
         if ( $cnt == 1 ){
           if ( $ext == 'otf' ){
             $str = '#Dircaster templete override file ' . basename( $f ) . $str;
           }else{
             $str = '#Dircaster override file ' . basename( $f ) . $str;
           }
         }
         // effects fwrite as well as db inserts
         if( get_magic_quotes_gpc() ){
           $str = stripslashes( $str);
         }
         $cnt++;
         if( !fwrite( $fp, rTrim( $str, "\n" ) ) ){
           $msg = 'Failure, could write to file:';
           $aJ['msg'] = $msg;
           echo json_encode( $aJ );
         }else{
           $msg = 'Success writing:';
           $pi = pathInfo( $f );
           $aJ['option'] = '<option value="' . $pi['filename'] . '">' .
                           $pi['filename'] . '</option>';

           $aTmp = pathinfo($_SERVER['HTTP_REFERER']);
           $aHref = $aTmp['dirname'];
           $nf = ltrim( $f, '.' );
           $nf = ltrim( $nf, '/' );
           $fl = '<a href="' . $aHref . '/' . $nf .
                 '" class="view-' . $vSufix .
                 '" target="_blank" title="View new/revised file in browser.">' .
                 'View file</a>';
           $aJ['link'] = $fl;
         }
       }// writeable lines
      }// end foreach
    }// end fopen
    fclose( $fp );

    $aJ['msg'] = $msg;

  }// end write file

  // return the json
  echo json_encode( $aJ );

 /*
  * end override_ajax_json.php
 */
?>
