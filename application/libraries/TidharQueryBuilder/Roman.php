<?php

class TidharQueryBuilder_Roman extends TidharQueryBuilder
{
  protected $hebrewNames;

  public function getEnglishName()
  {
    return $this->params['roman-name'];
  }

  /**
   * @return Zend_Search_Lucene_Search_Query
   */
  public function getQuery()
  {
    if ($this->query) {
      return $this->query;
    }

    if ( ! $this->getHebrewNames() ) {
      return $this->query = false;
    }

    $querystring = implode( ' OR ', $this->hebrewNames );

    // run it against TX at regular boost, plus title with extra boost
    $query = Zend_Search_Lucene_Search_QueryParser::parse( $querystring );

    return $this->query = $query;
  }

  public function getDisplayQuery()
  {
    return $this->params['roman-name'] . ' ['
           . implode( ' OR ', $this->getHebrewNames() ) . ']';
  }

  public function getRawTerms()
  {
    return $this->getHebrewNames();
  }
  
  protected function getHebrewNames()
  {
    if ( ! isset( $this->hebrewNames ) ) {
      $hebrew_translit   = new HebrewTranslitString( $this->params['roman-name'] );
      $this->hebrewNames = $hebrew_translit->toHebrew();
    }

    return $this->hebrewNames;
  }
}
