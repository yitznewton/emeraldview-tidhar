<?php

abstract class Query_Tidhar extends Query
{
  protected function  __construct( Collection $collection, array $params ) {
    parent::__construct($collection, $params);

    $to_check = array( 'q', 'q1', 'q2', 'q3', 'roman-name' );

    foreach ( $to_check as $key ) {
      if ( ! isset( $this->params[ $key ] ) ) {
        continue;
      }

      $pattern  = '/ (?<=\pL) " (?=\pL) /ux';
      $this->params[ $key ]
        = preg_replace( $pattern, '\\\\\\0', $this->params[ $key ] );
    }
  }

  public function getDisplayQuery() {
    $v = parent::getDisplayQuery();

    return str_replace( '\\"', '"', $v );
  }

  public static function factory( Collection $collection, array $params )
  {
    $indexes = array_keys( $collection->getIndexes() );

    if (
      array_key_exists( 'i', $params )
      && in_array( $params['i'], $indexes )
      && array_key_exists( 'q', $params )
      && $params['q']
    ) {
      return new Query_Tidhar_Fielded( $collection, $params );
    }
    elseif ( array_key_exists( 'q1', $params ) && $params['q1'] ) {
      return new Query_Tidhar_Boolean( $collection, $params );
    }
    elseif (
      array_key_exists( 'q', $params )
      && $params['q']
      && ! array_key_exists( 'i', $params )
    ) {
      return new Query_Tidhar_Simple( $collection, $params );
    }
    elseif (
      array_key_exists( 'roman-name', $params )
      && $params['roman-name']
    ) {
      return new Query_Tidhar_Roman( $collection, $params );
    }
    else {
      return false;
    }
  }
}
