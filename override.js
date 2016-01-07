/* start override.js */

   $(document).ready(function(){

    $('#newOverrideFileName').val( '' );
    $('#newOverrideFileName').focus();

    // start global vars

    var flBtnStartMsg          = $('#file-choice-btn').text();
    var flBtnStartBgColor      = $('#file-choice-btn').css('background-color');
    var flBtnStartColor        = $('#file-choice-btn').css('color');

    var flBtnCreateMsg         = 'Proceed to CREATE new file: ';
    var flBtnCreateBgColor     = 'yellow';
    var flBtnCreateColor       = 'black';

    var flBtnEditMsg           = 'Proceed to EDIT existing file: ';
    var flBtnEditBgColor       = 'green';
    var flBtnEditColor         = 'white';

    var flBtnBorrowMsg         = 'Proceed to CREATE  new file : ';
    var flBtnBorrowBgColor     = 'blue';
    var flBtnBorrowColor       = 'white';

    var flBtnCreateTemplateMsg     = 'Proceed to CREATE new file from TEMPLATE: ';
    var flBtnCreateTemplateBgColor = 'red';
    var flBtnCreateTemplateColor   = 'white';

    var typeFileStart  = 's'; // default value, existance test not made yet)
    var typeFileCreate = 'c'; // creating new file
    var typeFileExists = 'e'; // override or template file esists
    var typeFileBorrow = 'b'; // override or template file esists
    var typeFileFlag   = typeFileStart;

    var fileFromInput    = 'i';   // type a name
    var fileFromOverride       = 'o';   // choose from override dropdown
    var fileFromOverrideBorrow = 'b'; // choose from override dropdown, new naem
    var fileFromTemplate       = 't';   // choose from template dropdown
    var fileChosenFrom         = fileFromInput;

    //  end global vars

    //selects
    sortDropDownListByText( $('#file-select') );
    $('#file-select').val( '-' );
    sortDropDownListByText( $('#template-select') );
    $('#template-select').val( '-' );

    // start textare counts
    // char count for commentText textarea
    $('#commentText').bind('input propertychange', function() {
      taLen = $('#commentText').val().length;
      $('#commentText-length').text( taLen );
    });
    $('#summary').bind('input propertychange', function() {
      taLen = $('#summary').val().length;
      $('#summary-length').text( taLen );
    });
    $('#subtitle').bind('input propertychange', function() {
      taLen = $('#subtitle').val().length;
      $('#subtitle-length').text( taLen );
    });
    $('#keywords').bind('input propertychange', function() {
      taLen = $('#keywords').val().length;
      $('#keywords-length').text( taLen );
    });
    // char count for commentCDATA textarea
    $('#commentCDATA').bind('input propertychange', function() {
      taLen = $('#commentCDATA').val().length;
      $('#commentCDATA-length').text( taLen );
    });
    // end textare counts


    // ----------
    // Event logic
    // ----------

    $('#file-choice-btn').click(function(){
      // must a file name
      if ( $('#newOverrideFileName').val() == '' ){
        $('#show-fields').hide();
        $("#file-choice-btn").text( flBtnStartMsg );
        $('#file-choice-btn').css( { 'background-color' : flBtnStartBgColor, 'color' : flBtnStartColor } );
        typeFileFlag = typeFileStart;
        return;
      }
      // file name based on select boxs, are we using a template file
      var fl = '';
      var flf = '';
      if ( fileChosenFrom == fileFromInput ){
        fl = $('#newOverrideFolder').text() +
             $('#newOverrideFileName').val() +
             $('#newOverrideFileType').text();
        flf = fl;
      }else if ( fileChosenFrom == fileFromOverride ){
        // using same or new name
        if ( $('#file-select').val() != $('#newOverrideFileName').val() ){
          fl = $('#newOverrideFolder').text() +
               $('#newOverrideFileName').val()  +
               $('#newOverrideFileType').text();
          flf = $('#newOverrideFolder').text() +
                $("#file-select option:selected").val()  +
                $('#newOverrideFileType').text();
        }else{
          fl = $('#newOverrideFolder').text() +
               $("#file-select option:selected").val()  +
               $('#newOverrideFileType').text();
          flf = fl;
        }
      }else{
        fl = $('#newOverrideFolder').text() +
             'templates/' +
             $("#template-select option:selected").val()  +
             '.otf';
        flf = fl;
      }
      // test for creating a new file or editing existing override or
      // template fle
      if ( typeFileFlag  == typeFileStart ){
        tst = 'error';
        //AJAX to test for file existence
        $.getJSON(
         'override_ajax_json.php',
         { file : fl,
           from : flf,
           test : 'yes'
         },
         function( data, textStatus ) {
           tst = data['test'];
           if ( tst == 'new'  ){
             typeFileFlag = typeFileCreate; //create
             $("#file-choice-btn").text( flBtnCreateMsg + fl );
             $("#file-choice-btn").css( {'background-color' : flBtnCreateBgColor , 'color' : flBtnCreateColor } );
           }else if ( tst == 'exists'  ){
             $('#show-fields').hide();
             typeFileFlag = typeFileExists; //edit
             $("#file-choice-btn").text( flBtnEditMsg + fl );
             $("#file-choice-btn").css( {'background-color' : flBtnEditBgColor, 'color' : flBtnEditColor} );
           }else if ( tst == 'borrow'  ){
             $('#show-fields').hide();
             typeFileFlag = typeFileBorrow; // new file, but from override
             $("#file-choice-btn").text( flBtnBorrowMsg + fl + ' with data FROM: ' + flf);
             $("#file-choice-btn").css( {'background-color' : flBtnBorrowBgColor, 'color' : flBtnBorrowColor} );
           }else if ( tst == 'template' ){
             $('#show-fields').hide();
             typeFileFlag = typeFileExists;
             if ( fileChosenFrom == fileFromTemplate ){
               $("#file-choice-btn").text( flBtnCreateTemplateMsg + fl );
               $("#file-choice-btn").css( { 'background-color' : flBtnCreateTemplateBgColor, 'color' : flBtnCreateTemplateColor } );
              }
           }
         }// end return call function
        ); // end AJAX to test for file existence
      }else{
        $("#show-fields").show();
        $("#file-info").hide();
        var rText = $('#newOverrideFolder').text() + $('#newOverrideFileName').val() + $('#newOverrideFileType').text();
                    $('#operation-file').text( rText );
        $("#this-operation").show();
        // AJAX to get file contents
        $.getJSON(
         'override_ajax_json.php',
         { file : fl,
           from : flf,
           test : 'no'
         },
         function( data, textStatus ) {
           var items = [];
           $.each(
             data,
             function( key, val ) {
               if ( !val ){
                 val = '';
               }
               switch( key ){
                case '#commentText':
                  $(key).val(val);
                  setValAndLength( key, '#commentText-length' );
                  break;
                case '#commentCDATA':
                  $(key).val(val);
                  setValAndLength( key, '#commentCDATA-length' );
                  break;
                case '#summary':
                  $(key).val(val);
                  setValAndLength( key, '#summary-length' );
                  break;
                case '#subtitle':
                  $(key).val(val);
                  setValAndLength( key, '#subtitle-length' );
                  break;
                case '#keywords':
                  $(key).val(val);
                  setValAndLength( key, '#keywords-length' );
                  break;
                case '#duration':
                  $(key).val(val);
                  verifyDuration( 'duration_verify', $('#duration').val(), 'i'  );
                  break;
                case '#enclosureLength':
                  $(key).val(val);
                  verifyLength( 'length_verify', $('#enclosureLength').val(), 'i'  );
                  break;
                case '#enclosureURL':
                  $(key).val(val);
                  verifyLength( 'url_verify', $('#enclosureLength').val(), 'i'  );
                  break;
                case '#enclosureType':
                  $(key).val(val);
                  $('#mime-select').val( val );
                  break;
                case '#pubDate':
                  $(key).val(val);
                  verifyPubDate( 'pd_verify', $('#pubDate').val(), 'i'  );
                  break;
                case '#allowItemImage':
                  $(key).val(val);
                  $('#allow-image-select').val( val );
                case '#imageFileType':
                  $(key).val(val);
                  $('#image-type-select').val( val );
                  break;
                default:
                  $(key).val(val);
               }; //end switch
             }// end each function
           )// end each
           var n   = ': ' + $('#newOverrideFolder').text() + $('#newOverrideFileName').val() + $('#newOverrideFileType').text();
           $('#override-save-file-name').text( n );
         }// end return call function
       ); // end  AJAX to get file contents
      }// end button test
    });  //end file-choice-btn click


    // the shwo-preview-btn, button jusut displays a preview, it does not submit a form
    $('#show-preview-btn').click(function(){
      $('#ralbum').text( $('#album').val() );
      $('#rallowItemImage').text( $('#allowItemImage').val() );
      $('#rartist').text( $('#artist').val() );
      $('#rauthor').text( $('#author').val() );
      $('#rcommentCDATA').text( $('#commentCDATA').val() );
      $('#rcommentText').text( $('#commentText').val() );
      $('#rduration').text( $('#duration').val() );
      $('#renclosureType').text( $('#enclosureType').val() );
      $('#renclosureLength').text( $('#enclosureLength').val() );
      $('#renclosureURL').text( $('#enclosureURL').val() );
      $('#rguid').text( $('#guid').val() );
      $('#rimageFileType').text( $('#imageFileType').val() );
      $('#rimageTitle').text( $('#imageTitle').val() );
      $('#rimageURL').text( $('#imageURL').val() );
      $('#rkeywords').text( $('#keywords').val() );
      $('#rlink').text( $('#link').val() );
      $('#rpubDate').text( $('#pubDate').val() );
      $('#rsubtitle').text( $('#subtitle').val() );
      $('#rsummary').text( $('#summary').val() );
      $('#rtitle').text( $('#title').val() );
      $('#preview').show();
    }); // end $('#show-preview-btn').click


    // close preview window
    $('#cancel-preview-btn').click(function(){
      $('#preview').hide();
    }); // cancel-preview-btn click


    // ajax cll to write override file
    $('#override-save-file-btn').click(function(){
      $('#override-save-file-msg').hide();
      $('#override-save-file-name').hide();
      $('#override-save-file-link').hide();
      $('#template-save-file-div').hide();

      var fl = $('#newOverrideFolder').text() + $('#newOverrideFileName').val() + $('#newOverrideFileType').text();
      //AJAX to write override file
      $.getJSON(
       'override_ajax_json.php',
       { file              : fl,
         from              : fl,
         test              : 'write',
         Media_info        : 'tag',
         enclosureURL      : $('#enclosureURL').val(),
         enclosureLength   : $('#enclosureLength').val(),
         enclosureType     : $('#enclosureType').val(),
         duration          : $('#duration').val(),
         Podcast_info      : 'tag',
         title             : $('#title').val(),
         link              : $('#link').val(),
         pubDate           : $('#pubDate').val(),
         subtitle          : $('#subtitle').val(),
         author            : $('#author').val(),
         album             : $('#album').val(),
         artist            : $('#artist').val(),
         summary           : $('#summary').val(),
         keywords          : $('#keywords').val(),
         guid              : $('#guid').val(),
         commentText       : $('#commentText').val(),
         commentCDATA      : $('#commentCDATA').val(),
         allowItemImage    : $('#allowItemImage').val(),
         imageURL          : $('#imageURL').val(),
         imageTitle        : $('#imageTitle').val(),
         imageFileType     : $('#imageFileType').val()
       },
       function( data, textStatus ) {
         $('#override-save-file-msg').text( data['msg'] );
         $('#override-save-file-msg').show();
         $('#override-save-file-name').show();
         $('#override-save-file-link').html( data['link'] );
         $('#override-save-file-link').show();
         //add option to override dropdown, if newly created
         if ( typeFileFlag != typeFileExists ){
           // add newest file to options and sort
           $('#file-select').append( data['option'] );
           sortDropDownListByText( $('#file-select') );
         }
         // template area
         $('#template-save-file-div').show();
         $('#template-save-file-input').val( $('#newOverrideFileName').val() );
         $('#template-save-file-input').show();
         $('#template-save-file-msg').text( 'Not yet saved: ');
         $('#template-save-file-msg').show();
         $('#template-save-file-btn').show();
         var templateName = $('#template-save-file-input').val() + '.otf';
         var tf = $('#newOverrideFolder').text() + 'templates/' + templateName;
         $('#template-save-file-name').text( tf );
         $('#template-save-file-name').show();
         $('#template-save-file-link').hide();
       }// end return call function
      ); // end AJAX to write override file
    }); // override-save-file-btn click


    // close current, start new file
    $('#close-fields-btn').click(function(){
      cleanUp();
    }); // end close-fields-btn click


    // reset file area
    $('#reset-file-area-btn').click(function(){
      cleanUp();
    }); // end $('#reset-file-area-btn').click


    $('#file-select').change(function(){
      var v = $("#file-select option:selected").val();
      if ( v != '-' ){
        $('#newOverrideFileName').val( v );;
      }
      $('#newOverrideFileName').focus();
      fileChosenFrom = fileFromOverride;
    });


    $('#template-select').change(function(){
      if ( $("#template-select option:selected").val() == '-' ){
        return false;
      }
      $('#newOverrideFileName').focus();
      $('#newOverrideFileName').val('');
      fileChosenFrom = fileFromTemplate;
    }); // end template change selection


    $('#mime-select').change(function(){
      var v = $("#mime-select option:selected").val();
      $('#enclosureType').val( v );;
    }); // end $('#mime-select').change

    $('#allow-image-select').change(function(){
      var v = $("#allow-image-select option:selected").val();
      $('#allowItemImage').val( v );;
    }); // end $('#allow-image-select').change

    $('#image-type-select').change(function(){
      var v = $("#image-type-select option:selected").val();
      $('#imageFileType').val( v );;
    }); // end $('#image-type-select').change

    $('#template-save-file-btn').click(function(){
      var templateName = '';
      if ( $('#template-save-file-input').val() == '' ){
        templateName = $('#newOverrideFileName').val() + 'otf';
      }else{
        templateName = $('#template-save-file-input').val() + '.otf';
      }
      fl = $('#newOverrideFolder').text() + 'templates/' + templateName;
      //AJAX to write template file
      $.getJSON(
       'override_ajax_json.php',
       { file              : fl,
         from              : fl,
         test              : 'write',
         Media_info        : 'tag',
         enclosureURL      : $('#enclosureURL').val(),
         enclosureLength   : $('#enclosureLength').val(),
         enclosureType     : $('#enclosureType').val(),
         duration          : $('#duration').val(),
         Podcast_info      : 'tag',
         title             : $('#title').val(),
         link              : $('#link').val(),
         pubDate           : $('#pubDate').val(),
         subtitle          : $('#subtitle').val(),
         author            : $('#author').val(),
         album             : $('#album').val(),
         artist            : $('#artist').val(),
         summary           : $('#summary').val(),
         keywords          : $('#keywords').val(),
         guid              : $('#guid').val(),
         commentText       : $('#commentText').val(),
         commentCDATA      : $('#commentCDATA').val(),
         allowItemImage    : $('#allowItemImage').val(),
         imageURL          : $('#imageURL').val(),
         imageTitle        : $('#imageTitle').val(),
         imageFileType     : $('#imageFileType').val()
       },
       function( data, textStatus ) {
         $('#template-save-file-msg').text( data['msg'] );
         $('#template-save-file-msg').show();
         $('#template-save-file-name').show();
         $('#template-save-file-input').hide();
         $('#template-save-file-link').html( data['link'] );
         $('#template-save-file-link').show();
         $('#template-select').append( data['option'] );
         sortDropDownListByText( $('#template-select') );
       }// end return call function
      ); // end AJAX to write template file
    }); // end $('#template-save-file-btn').click


    $('#pubdate-now-btn').click(function(){
      refreshPubDateFields( 'pd_now' );
    }); // end $('#pubdate-now-btn').click


    // pubdate disply via select dropdown, by class
    $('.pubdate-select').each(function(){
      $(this).click(function(){
        var dStr = makePubDate($("this option:selected").val() );
        $('#pubDate').val( dStr );
      });
    }); // end pubdate disply via select dropdown, by class


    $('#pubdate-verify-btn').click(function(){
      verifyPubDate( 'pd_verify', $('#pubDate').val(), 'v'  );
    }); // end $('#pubdate-verify-btn').click


    $('#pubdate-select-div-btn').click(function(){
      if ( $(this).text() == '[+]' ) {
        $(this).text( '[-]' );
        populateSelects( $('#pubDate').val() );
        $('#pubdate-select-div').show();
      }else{
        $(this).text( '[+]' );
        $('#pubdate-select-div').hide();
      }
    }); // $('#pubdate-select-div-btn').click


    $('#duration-verify-btn').click(function(){
      verifyDuration( 'duration_verify', $('#duration').val(), 'v'  );
    }); // end $('#duration-verify-btn').click

    $('#enclosure-length-verify-btn').click(function(){
      verifyLength( 'length_verify', $('#enclosureLength').val(), 'v'  );
    }); // end $('#enclosure-length-verify-btn').click

    $('#enclosureURL-verify-btn').click(function(){
      verifyEnclosureURL( 'url_verify', $('#enclosureURL').val(), 'v'  );
    }); // end $('#enclosureURL-verify-btn').click


    function cleanUp(){
      // reset data entry form fields
      $('#form-fields').find(':input').each(function() {
        switch(this.type) {
         case 'password':
         case 'select-multiple':
         case 'select-one':
         case 'text':
          $(this).val('');
         case 'textarea':
          $(this).val('');
         case 'checkbox':
         case 'radio':
          //this.checked = false;
         case 'button':
           if ( this.id == 'file-choice-btn' ){
             $(this).text( flBtnStartMsg );
             $(this).css( { 'background-color' : flBtnStartBgColor, 'color' : flBtnStartColor } );
           }
         default:
        }// end switch
      }); // end each
      typeFileFlag   = typeFileStart; //start
      fileChosenFrom = fileFromInput;
      //reset pubdate are
      $(this).text( '[+]' );
      $('#pubdate-select-div').hide();
      // reset visible ares
      $('#preview').hide();
      $('#show-fields').hide();
      $('#file-info').show();
      $('#this-operation').hide();
      // reset the override/template button/msg area
      $('#override-save-file-name').show();
      $('#override-save-file-msg').text( 'Not yet saved: ');
      $('#template-save-file-msg').text( 'Not yet saved: ');
      $('#override-save-file-link').hide();
      $('#template-save-file-div').hide();
      $('#template-save-file-btn').hide();
      $('#template-save-file-msg').hide();
      $('#template-save-file-name').hide();
      $('#template-save-file-link').hide();
      //reset length indicators
      $('#commentText-length').text( '0' );
      $('#commentCDATA-length').text( '0' );
      $('#summary-length').text( '0' );
      $('#subtitle-length').text( '0' );
      $('#keywords-length').text( '0' );
    }// end function cleanUp


    // make link the same as enclosureURL
    $('#link-same-as-btn').click(function(){
      $('#link').val( $('#enclosureURL').val() );
    }); // $('#link-same-as-btn').click


    // make guid the same as enclosureURL
    $('#guid-same-as-btn').click(function(){
      $('#guid').val( $('#enclosureURL').val() );
    }); // $('#guid-same-as-btn').click


   //==============================
   }); // end jquery document ready
   //==============================


   // functions outside doc ready
   function makePubDate( v ){
     var $str = '';
     str = $('#pubdate-select-day').val() + ', ' +
           $('#pubdate-select-nnn').val() + ' ' +
           $('#pubdate-select-month').val() + ' ' +
           $('#pubdate-select-year').val() + ' ' +
           $('#pubdate-select-hh').val() + ':' +
           $('#pubdate-select-mm').val() + ':' +
           $('#pubdate-select-ss').val() + ' ' +
           $('#pubdate-select-tz').val();
     return str;
   }; // end function makePubDate


   function sortDropDownListByText( theSelect) {
     $(theSelect).each(function() {
       var selectedValue = $(this).val();
       $(this).html($("option", $(this)).sort(function(a, b) {
       var rtn;
       if ( a.text == b.text ){
        rtn = 0;
       }else if ( a.text < b.text ){
         rtn = -1;
       }else{
         rtn = 1;
       }
       return rtn;
       }));
       $(this).val(selectedValue);
     });
    }; // end function sortDropDownListByText


    function refreshPubDateFields( val ){
      $.getJSON(
        'override_ajax_util.php',
        { todo : val },
        function( data, textStatus ) {

          $('#pubdate-select-day').val( data['D'] );
          $('#pubdate-select-nnn').val( data['d'] );
          $('#pubdate-select-month').val( data['M'] );
          $('#pubdate-select-year').val( data['Y'] );
          $('#pubdate-select-hh').val( data['H'] );
          $('#pubdate-select-mm').val( data['i'] );
          $('#pubdate-select-ss').val( data['s'] );
          $('#pubdate-select-tz').val( data['T'] );

          $('#pubDate').val( data['pd'] );
          $('#pubdate-time-msg').text( data['12'] );
          $('#pubdate-time-msg').show();

        }// end return call function
      ); // end AJAX to get todsy's date
    };// end function refreshPubDateFields


    function verifyPubDate( key, val, type  ){
      $.getJSON(
        'override_ajax_util.php',
        { todo : key,
          vpd  : val,
          typ  : type
        },
        function( data, textStatus ) {
          if ( data['valid'] == 'yes' ){
            if ( type == 'i' ){
              $('#pubdate-time-msg').html( data['hd'] );
            }else{
              $('#pubdate-time-msg').html( data['msg'] );
            }
          }else if ( data['valid'] == 'wrn' ){
            var txt = '';
            txt += data['hd'];
            txt += '<br />';
            txt += data['msg'];
            $('#pubdate-time-msg').html( txt );
          }else{
            var txt = '';
            if ( data['Day'] != '' ){
              txt += data['Day'];
            }
            if ( data['Day #'] != '' ){
              txt += data['Day #'];
            }
            if ( data['month'] != '' ){
              txt += data['month'];
            }
            if ( data['year'] != '' ){
              txt += data['year'];
            }
            if ( data['hh'] != '' ){
              txt += data['hh'];
            }
            if ( data['mm'] != '' ){
              txt += data['mm'];
            }
            if ( data['ss'] != '' ){
              txt += data['ss'];
            }
            if ( data['tz'] != '' ){
              txt += data['tz'];
            }

            txt += data['msg'];
            $('#pubdate-time-msg').html( txt );

          }
          $('#pubdate-time-msg').css( {'color' : 'red'} );
          $('#pubdate-time-msg').show();
        }// end return call function
      ); // end AJAX to get todsy's date
    }; // end function verifyPubDate


    function verifyDuration( key, val, action  ){
      $.getJSON(
        'override_ajax_util.php',
        { todo : key,
          vpd  : val
        },
        function( data, textStatus ) {
          //data['valid'] );
          //data['newStr'] );
          //data['newValue'] );
          if ( action == 'v' ){
            $('#duration-verify-msg').text( data['msg'] );
            $('#duration-verify-msg').css( {'color' : 'red'} );
          }else if ( action == 'i' ){
            $('#duration-verify-msg').text( data['inwords'] );
            $('#duration-verify-msg').css( {'color' : 'black'} );
          }
        }// end return call function
      ); // end AJAX
    }; // end function verifyDuration


    function verifyLength( key, val, action  ){
      $.getJSON(
        'override_ajax_util.php',
        { todo : key,
          vpd  : val
        },
        function( data, textStatus ) {
          if ( data['newvalue'] != '' ){
            $('#enclosureLength').val( data['newvalue'] );
          }
          if ( action == 'v' ){
            $('#enclosure-length-verify-msg').text( data['msg'] );
            $('#enclosure-length-verify-msg').css( {'color' : 'red'} );
          }else if ( action == 'i' ){
            $('#enclosure-length-verify-msg').text( data['newstr'] );
            $('#enclosure-length-verify-msg').css( {'color' : 'black'} );
          }
        }// end return call function
      ); // end AJAX
    }; // end function verifyLength


    function verifyEnclosureURL( key, val, action  ){
      $.getJSON(
        'override_ajax_util.php',
        { todo : key,
          vpd  : val
        },
        function( data, textStatus ) {
          if ( action == 'v' ){
            $('#enclosureURL-verify-msg').html( data['msg'] );
            $('#enclosureURL-verify-msg').css( {'color' : 'red'} );
          }else if ( action == 'i' ){
            $('#enclosureURL-verify-msg').html( data['newstr'] );
            $('#enclosureURL-verify-msg').css( {'color' : 'black'} );
          }
        }// end return call function
      ); // end AJAX
    }; // end function verifyEnclosureURL



    function setValAndLength( source, target ){
      var foundAt = $(source).val().lastIndexOf( "\n" );
      if ( foundAt != -1 ){
        val = $(source).val().substr( 0, foundAt );
      }else{
        val = $(source).val();
      }
      $(source).val( val );
      $(target).text( val.length );
    }// end function setValAndLength


    function populateSelects( val ){
      // AJAX to get users pubdate for selects
      $.getJSON(
        'override_ajax_util.php',
        { todo : 'pd_user',
          value: val
        },
        function( data, textStatus ) {
          if ( data['valid'] == 'yes' ){
            $('#pubdate-select-day').val( data['D'] );
            $('#pubdate-select-nnn').val( data['d'] );
            $('#pubdate-select-month').val( data['M'] );
            $('#pubdate-select-year').val( data['Y'] );
            $('#pubdate-select-hh').val( data['H'] );
            $('#pubdate-select-mm').val( data['i'] );
            $('#pubdate-select-ss').val( data['s'] );
            $('#pubdate-select-tz').val( data['T'] );
          }
        }// end return call function
      ); // end AJAX to get users pubdate selects
    }// end function populateSelects

/* end override.js */
