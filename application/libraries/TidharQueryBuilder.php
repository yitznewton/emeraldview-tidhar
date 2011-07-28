<?php

abstract class TidharQueryBuilder extends QueryBuilder
{
  public static function factory( array $params, Collection $collection )
  {
    $indexes = array_keys( $collection->getIndexes() );

    if ( array_key_exists( 'i', $params ) && in_array( $params['i'], $indexes ) ) {
      return new QueryBuilder_Fielded( $params, $collection );
    }
    elseif (array_key_exists( 'q1', $params )) {
      return new QueryBuilder_Boolean( $params, $collection );
    }
    elseif (array_key_exists( 'q', $params )) {
      return new QueryBuilder_Simple( $params, $collection );
    }
    elseif (array_key_exists( 'roman-name', $params )) {
      return new TidharQueryBuilder_Roman( $params, $collection );
    }
    else {
      return false;
    }
  }
}
