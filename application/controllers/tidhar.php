<?php

class Tidhar_Controller extends Emeraldview_Controller
{
  public function about( $collection_name )
  {
    parent::about( $collection_name );
    $this->template->addJs( 'views/tidhar/js/search' );
    $this->passDown( 'page_title', 'Encyclopedia of the Founders and Builders of Israel | אנציקלופדיה לחלוצי הישוב ובוניו' );
  }

  public function search( $collection_name )
  {
    if ( ! $this->input->get() ) {
      url::redirect( $collection_name );
    }

    $collection = $this->loadCollection( $collection_name );

    if ( ! $collection ) {
      return $this->show404();
    }

    $per_page = $collection->getConfig( 'search_hits_per_page', 20 );

    if ( (int) $this->input->get( 'p' ) ) {
      $start_at = 1 + ((int) $this->input->get( 'p' ) - 1) * $per_page;
    }
    else {
      $start_at = 1;
    }

    $query = Query_Tidhar::factory( $collection, $this->input->get() )
      or url::redirect( $collection->getUrl() );

    //$search_handler = SearchHandler::factory( $query );
    $search_handler = SearchHandler_Tidhar::factory( $query );
    $search_handler->setHitsPerPage( $per_page );
    $search_handler->setStartAt( $start_at );

    $hits_page = new HitsPage( $search_handler )
      or url::redirect( $collection->getUrl() );

    // add Roman name to history tracking
    $relevant_params = $this->session->getRelevantParams();
    $relevant_params[] = 'roman-name';
    $this->session->setRelevantParams( $relevant_params );

    $history = $this->session->getSearchHistory( $collection );
    $this->session->recordSearch( $query );

    $this->loadView( 'search' );
    $this->template->addJs( 'views/tidhar/js/search' );

    $this->passDown( 'page_title', 'Search'
                                   . ' | ' . $this->collection->getDisplayName( $this->language )
                                   . ' | ' . $this->emeraldviewName );
    $this->passDown( 'language_select', myhtml::language_select( $this->availableLanguages, $this->language ) );
    $this->passDown( 'search_handler',  $search_handler );
    $this->passDown( 'search_history',  $history );
    $this->passDown( 'hits_page',       $hits_page );
  }

  public function searchtips()
  {
    $this->loadCollection( 'tidhar' );

    $this->setTheme( 'tidhar' );
    $this->loadView( 'search_tips' );
    $this->passDown( 'page_title', 'Tips on using search'
                                   . ' | ' . $this->collection->getDisplayName( $this->language )
                                   . ' | ' . $this->emeraldviewName );
  }

  public function contact()
  {
    $collection = $this->loadCollection( 'tidhar' );

    $validation_errors = array();

    $captcha = new Captcha();

    if ( $this->input->post() ) {
      $this->passDown( 'submitted', true );

      $name        = $this->input->post('name');
      $email       = $this->input->post('email');
      $institution = $this->input->post('institution');
      $position    = $this->input->post('position');
      $message     = $this->input->post('message');
      $cc          = $this->input->post('cc');
      $public      = $this->input->post('publicize');

      if ( ! $email ) {
        $validation_errors['email'] = L10n::_('You must provide an email address.');
      }
      elseif ( ! valid::email( $email ) ) {
        $validation_errors['email'] = L10n::_('The email address you specified is invalid.');
      }

      if ( ! $message ) {
        $validation_errors['message'] = L10n::_('You must provide a message.');
      }

      if ( ! Captcha::valid( $this->input->post( 'captcha' ) ) ) {
        $validation_errors['captcha'] = L10n::_('Try again.');
      }

      if ( empty( $validation_errors ) ) {
        // send email
        
        $to = $collection->getConfig('send_email_to');

        if ( ! $to ) {
          throw new Exception('No send_email_to specified');
        }

        $subject = 'From Tidhar contact form';

        if ( $public ) {
          $subject .= ' - MAY PUBLICIZE';
        }

        $message = 'Message:'. chr(10) . $message;

        if ( $position ) {
          $message = 'Position: ' . $position . chr(10) . $message;
        }

        if ( $institution ) {
          $message = 'Institution: ' . $institution . chr(10) . $message;
        }

        require_once Kohana::find_file('vendors', 'swiftmailer/swift_required', true);

        $message = Swift_Message::newInstance()
                   ->setFrom(      $email   )
                   ->setTo(        $to      )
                   ->setSubject(   $subject )
                   ->setBody(      $message )
                   ;

        if ( is_string( $name ) ) {
          $message->setFrom( array( $email => $name ) );
        }
        
        if ( $cc ) {
          $message->addCC( $email );
        }
        
        $transport = Swift_SendmailTransport::newInstance();
        $mailer    = Swift_Mailer::newInstance( $transport );
        $result    = $mailer->send( $message );
        
        if ( ! $result ) {
          throw new Exception('Sending contact email failed');
        }
      }
      else {
        $this->passDown( 'email',       $email );
        $this->passDown( 'name',        $name );
        $this->passDown( 'institution', $institution );
        $this->passDown( 'position',    $position );
        $this->passDown( 'message',     $message );
      }

      $this->passDown( 'errors', $validation_errors );
    }
    else {
      $this->passDown( 'submitted', false );
    }

    $this->setTheme( 'tidhar' );
    $this->loadView( 'contact' );
    $this->passDown( 'page_title', 'Contact us' );
    $this->passDown( 'errors',     $validation_errors );
  }

  public function view( $collection_name, $slug )
  {
    call_user_func_array(
      array( $this, 'parent::view' ), func_get_args()
    );

    $text = $this->page->getHTML();

    $text = preg_replace_callback( '/עמוד (\\d+)/u',
      array($this, 'crossReferenceLink'), $text );

    $search_terms = $this->input->get('search');

    if ( ! $search_terms ) {
      $search_terms = array();
    }

    if ( $search_terms ) {
      $highlighter = new Highlighter_Text();
      $highlighter->setTerms( $search_terms );
      $highlighter->setDocument( $text );
      $text = $highlighter->execute();
    }

    $this->passDown( 'text', $text );
  }

  public function crossReferenceLink( $matches )
  {
    if ( ! isset( $matches[0] ) || ! $matches[0] ) {
      throw new UnexpectedValueException( 'Matches don\'t make sense' );
    }

    if ( ! isset( $matches[1] ) ) {
      return $matches[0];
    }

    $node = RouteNodeTranslator::factory( $this->collection, $this->root_node )
            ->getNode( array( $matches[1] ) );

    if ( ! $node ) {
      // whatever subnode was requested could not be found
      return $matches[0];
    }

    $url = NodePage::factory( $this->collection, $node )->getUrl();

    // avoid myhtml::element() because of the newline it inserts
    $a_element = '<a href="' . $url . '">' . $matches[0] . '</a>';

    return $a_element;
  }

  public function show404()
  {
    $this->loadCollection( 'tidhar' );
    
    return parent::show404();
  }
}
