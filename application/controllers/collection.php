<?php

class Collection_Controller extends Emeraldview_Template_Controller
{
	public function index()
  {
		$this->template->page_title  = EmeraldviewConfig::get('emeraldview_name');
    
    $this->view = new View( $this->theme . '/index' );
    $this->view->collections = Collection::getAllAvailable();
    $this->template->set_global( 'language_select', myhtml::language_select( $this->availableLanguages, $this->language ) );
    $this->template->set_global( 'language', $this->language );
  }
  
  public function about( $collection_name )
  {
		$collection = $this->loadCollection( $collection_name );

    $this->view = new View( $this->theme . '/about' );
    
    $this->template->set_global( 'collection',      $collection );
    $this->template->set_global( 'collection_display_name',    $collection->getDisplayName( $this->language ) );
    $this->template->set_global( 'page_title',      $collection->getDisplayName( $this->language )
                                                    . ' | ' . EmeraldviewConfig::get('emeraldview_name') );
    $this->template->set_global( 'language_select', myhtml::language_select( $this->availableLanguages, $this->language ) );
    $this->template->set_global( 'description',     $collection->getDescription( $this->language ) );
  }
  
  public function browse( $collection_name, $classifier_name )
  {
    $collection = $this->loadCollection( $collection_name );
    $classifier = $collection->getClassifier( $classifier_name );

    if (!$classifier) {
      url::redirect( $collection->getUrl() );
    }
    
    $node_formatter = $classifier->getNodeFormatter();
    $tree = NodeTreeFormatter::format( $classifier->getTree(), $node_formatter );

    $this->view = new View( $this->theme . '/browse' );
    
    $this->template->set_global( 'collection',      $collection );
    $this->template->set_global( 'collection_display_name',    $collection->getDisplayName( $this->language ) );
    $this->template->set_global( 'page_title',      $classifier->getName()
                                                    . ' | ' . $collection->getDisplayName( $this->language )
                                                    . ' | ' . EmeraldviewConfig::get('emeraldview_name') );
    $this->template->set_global( 'classifier',      $classifier );
    $this->template->set_global( 'language_select', myhtml::language_select( $this->availableLanguages, $this->language ) );
    $this->template->set_global( 'description',     $collection->getDescription( $this->language ) );
    $this->template->set_global( 'tree',            $tree );
  }
  
  public function view( $collection_name, $slug )
  {
    $subnode_id = '';

    if (count( func_get_args() ) > 2) {
      $subnodes = array_slice( func_get_args(), 2 );
      $subnode_id = '.' . implode( '.', $subnodes );
    }

    $collection = $this->loadCollection( $collection_name );

    $document_id = $collection->getSlugLookup()->retrieveId( $slug );

    if (!$document_id) {
      url::redirect( $collection->getUrl() );
    }

    $document_id .= $subnode_id;

    $node = Node_Document::factory( $collection, $document_id );
    
    if (!$node) {
      url::redirect( $collection->getUrl() );
    }

    if ($collection->getConfig( 'document_tree_format' )) {
      $node_formatter = new NodeFormatter_String(
        $collection->getConfig( 'document_tree_format' )
      );
    }
    elseif ($collection->getConfig( 'document_tree_format_function' )) {
      $node_formatter = new NodeFormatter_Function(
        $collection->getConfig( 'document_tree_format_function' )
      );
    }
    else {
      $node_formatter = new NodeFormatter_String( '[Title]' );
    }

    $document_section = DocumentSection::factory( $node );
    $tree = NodeTreeFormatter::format( $document_section->getTree(), $node_formatter );

    $this->view = new View( $this->theme . '/view' );

    $this->template->set_global( 'collection',      $collection );
    $this->template->set_global( 'collection_display_name',    $collection->getDisplayName( $this->language ) );
    $this->template->set_global( 'page_title',      $node->getField('Title')
                                                    . ' | ' . $collection->getDisplayName( $this->language )
                                                    . ' | ' . EmeraldviewConfig::get('emeraldview_name') );
    $this->template->set_global( 'document',        $document_section );
    $this->template->set_global( 'language_select', myhtml::language_select( $this->availableLanguages, $this->language ) );
    $this->template->set_global( 'tree',            $tree );
    $this->template->set_global( 'tree_pager',      NodeTreePager::html( $node ) );
  }
}