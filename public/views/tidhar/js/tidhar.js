function changeLanguage( language_select )
{
  langcode = language_select.value;
  path = window.location.pathname;

  querystring = window.location.search.substring(1);
  querystring = querystring.replace( /&?language=\w+/, '' );

  if (querystring) {
    querystring += '&language=' + language_select.value;
  }
  else {
    querystring = 'language=' + language_select.value;
  }

  window.location = path + '?' + querystring;

  return true;
}

function toggleTOC()
{
  hide_span = $('#toc-toggle-hide');
  show_span = $('#toc-toggle-show');
  isTocShown  = (hide_span.css('display') == 'inline') ? true : false;

  if (isTocShown) {
    $('#toc-container').hide();
    hide_span.hide();
    show_span.show();
  }
  else {
    $('#toc-container').show();
    hide_span.show();
    show_span.hide();
  }

  return false;
}

$(document).ready( function() {
  $('#language-select-select').change( function() {
    changeLanguage( this );
  });

  $('#language-select-submit').hide();

  $('#toc-hide').show();

  $('#toc-hide a').click( function() {
    $('#toc-hide').hide();
    $('#toc-show').show();
    $('#toc-container').hide();
  });

  $('#toc-show a').click( function() {
    $('#toc-hide').show();
    $('#toc-show').hide();
    $('#toc-container').show();
  });

  $('.search-tips-show-link a').click( function() {
    $('.search-tips-hide-link').show();
    $('.search-tips-show-link').hide();
    $('.search-tips').slideDown();
    return false;
  });

  $('.search-tips-hide-link a').click( function() {
    $('.search-tips-show-link').show();
    $('.search-tips-hide-link').hide();
    $('.search-tips').slideUp();
  });

  /*
  // not working
  $('.toc-toggle').click( function() {
    return toggleTOC();
  });
  */

  if ( $(".browse-tabs").length != 0 ) {
    var spinners = $('.browse-tabs .spinner');

    $(".browse-tabs").tabs({
      // callbacks to fire treeview() on trees within AJAX-loaded tabs
      select: function( event, ui ) {
        spinners.hide();  // earlier selections
        $( ui.tab ).children('.spinner').show();
      },
      load: function( event, ui ) {
        var trees = $( ui.panel ).children('.browse-tree');
        if ( $( trees ).length != 0 ) {
          $( trees ).treeview({
            collapsed: true,
            animated:  "fast",
            persist:   "location"
          });
        }

        $( ui.tab ).children('.spinner').hide();
      },
      spinner: '&nbsp;',

      cookie: { expires: 0 }
    });

    var tabs = $('.browse-tabs').tabs();
    var selected = tabs.tabs('option', 'selected');

    if (selected === 0){
      spinners.hide();
      $( spinners.get(0) ).show();
    }
    else {
      spinners.hide();
      $( spinners.get(selected) ).show();
    }
  }

  if ( $(".browse-tree").length != 0 ) {
    $(".browse-tree").treeview({
      collapsed: true,
      animated:  "fast",
      persist:   "location"
    });
  }

  $('h2.browse-section').hide();
  
  $('#pager-form').submit( function() {
    if ( typeof doc_url !== undefined || doc_url ) {
      new_url = doc_url + '/' + $(this).children('input:text').get(0).value;
      window.location = new_url;

      return false;
    }
  });
});
