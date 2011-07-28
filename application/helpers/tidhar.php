<?php

class tidhar
{
  public static function history( Collection $collection, array $search_history )
  {
    $items = '';
    $search_history = array_reverse( $search_history );

    foreach ( $search_history as $params ) {
      $query = Query_Tidhar::factory( $collection, $params );
      
      if ( ! $query ) {
        continue;
      }

      $url = $collection->getUrl() . '/search?' . http_build_query( $params );
      
      if ( $query instanceof Query_Tidhar_Roman ) {
        $display_query = $query->getEnglishName();
      }
      else {
        $display_query = str_replace(
          'EX:', L10n::_('Entry title: '), $query->getDisplayQuery()
        );
      }

      $link = myhtml::element( 'a', $display_query, array( 'href' => $url ) );
      $items .= myhtml::element( 'li', $link );
    }

    return myhtml::element( 'ol', $items );
  }

  public static function random_entries( $collection, $count = 3 )
  {
    $random = $collection->getRandomNodesHavingMetadata( 'Subject', $count );

    $inner_html = '';

    foreach ( $random as $node ) {
      $node_page = NodePage::factory( $collection, $node );
      $subject = $node->getField('Subject');

      if ( is_array( $subject ) ) {
        $index = rand( 0, count( $subject) - 1 );
        $subject = $subject[ $index ];
      }

      $subject = tidhar::flip_subject( $subject );

      $inner_html .= '<li>' . myhtml::element( 'a', $subject, array( 'href' => $node_page->getUrl() ) ) . "</li>\n";
    }

    $attr = array(
      'dir'   => 'rtl',
      'id'    => 'random-entries',
      'class' => 'rtl',
    );

    return myhtml::element( 'ul', $inner_html, $attr );
  }

  public static function result_summary( HitsPage $hits_page, SearchHandler $search_handler )
  {
    if ( $hits_page->hits ) {
      $format = 'Results <strong>%d</strong> - <strong>%d</strong> of '
                . '<strong>%d</strong> for <strong>%s</strong>';

      $display_query = str_replace(
        'EX:',
        L10n::_('Entry title: '),
        $search_handler->getQuery()->getDisplayQuery()
      );


      $args = array(
        $hits_page->firstHit, $hits_page->lastHit,
        $search_handler->getTotalHitCount(),
        $display_query,
      );
    }
    else {
      $format = 'No results were found for your search '
                . '<strong>%s</strong>';
      $args = array( $search_handler->getQuery()->getDisplayQuery() );
    }

    return L10n::vsprintf( $format, $args );
  }

  public static function form_simple(
    Collection $collection, SearchHandler $search_handler = null
  )
  {
    $text_attributes = array(
      'type'  => 'text',
      'class' => 'has-search-helper',
      'id'    => 'search-form-simple-text',
      'name'  => 'q',
    );

    if ( $search_handler && $search_handler->getQuery() instanceof Query_Tidhar_Simple ) {
      // this page is the result of a simple search, so fill in the form
      $params = $search_handler->getQuery()->getParams();
      $value  = str_replace( '\\"', '"', $params['q'] );
      $text_attributes['value'] = htmlentities( $value, ENT_COMPAT, 'UTF-8' );
    }
    elseif (
      L10n::getLanguage() != 'he'
      && ( ! isset( $params['q'] )
        || ! $params['q'] )
    ) {
      $text_attributes['value'] = L10n::_('Hebrew characters only here...');
    }

    $text_element = myhtml::element('input', null, $text_attributes);

    $submit_attributes = array(
      'type'  => 'submit',
      'value' => L10n::_('Search'),
    );

    $submit_element = myhtml::element('input', null, $submit_attributes);

    $form_attributes = array(
      'name'   => 'search',
      'id'     => 'search-form-simple',
      'class'  => 'search-form',
      'action' => $collection->getUrl() . '/search',
      'method' => 'GET',
    );

    $form_contents = $text_element . $submit_element;

    return myhtml::element('form', $form_contents, $form_attributes);
  }

  public static function form_sidebar(
    Collection $collection
  )
  {
    $text_attributes = array(
      'type'  => 'text',
      'class' => 'search-form-sidebar-text',
      'name'  => 'q',
    );

    $text_element = myhtml::element('input', null, $text_attributes);

    $submit_attributes = array(
      'type'  => 'submit',
      'class' => 'submit',
      'value' => L10n::_('Search'),
    );

    $submit_element = myhtml::element('input', null, $submit_attributes);

    $form_attributes = array(
      'name'   => 'search',
      'class'  => 'search-form-sidebar',
      'action' => $collection->getUrl() . '/search',
      'method' => 'GET',
    );

    $form_contents = $text_element . $submit_element;

    return myhtml::element('form', $form_contents, $form_attributes);
  }

  public static function form_fielded(
    Collection $collection, SearchHandler $search_handler = null
  )
  {
    if ( $search_handler && $search_handler->getQuery() instanceof Query_Tidhar_Fielded ) {
      $params = $search_handler->getQuery()->getParams();
      $index_default = 'EX';
      $text_default  = isset( $params['q'] ) ? $params['q'] : null;
    }
    else {
      $params = null;
      $index_default = 'EX';
      $text_default = null;
    }

    $index_attr = array(
      'type'  => 'hidden',
      'name'  => 'i',
      'value' => $index_default,
    );

    $index_hidden = myhtml::element( 'input', null, $index_attr );

    $text_attr = array(
      'type' => 'text',
      'name' => 'q',
      'id'   => 'search-form-entry-text',
      'value' => htmlentities( $text_default, ENT_COMPAT, 'UTF-8' ),
    );

    $value  = str_replace( '\\"', '"', $text_default );
    $text_attr['value'] = htmlentities( $value, ENT_COMPAT, 'UTF-8' );

    $text_input = myhtml::element( 'input', null, $text_attr );

    $submit_attr = array(
      'type' => 'submit',
      'value' => L10n::_('Search'),
    );

    $submit_input = myhtml::element( 'input', null, $submit_attr );

    $format = 'Search entry titles for %1$s';
    $args = array( $text_input );

    $form_contents = L10n::vsprintf( $format, $args, true )
                     . $index_hidden . $submit_input;

    $form_attributes = array(
      'name'   => 'search',
      'id'     => 'search-form-fielded',
      'class'  => 'search-form',
      'action' => $collection->getUrl() . '/search',
      'method' => 'GET',
    );

    return myhtml::element( 'form', $form_contents, $form_attributes );
  }

  public static function chooser()
  {
    $simple  = L10n::_('All');
    $title   = L10n::_('Entry titles');

    $html = <<<EOF
      <ul id="search-form-chooser">
        <li>
          <a id="search-form-link-simple" class="search-form-link" href="#">$simple</a>
        </li>
        <li>
          | <a id="search-form-link-fielded" class="search-form-link" href="#">$title</a>
        </li>
        <li id="enable-hebrew-keyboard" style="display:none">
          | <a href="#">Enable Hebrew keyboard</a>
        </li>
      </ul>
EOF;

    return $html;
  }

  public static function roman_name_search( Collection $collection, SearchHandler $search_handler = null )
  {
    $params = $search_handler ? $search_handler->getQuery()->getParams() : array();

    $text_attributes = array(
      'type'  => 'text',
      'class' => 'has-search-helper',
      'id'    => 'input-roman-name',
      'name'  => 'roman-name',
    );

    if ( isset( $params['roman-name'] ) && $params['roman-name'] ) {
      $text_attributes['value'] = $params['roman-name'];
    }
    else {
      $text_attributes['value'] = L10n::_('...or enter name in English here');
    }

    $text_element = myhtml::element('input', null, $text_attributes);

    $submit_attributes = array(
      'type'  => 'submit',
      'value' => L10n::_('Search'),
    );

    $submit_element = myhtml::element('input', null, $submit_attributes);

    $form_attributes = array(
      'name'   => 'search',
      'id'     => 'search-form-roman-name',
      //'class'  => 'search-form',
      'action' => $collection->getUrl() . '/search',
      'method' => 'GET',
    );

    $form_contents = $text_element . $submit_element;

    return myhtml::element('form', $form_contents, $form_attributes);
  }

  public static function citation( NodePage_DocumentSection $node_page )
  {
    $volumes = array(
      '1'  => '1947',
      '2'  => '1947',
      '3'  => '1949',
      '4'  => '1950',
      '5'  => '1952',
      '6'  => '1955',
      '7'  => '1956',
      '8'  => '1957',
      '9'  => '1958',
      '10' => '1959',
      '11' => '1961',
      '12' => '1962',
      '13' => '1963',
      '14' => '1965',
      '15' => '1966',
      '16' => '1967',
      '17' => '1968',
      '18' => '1969',
      '19' => '1971',
    );

    $page   = $node_page->getNode()->getField('Title');
    $volume = $node_page->getNode()->getParent()->getField('Volume');
    $year   = $volumes[ $volume ];
    $url    = $node_page->getUrl();

    $ret = 'Tidhar, D. (' . $year . '). <i>Entsiklopedyah le-halutse '
           . 'ha-yishuv u-vonav</i> (Vol. ' . $volume . ', p. ' . $page
           . '). Retrieved from ' . $url;

    return $ret;
  }

  public static function discoveries( Collection $collection )
  {
    if ( ! is_array( $collection->getConfig( 'discoveries' ) ) ) {
      return '';
    }

    $ret = '<ul>' . chr(10);

    foreach ( $collection->getConfig( 'discoveries' ) as $name => $quote ) {
      $ret .= '<li><q>' . $quote . '</q><cite>' . $name . '</cite></li>' . chr(10);
    }
    
    $ret .= '</ul>' . chr(10);
    
    return $ret;
  }

  protected static function flip_subject( $subject )
  {
    if ( preg_match( '/(^[^,]+), +(.*)/', $subject, $matches ) ) {
      return $matches[2] . ' ' . $matches[1];
    }
    else {
      return $subject;
    }
  }
}
