function chooseSearchForm( id, slide )
{
  if ( slide == undefined ) {
    slide = false;
  }

  $('form.search-form').each( function() {
    if ( this.id == id ) {
      if (slide) {
        $(this).slideDown();
      }
      else {
        $(this).show();
      }
    }
    else {
      if (slide) {
        $(this).slideUp();
      }
      else {
        $(this).hide();
      }
    }
  });
}

function keyboard_enabled()
{
  if ( typeof keyboard_enabled_var !== 'undefined' ) {
    return keyboard_enabled_var;
  }

  if ( $.cookie( 'keyboard_enabled' ) == 'false' ) {
    keyboard_enabled_var = false;
  }
  else {
    keyboard_enabled_var = true;
  }

  return keyboard_enabled_var;
}

function show_roman_popup()
{
  if ( typeof show_roman_popup_var !== 'undefined' ) {
    return show_roman_popup_var;
  }

  show_roman_popup_var = false;  // for next time

  if ( $.cookie( 'already_shown_roman_popup' ) == 'true' ) {
    return false;
  }
  else {
    date_obj = new Date();
    date_obj.setTime( date_obj.getTime() + 1000*60*5 );  // five minutes

    cookie_options = { expires : date_obj };
    $.cookie( 'already_shown_roman_popup', 'true', cookie_options );
    
    return true;
  }
}

function enable_keyboard( val )
{
  if ( val ) {
    keyboard_enabled_var = true;
    $.cookie( 'keyboard_enabled', 'true' );
  }
  else {
    keyboard_enabled_var = false;
    $.cookie( 'keyboard_enabled', 'false' );
  }
}

function helper_text_for( id )
{
  switch ( id ) {
    case 'search-form-simple-text':
      return simple_default_text;
    case 'input-roman-name':
      return roman_default_text;
    default:
      return false;
  }
}

var active_text_input;
var keyboard_enabled_var;
var show_roman_popup_var;

$(document).ready( function() {
  if ( ! keyboard_enabled() ) {
    $('#enable-hebrew-keyboard').show();
  }

  $('.disable-keyboard-link').click( function() {
    $('#keyboard').hide( 'fast' );
    $('#enable-hebrew-keyboard').show();
    $('#enable-hebrew-keyboard').animate( { fontSize: '1.6em' } , 400 );
    $('#enable-hebrew-keyboard').animate( { fontSize: '1em' } , 400 );

    enable_keyboard( false );
  });

  $('#enable-hebrew-keyboard a').click( function() {
    $('#enable-hebrew-keyboard').fadeOut();

    enable_keyboard( true );
  });
  
  $('.key').click( function() {
    // FIXME: see if we can interrupt such that onblur never happens when we are
    // blurring in order to click a .key
    if ( active_text_input.value == helper_text_for( active_text_input.id ) ) {
      active_text_input.value = '';
      $(active_text_input).removeClass('search-text-helper');
    }
    
    if ( $(this).hasClass('backspace') ) {
      active_text_input.value = active_text_input.value.substring( 0, active_text_input.value.length - 1 );
    }
    else {
      active_text_input.value += $(this).attr('title');
    }
  });

  $('#search-form-simple-text').blur( function() {
    // this needs to come before the code that sets the helper text, so that
    // the helper text for the simple input not be copied to the entry input
    TH.$$('search-form-entry-text').value = this.value;
  });

  $('#search-form-entry-text').blur( function() {
    TH.$$('search-form-simple-text').value = this.value;
  });
  
  $('.search-form :text').each( function() {
    if (
      $(this).hasClass('has-search-helper')
      && ( this.value == helper_text_for( this.id ) || ! this.value )
    ) {
      this.value =  helper_text_for( this.id );
      $(this).addClass( 'search-text-helper' );
    }

    // Keyboard bindings for Hebrew inputs
    $(this).focus( function() {
      if ( keyboard_enabled() ) {
        active_text_input = this;

        var position = $(this).position();

        TH.$$('keyboard').style.top = position.top + 50 + 'px';
        TH.$$('keyboard').style.left = position.left + 'px';
        $('#keyboard').show( 'fast' );
      }

      if ( $(this).hasClass('has-search-helper') ) {
        $(this).removeClass('search-text-helper');

        if ( this.value === helper_text_for( this.id ) ) {
          this.value = '';
        }
      }
    })
    .blur( function() {
      if ( $(this).hasClass('has-search-helper') && ! this.value ) {
        $(this).addClass( 'search-text-helper' );
        this.value = helper_text_for( this.id );
      }
    });
  });

  var input_roman_name = TH.$$('input-roman-name');
  
  if (
    input_roman_name.value == helper_text_for( 'input-roman-name' )
    || ! input_roman_name.value
  ) {
    $(input_roman_name).addClass( 'search-text-helper' );
    input_roman_name.value = helper_text_for( 'input-roman-name' );
  }

  $(input_roman_name).focus( function() {
    console.log(input_roman_name.value);
    $(this).removeClass('search-text-helper');

    if ( this.value === helper_text_for( this.id ) ) {
      this.value = '';
    }
  })
  .blur( function() {
    if ( ! this.value ) {
      $(this).addClass( 'search-text-helper' );
      this.value =  helper_text_for( this.id );
    }
  });

  $('.search-form-link').click( function() {
    $('.search-form-link').removeClass('active');
    $(this).addClass('active');
  });

  TH.$$('search-form-link-simple').onclick = function() {
    chooseSearchForm( 'search-form-simple', true );
  };

  TH.$$('search-form-link-fielded').onclick = function() {
    chooseSearchForm( 'search-form-fielded', true );
  };

  if ( $('.search-form').length > 0 ) {
    $('*').click( function( event ) {
      $this = $(this);

      if (
        this.id == 'keyboard'
        || /\bkey\b/.test( this.className )
        || $this.parent().hasClass('search-form')
        || $this.parent().parent().hasClass('search-form')
      ) {
        event.stopPropagation();
        return;
      }

      // clicked "anywhere else"
      $('#keyboard').hide('fast');
    });
  }

  $('form').submit( function() {
    $('.search-text-helper').each( function() {
      this.value = '';
    })
  });

  $('#input-roman-name').focus( function() {
    if ( show_roman_popup() ) {
      $('#search-roman-popup').show().fadeOut( 4000 );
    }
  });
});
