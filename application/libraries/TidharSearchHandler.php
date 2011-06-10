<?php

class TidharSearchHandler extends SearchHandler_Solr
{
  public function execute()
  {
    $querystring = $this->query->getDisplayQuery();

    $solr_params = array(
      'q'       => $querystring,
      'hl'      => 'on',
      'hl.fl'   => '*',
      'qf'      => 'text EX^2.5',
      'wt'      => 'xslt',
      'tr'      => 'emeraldview.xsl',
      'start'   => $start_at-1,
      'rows'    => $per_page,
      'defType' => 'dismax',
    );

    $host = $this->getCollection()->getConfig( 'solr_host' );

    if ( ! $host ) {
      $msg = 'No Solr host specified in config for collection '
             . $this->getCollection()->getGreenstoneName();

      throw new Exception( $msg );
    }

    $query_url = 'http://' . $host . '/select/?'
                 . http_build_query( $solr_params );

    $xml = @file_get_contents( $query_url );

    if ( ! $xml ) {
      throw new Exception( 'Unexpected or no response from Solr' );
    }

    $data = new SimpleXMLElement( $xml );

    $attributes          = $data->attributes();
    $this->totalHitCount = (int) $attributes['numFound'];

    $hits = array();

    foreach ( $data->children() as $child ) {
      $hits[] = new Hit_Solr( $this, $child );
    }

    return $hits;
  }

  public static function factory( Query $query )
  {
    return new TidharSearchHandler( $query );
  }
}
