<?php

class Query_Tidhar_Roman extends Query_Tidhar
{
  protected $query;
  protected $hebrewNames;

  public function getEnglishName()
  {
    return $this->params['roman-name'];
  }

  /**
   * @return string
   */
  public function getQuerystring()
  {
    if ($this->query) {
      return $this->query;
    }

    if ( ! $this->getHebrewNames() ) {
      return $this->query = false;
    }

    return $this->query = '"' . implode( '" OR "', $this->hebrewNames ) . '"';
  }

  public function getDisplayQuery()
  {
    return $this->params['roman-name'];
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
