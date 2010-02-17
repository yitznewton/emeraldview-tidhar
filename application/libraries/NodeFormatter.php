<?php

class NodeFormatter
{
  protected $node;

  protected function __construct( Node $node )
  {
    $this->node = $node;
  }

  public function format()
  {
    $field_names = array( 'dc.Title', 'Title' );
    $title = $this->node->getFirstFieldFound( $field_names );
    
    if (is_array( $title )) {
      $text = $title[0];
    }
    elseif ($title) {
      $text = $title;
    }
    else {
      $text = $this->node->getId();
    }

    return $text;
  }

  public static function factory( Node $node, $context )
  {
    switch ( get_class( $context ) ) {
      case 'NodePage_Classifier':
        $prefix = 'classifiers.' . $context->getId() . '.';
        break;
        
      case 'NodePage_DocumentSection':
        $prefix = 'document_tree_';
        break;
      
      case 'SearchHandler':
        $prefix = 'search_results_';
        break;
      
      default:
        throw new InvalidArgumentException( 'Invalid $caller' );
    }

    $format_string = $context->getCollection()->getConfig( $prefix . 'format' );
    if ( $format_string ) {
      return new NodeFormatter_String( $node, $format_string );
    }

    $function_definition = $context->getCollection->getConfig( $prefix . 'format_function' );
    // FIXME: this constructor needs refactoring
    if ( $function_definition ) {
      return new NodeFormatter_Function( $node, $function_definition );
    }

    // no supplied formatter configuration
    return new NodeFormatter( $node );
  }
}
