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
 * Session extends the default Kohana session with search history support
 *
 * @package libraries
 * @copyright  Copyright (c) 2010 Benjamin Schaffer (http://yitznewton.org/)
 * @license    http://yitznewton.org/emeraldview/index.php?title=License     New BSD License
 */
class Session extends Session_Core
{
  protected $relevantParams = array('q', 'q1', 'q2', 'q3', 'i', 'i1', 'i2', 'i3', 'b1', 'b2', 'b3', 'l');

  /**
   * Returns an array of previous search parameter sets
   *
   * @param Collection $collection
   * @return array
   */
  public function getSearchHistory( Collection $collection )
  {
    $global_history = $this->get( 'search_history' );

    if ( isset( $global_history[ $collection->getName() ] ) ) {
      return $global_history[ $collection->getName() ];
    }
    else {
      return array();
    }
  }

  /**
   * Sets the Collection's search history
   *
   * @param Collection $collection
   * @param array $history
   */
  protected function setSearchHistory( Collection $collection, array $history )
  {
    $global_history = $this->get( 'search_history' );
    $global_history[ $collection->getName() ] = $history;
    $this->set( 'search_history', $global_history );
  }

  /**
   * Records a new search, and returns an array of the new history
   *
   * @param Query $query
   * @return array
   */
  public function recordSearch( Query $query )
  {
    $collection = $query->getCollection();
    $params     = $query->getParams();
    $history    = $this->getSearchHistory( $collection );

    $params = $this->filterParams( $params );

    if ( ! $params ) {
      return $history;
    }
    
    $already_there = array_search( $params, $history );
    
    if ( $already_there !== false ) {
      if ( $already_there != count( $history ) - 1 ) {
        // move this search to the top
        array_splice( $history, $already_there, 1 );
        array_push( $history, $params );
      }

      $this->setSearchHistory( $collection, $history );
      return;
    }

    array_push( $history, $params );

    $max_searches = $collection->getConfig( 'search_history_length', 5 );

    while ( count( $history ) > $max_searches ) {
      array_shift( $history );
    }

    $this->setSearchHistory( $collection, $history );

    return $history;
  }
  
  /**
   * Returns the relevant parameters
   *
   * @return array
   */
  public function getRelevantParams()
  {
    return $this->relevantParams;
  }
  
  /**
   * Sets the relevant query parameters
   *
   * @param array $keys An array of relevant keys
   */
  public function setRelevantParams( array $keys )
  {
    $this->relevantParams = $keys;
  }

  /**
   * Returns an array of query parameters with irrelevant ones filtered out
   *
   * @param array $params An array of raw parameters
   * @return array
   */
  protected function filterParams( array $params )
  {
    foreach( $params as $key => $value ) {
      if ( ! in_array( $key, $this->relevantParams ) ) {
        unset( $params[ $key ] );
      }
    }

    return $params;
  }
}
