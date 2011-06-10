<?php

abstract class TidharQuery extends Query
{
  public static function factory( Collection $collection, array $params )
  {
    $indexes = array_keys( $collection->getIndexes() );

    if ( array_key_exists( 'i', $params ) && in_array( $params['i'], $indexes ) ) {
      return new Query_Fielded( $collection, $params );
    }
    elseif (array_key_exists( 'q1', $params )) {
      return new Query_Boolean( $collection, $params );
    }
    elseif (array_key_exists( 'q', $params )) {
      return new Query_Simple( $collection, $params );
    }
    elseif (array_key_exists( 'roman-name', $params )) {
      return new TidharQuery_Roman( $collection, $params );
    }
    else {
      return false;
    }
  }
}
