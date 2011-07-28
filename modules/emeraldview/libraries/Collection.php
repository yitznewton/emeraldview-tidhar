<?php
/**
 * EmeraldView
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://yitznewton.org/emeraldview/index.php?title=License
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to yitznewton@hotmail.com so we can send you a copy immediately.
 *
 * @version 0.2.0
 * @package libraries
 */
/**
 * Collection is a container interface for various collection-level resources
 *
 * @package libraries
 * @copyright  Copyright (c) 2010 Benjamin Schaffer (http://yitznewton.org/)
 * @license    http://yitznewton.org/emeraldview/index.php?title=License     New BSD License
 */
class Collection
{
  /**
   * The slug name of the Collection, either as specified in
   * config/emeraldview.yml or as the Greenstone directory name
   *
   * @var string
   */
  private $name;
  /**
   * Subdirectory of GSDLHOME/collect which houses the Collection
   *
   * @var string
   */
  private $greenstoneDirName;
  /**
   * An array of all the Collection's active NodePage_Classifiers, as
   * specified in config/emeraldview.yml
   *
   * @var array
   */
  private $classifiers;
  /**
   * The Collection's CollectCfg object
   *
   * @var CollectCfg
   */
  private $collectCfg;
  /**
   * The Collection's Infodb object
   *
   * @var Infodb
   */
  private $infodb;
  /**
   * The Collection's SlugLookup object
   *
   * @var SlugLookup
   */
  private $slugLookup;
  
  /**
   * The slug name of the Collection, either as specified in
   * config/emeraldview.yml or as the Greenstone directory name
   *
   * @param string $name
   */
  private function __construct( $name )
  {
    $collection_config = EmeraldviewConfig::get("collections.$name");
    
    if (
      empty( $collection_config )
      || (isset( $collection_config['active'] )
          && $collection_config['active'] === false)
    ) {
      throw new InvalidArgumentException("Not an active collection ($name)");
    }
    
    $this->name = $name;

    if ( isset( $collection_config[ 'greenstone_dir' ] ) ) {
      $this->greenstoneDirName = $collection_config[ 'greenstone_dir' ];
    }
    else {
      $this->greenstoneDirName = $name;
    }
    
    if (
      !is_readable( $this->getGreenstoneDirectory() )
      || !is_dir( $this->getGreenstoneDirectory() )
    ) {
      $msg = "Trying to load collection $name; could not access Greenstone "
           . 'collection directory (' . $this->getGreenstoneDirectory() .')';
      throw new Exception( $msg );
    }
    
    $this->collectCfg = CollectCfg::factory( $this );
    $this->infodb     = Infodb::factory( $this );
    $this->buildCfg   = BuildCfg::factory( $this );

    if ( ! in_array( 'section', $this->getIndexLevels() ) ) {
      $msg = 'Only section-level indexes supported';
      throw new UnexpectedValueException( $msg );
    }

    $this->slugLookup = new SlugLookup( $this );

    $this->slugLookup->initialize();
  }
  
  /**
   * Returns one or all nodes from the Collection's config settings as
   * specified in config/emeraldview.yml
   * 
   * @param string $subnode
   * @param mixed $default
   * @return mixed
   */
  public function getConfig( $subnode = null, $default = null )
  {
    $node = 'collections.' . $this->name;
    
    if ($subnode) {
      $node .= '.' . $subnode;
    }

    $value = EmeraldviewConfig::get( $node );

    if ( $value === null ) {
      $value = $default;
    }
    
    return $value;
  }
  
  /**
   * Returns an array of strings indicating the available indexes
   * 
   * @return array
   */
  public function getIndexes()
  {
    return $this->buildCfg->getIndexes();
  }
  
  /**
   * Returns the Collection's CollectCfg object
   * 
   * @return CollectCfg
   */
  public function getCollectCfg()
  {
    return $this->collectCfg;
  }

  /**
   * Returns the Collection's BuildCfg object
   *
   * @return BuildCfg
   */
  public function getBuildCfg()
  {
    return $this->buildCfg;
  }
  
  /**
   * Returns the Collection's Infodb object
   *
   * @return Infodb
   */
  public function getInfodb()
  {
    return $this->infodb;
  }

  /**
   * Returns the Collection's SlugLookup object
   *
   * @return SlugLookup
   */
  public function getSlugLookup()
  {
    return $this->slugLookup;
  }
  
  /**
   * Returns the slug name of the Collection, either as specified in
   * config/emeraldview.yml or as the Greenstone directory name
   * 
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  
  /**
   * Returns an array of strings indicating the available index levels
   *
   * @return array
   */
  public function getIndexLevels()
  {
    return $this->getCollectCfg()->getLevels();
  }

  /**
   * Returns the display name of the Collection in a given language
   * 
   * @param string $language_code
   * @return string
   */
  public function getDisplayName( $language_code )
  {
    return $this->getCollectCfg()
           ->getMetadata( 'collectionname', $language_code );
  }
  
  /**
   * Returns the description of the Collection in a given language
   *
   * @param string $language_code
   * @return string
   */
  public function getDescription( $language_code )
  {
    return $this->getCollectCfg()
           ->getMetadata( 'collectionextra', $language_code );
  }
  
  /**
   * Returns the subdirectory of GSDLHOME/collect which houses the Collection
   *
   * @return string
   */
  public function getGreenstoneName()
  {
    return $this->greenstoneDirName;
  }

  /**
   * Returns the complete path to the Greenstone collection files
   *
   * @return string
   */
  public function getGreenstoneDirectory()
  {
    $dir = EmeraldviewConfig::get('greenstone_collection_dir')
         . '/' . $this->getGreenstoneName();
         
    return $dir;
  }

  /**
   * Returns the URL of the Collection's EmeraldView home page
   *
   * @return string
   */
  public function getUrl()
  {
    return url::base() . $this->getName();
  }

  /**
   * Returns the base URL for EmeraldView's presentation of the Collection's
   * file archive
   *
   * @return string
   */
  public function getGreenstoneUrl()
  {
    return url::base() . 'files/' . $this->getGreenstoneName();
  }
  
  /**
   * @return array
   */
  private function getClassifierIds()
  {
    return $this->infodb->getClassifierIds();
  }
  
  /**
   * Returns an array of all the Collection's active NodePage_Classifiers, as
   * specified in config/emeraldview.yml
   *
   * @return array
   */
  public function getClassifiers()
  {
    if (isset( $this->classifiers )) {
      return $this->classifiers;
    }
    
    $classifiers = array();
    
    foreach ( $this->getClassifierIds() as $id ) {
      if ( $this->getConfig( "classifiers.$id.active" ) === false ) {
        // this Classifier is not active
        continue;
      }
      
      $node = $this->getNode( $id );
      $classifiers[] = NodePage_Classifier::factory( $this, $node );
    }
    
    return $this->classifiers = $classifiers;
  }

  /**
   * Returns a Node_Document based on a given title; for use with continuous
   * paged Collections
   *
   * @param string $title The Node's title
   * @return Node_Document
   */
  public function getNodeByTitle( $title )
  {
    $id = $this->getInfodb()->getNodeIdByTitle( $title );

    return $this->getNode( $id );
  }

  /**
   * Returns a Node from the Collection bearing the specified ID
   *
   * @param string $id The Node's ID
   * @return Node
   */
  public function getNode( $id )
  {
    return Node::factory( $this->infodb, $id );
  }

  /**
   * Returns an array of randomly-selected nodes which have at least one
   * instance of the specified metadata element
   *
   * @param string $element
   * @param integer $count
   * @return array
   */
  public function getRandomNodesHavingMetadata( $element, $count = 1 )
  {
    $node_ids = $this->getInfodb()->getRandomLeafNodeIdsHavingMetadata( $element, $count );
    $nodes    = array();

    foreach ( $node_ids as $id ) {
      $nodes[] = $this->getNode( $id );
    }

    return $nodes;
  }
  
  /**
   * @param string $name
   * @return Collection
   */
  public static function factory( $name )
  {
    try {
      return new Collection( $name );
    }
    catch (Exception $e) {
      Kohana::log( 'error', $e->getMessage() );
      return false;
    }
  }
  
  /**
   * Returns an array of all available Collections within EmeraldView
   *
   * @return array
   */
  public static function getAllAvailable()
  {
    $collections_config = EmeraldviewConfig::get('collections');

    $collections = array();
    
    foreach ($collections_config as $name => $config) {
      if ( isset($config['active']) && !$config['active'] ) {
        continue;
      }
      
      $collection = Collection::factory( $name );

      if ($collection) {
        $collections[] = $collection;
      }
    }

    return $collections;
  }
}
