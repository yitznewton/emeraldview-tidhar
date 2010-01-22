<?php

class HitsPage
{
  protected $searchHandler;
  protected $hits;
  protected $perPage;
  protected $pageNumber;
  protected $totalPages;

  public function __construct( SearchHandler $search_handler, $page_number, $per_page = 20 )
  {
    $this->searchHandler = $search_handler;

    if ( ! is_int( $page_number ) ) {
      throw new InvalidArgumentException( 'Second argument must be an integer' );
    }

    if ( ! is_int( $per_page ) ) {
      throw new InvalidArgumentException( 'Third argument must be an integer' );
    }
    
    $this->perPage = $per_page;

    $this->hits = $search_handler->execute();
    $this->totalPages = ceil( count( $this->hits ) / $per_page );

    if ( $this->totalPages >= $page_number ) {
      $this->pageNumber = $page_number;
    }
    else {
      $this->pageNumber = 1;
    }

    var_dump($this->links());exit;
  }

  public function hits()
  {
    $first_hit = $this->$pageNumber * $this->perPage;

    return array_slice( $this->hits, $first_hit, $this->perPage );
  }

  public function links()
  {
    $links = new stdClass();
    $params = $this->searchHandler->getParams();

    if ( $this->pageNumber > 1 ) {
      $params['p'] = 1;
      $links->first = $this->searchHandler->getCollection()->getUrl()
                      . '/search?' . http_build_query( $params );

      $params['p'] = $this->pageNumber - 1;
      $links->previous = $this->searchHandler->getCollection()->getUrl()
                         . '/search?' . http_build_query( $params );
    }

    if ( $this->pageNumber < $this->totalPages ) {
      $params['p'] = $this->pageNumber + 1;
      $links->next = $this->searchHandler->getCollection()->getUrl()
                      . '/search?' . http_build_query( $params );

      $params['p'] = $this->totalPages;
      $links->last = $this->searchHandler->getCollection()->getUrl()
                      . '/search?' . http_build_query( $params );
    }
    
    $links->pages = array();
    
    for ( $i = 1; $i < $this->pageNumber; $i++ )
    {
      $params['p'] = $i;
      $links->pages[ $i ] = $this->searchHandler->getCollection()->getUrl()
                          . '/search?' . http_build_query( $params );
    }

    for ( $i = $this->pageNumber + 1; $i < $this->totalPages + 1; $i++ )
    {
      $params['p'] = $i;
      $links->pages[ $i ] = $this->searchHandler->getCollection()->getUrl()
                          . '/search?' . http_build_query( $params );
    }
    
    return $links;
  }
}