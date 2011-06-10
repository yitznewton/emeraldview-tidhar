<?php
class SearchHandler_Tidhar extends SearchHandler_Solr
{
  protected function getSolrParams()
  {
    $params = parent::getSolrParams();

    $qt = $this->query->getCollection()->getConfig( 'solr_simple_qt' );

    if (
      $this->query instanceof Query_Simple
      && $qt
      && strpos( $this->query->getQuerystring(), '\'' ) === false
      && strpos( $this->query->getQuerystring(), '"' ) === false
    ) {
      $params['qt'] = $qt;
    }
    else {
      unset( $params['qt'] );
    }

    return $params;
  }

  public static function factory( Query $query )
  {
    return new self( $query );
  }
}