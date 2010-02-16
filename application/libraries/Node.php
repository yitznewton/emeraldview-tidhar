<?php

abstract class Node
{
  protected $collection;
  protected $id;
  protected $data;
  protected $children = array();
  protected $rootNode;
  
  abstract protected function recurse();
  abstract protected function getChild( $node_id );
  
  protected function getFormatter()
  {
    return new NodeFormatter( $this );
  }

  protected function __construct(
    Collection $collection, $node_id = null, $root_only = false
  )
  {
    $this->id = $node_id;
    $this->data = $collection->getInfodb()->getNode( $this->id );

    if ( ! $this->data ) {
      throw new InvalidArgumentException('No such node');
    }

    $this->collection = $collection;
    
    if ( ! $root_only ) {
      $this->recurse();
    }

    // clean up for later metadata retrieval
    unset( $this->data['contains'] );
  }
  
  public function format()
  {
    $node_formatter = NodeFormatter::factory( $this );
    $text = $node_formatter->format();

    if ( strpos( $text, '<a' ) === false ) {
      $text = html::anchor( $this->getPage()->getUrl(), $text );
    }

    return $text;
  }

  public function getAncestors()
  {
    $id = $this->getId();
    $ancestors = array();

    while ( strpos( $id, '.' ) !== false ) {
      $id = substr( $id, 0, strrpos( $id, '.' ) );
      $ancestors[] = $this->getCousin( $id );
    }

    return array_reverse( $ancestors );
  }

  public function getId()
  {
    return $this->id;
  }

  public function getRootId()
  {
    if (strpos( $this->id, '.' )) {
      return substr( $this->id, 0, strpos( $this->id, '.' ) );
    }
    else {
      return $this->id;
    }
  }

  public function getSubnodeId()
  {
    if (strpos( $this->id, '.' )) {
      return substr( $this->id, strpos( $this->id, '.' ) + 1 );
    }
    else {
      return false;
    }
  }

  public function getRootNode()
  {
    if ($this->rootNode) {
      return $this->rootNode;
    }
    
    if ( $this->getRootId() == $this->getId() ) {
      $this->rootNode = $this;
      
      return $this->rootNode;
    }

    $class = get_class( $this );

    // ugly workaround for lack of LSB in < 5.3
    $this->rootNode = $this->staticFactory(
      $this->collection, $this->getRootId()
    );
    
    return $this->rootNode;
  }

  public function getPage()
  {
    return NodePage::factory( $this );
  }

  public function getCousin( $id )
  {
    if (strpos($id, $this->getRootId()) === false) {
      // client did not specify the root ID (really this is the more sensible
      // way to call, but we are accomodating certain methods that favor
      // the full subnode id)
      $id = $this->getRootId() . '.' . $id;
    }

    return $this->staticFactory( $this->collection, $id );
  }

  public function getCollection()
  {
    return $this->collection;
  }

  public function getChildren()
  {
    return $this->children;
  }
  
  public function getField( $field_name )
  {
    if (array_key_exists( $field_name, $this->data )) {
      return $this->data[ $field_name ];
    }
    else {
      return false;
    }
  }

  public function getFirstFieldFound( $field_names )
  {
    if ( ! is_array( $field_names ) ) {
      $field_names = array( $field_names );
    }

    foreach ( $field_names as $field ) {
      if ( $this->getField( $field ) ) {
        return $this->getField( $field );
      }
    }

    return false;
  }

  public function getAllFields()
  {
    return $this->data;
  }

  abstract public static function factory(
    Collection $collection, $node_id, $root_only = false
  );

  abstract protected function staticFactory(
    Collection $collection, $node_id, $root_only = false
  );
}