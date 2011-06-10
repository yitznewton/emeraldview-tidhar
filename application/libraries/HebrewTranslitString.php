<?php

// adapted from Stephen P. Morse - steve@stevemorse.org
class HebrewTranslitString
{
  const ALEF = 'א'; // d7 90
  const BAIS = 'ב'; // d7 91
  const GIMEL = 'ג'; // d7 92
  const DALET = 'ד'; // d7 93
  const HAY = 'ה'; // d7 94
  const VAV = 'ו'; // d7 95
  const ZAYIN = 'ז'; // d7 96
  const KHESS = 'ח'; // d7 97
  const TESS = 'ט'; // d7 98
  const YUD = 'י'; // d7 99
  const KAF = 'כ'; // d7 9b
  const KAF_SOFIT = 'ך'; // d7 9a
  const LAMED = 'ל'; // d7 9c
  const MEM = 'מ'; // d7 9e
  const MEM_SOFIT = 'ם'; // d7 9d
  const NUN = 'נ'; // d7 a0
  const NUN_SOFIT = 'ן'; // d7 9f
  const SAMEKH = 'ס'; // d7 a1
  const AYIN = 'ע'; // d7 a2
  const PAY = 'פ'; // d7 a4
  const FAY_SOFIT = 'ף'; // d7 a3
  const TSADI = 'צ'; // d7 a6
  const TSADI_SOFIT = 'ץ'; // d7 a5
  const KUF = 'ק'; // d7 a7
  const RAISH = 'ר'; // d7 a8
  const SHIN = 'ש'; // d7 a9
  const TAF = 'ת'; // d7 aa
  const BLANK = ' ';
  
  protected $inputString;
  
  public function __construct( $text )
  {
    $this->inputString = strtoupper( $text );
  }
      
  protected function isAlphaNumeric($c)
  {
    return ($c >= '0' && $c <= '9') || ($c >= 'A' && $c <= 'Z') || ($c >= 'a' && $c <= 'z');
  }

  protected function isAtStart($i)
  {
    return (($i === 0) || !$this->isAlphaNumeric($this->inputString[$i-1]));
  }

  protected function isAtEnd($i)
  {
    $ch = substr( $this->inputString, $i, 1 );
    
    while( $i < strlen( $this->inputString ) - 1 ) {
      $rv = substr( $this->inputString, $i+1, 1 );
      if ( $ch != $rv ) {
        break;
      }
      $i++;
    }
    return (($i == strlen( $this->inputString ) - 1 ) || !$this->isAlphaNumeric(substr( $this->inputString, $i+1, 1 )));
  }

  protected function isFollowedBy( $i, $to_test )
  {
    if ( ! is_array( $to_test ) ) {
      $to_test = array( $to_test );
    }

    $total_length = strlen( $this->inputString );
    
    foreach ( $to_test as $current ) {
      $test_length  = strlen( $current );

      if ( $total_length < $i + $test_length + 1 ) {
        continue;
      }

      if ( substr( $this->inputString, $i+1, $test_length ) === $current ) {
        return $current;
      }
    }

    return false;
  }

  protected function isPrecededBy( $i, $to_test )
  {
    if ( ! is_array( $to_test ) ) {
      $to_test = array( $to_test );
    }

    $total_length = strlen( $this->inputString );

    foreach ( $to_test as $current ) {
      $test_length  = strlen( $current );

      if ( $i - $test_length < 0 ) {
        continue;
      }

      if ( substr( $this->inputString, $i-1, $test_length ) === $current ) {
        return $current;
      }
    }

    return false;
  }

  protected function trueNextChar($i)
  {
    if ($i >= strlen( $this->inputString )) {
      return "";
    }
    return substr( $this->inputString, $i+1, 1 );
  }

  public function toHebrew()
  {
    $hebrewLetters = array();
    $hebrewText    = array('');

    for ( $i = 0; $i < strlen( $this->inputString ); $i++ ) {
      $english_letter = substr( $this->inputString, $i, 1 );

      if ( ! ord( $english_letter ) ) {
        // not ASCII
        continue;
      }
      
      if ( $this->isPrecededBy( $i, $english_letter ) ) {
        // repeated letter
        continue;
      }

      $possibilities = $this->getDictionaryPossibilities( $i );
      
      if ( ! $possibilities ) {
        $possibilities = $this->getHebrewLetter( $i );
        $hebrewLetters[] = $possibilities;
      }

      $letterCount = count( $possibilities );
      $wordCount   = count( $hebrewText );

      for ( $letterIndex = 1; $letterIndex < $letterCount; $letterIndex++ ) {
        for ( $wordIndex = 0; $wordIndex < $wordCount; $wordIndex++ ) {
          $hebrewText[ $letterIndex*$wordCount + $wordIndex] = $hebrewText[$wordIndex];
        }
      }
      for ( $letterIndex = 0; $letterIndex < $letterCount; $letterIndex++ ) {
        for ( $wordIndex = 0; $wordIndex < $wordCount; $wordIndex++ ) {
          $hebrewText[ $letterIndex*$wordCount + $wordIndex] .= $possibilities[ $letterIndex ];
        }
      }
    }

    // clean up
    $yuds_ptn = '/' . self::YUD . '{3,}/u';
    $vavs_ptn = '/' . self::VAV . '{3,}/u';

    for ( $x = 0; $x < count( $hebrewText ); $x++ ) {
      $hebrewText[ $x ] = preg_replace( $yuds_ptn, self::YUD . self::YUD, $hebrewText[ $x ] );
      $hebrewText[ $x ] = preg_replace( $vavs_ptn, self::VAV . self::VAV, $hebrewText[ $x ] );
      $hebrewText[ $x ] = $this->replaceSofits( $hebrewText[ $x ] );
    }

    $hebrewText = array_unique( $hebrewText );

    return $hebrewText;
  }

  /**
   *
   * @param integer $i
   * @return array|boolean
   */
  protected function getHebrewLetter( &$i )
  {
    $english_letter = substr( $this->inputString, $i, 1 );

    switch ( $english_letter ) {
      case 'A':
        if ( $this->isFollowedBy($i, array('E','I')) ) {
          if ($this->isAtStart($i)) {
            $i++;
            return array( self::ALEF . self::YUD . self::YUD );
          } else {
            $i++;
            return array( self::YUD . self::YUD, '' );
          }
        }
        elseif ( $this->isFollowedBy($i, array('Y')) ) {
          if ($this->isAtStart($i)) {
            $i++;
            return array( self::ALEF . self::YUD . self::YUD );
          } else {
            $i++;
            return array( self::YUD . self::YUD );
          }
        }
        elseif ( $this->isFollowedBy($i, array('U')) ) {
          $i++;
          return array( self::ALEF .self::VAV, self::VAV . self::YUD );
        } elseif ($this->isAtEnd($i)) {
          return array( self::ALEF, self::HAY, self::YUD . self::YUD );
        } elseif ($this->isAtStart($i)) {
          return array( self::ALEF, self::AYIN );
        } else {
          return array( self::YUD . self::YUD, '' );
        }
            
      case 'B':
        return array( self::BAIS );
              
      case 'C':
        if (!$this->isAtEnd($i)) {
          if ( $this->isFollowedBy($i, array('E','I','Y')) ) {
            $i++;
            return array( self::SAMEKH, self::SHIN );
          } elseif ($this->isFollowedBy( $i, 'HH' )) {
            $i += 2;
            return array( self::TSADI . self::HAY, self::KAF . self::HAY, self::KHESS . self::HAY );
          } elseif ($this->isFollowedBy( $i, 'H' )) {
            $i++;
            return array( self::TSADI, self::TSADI . '\'', self::KAF, self::KHESS );
          } else {
            if ($this->isFollowedBy( $i, 'K' )) {
              $i++;
            }
            return array( self::KUF, self::KAF );
          }
        } else {
          return array( self::KUF );
        }
              
      case 'D':
        return array( self::DALET );
          
      case 'E':
        if (!$this->isAtEnd($i) && $this->isFollowedBy( $i, array('Y', 'I') )) {
          $i++;
          if ($this->isAtStart($i)) {
            return array( self::ALEF . self::YUD . self::YUD );
          } else {
            return array( self::YUD . self::YUD, self::YUD );
          }
        }
        elseif ($this->isFollowedBy( $i, array('U') )) {
          $i++;
          return array( self::VAV . self::YUD, self::VAV );
        }
        elseif ($this->isAtStart($i)) {
          return array( self::ALEF );
        } elseif (!$this->isAtEnd($i) && $this->trueNextChar($i) == 'E') {
          $i++;
          return array( self::YUD . self::YUD, self::YUD );
        } else {
          return false;
        }
            
      case 'F':
        if ( $this->isAtEnd($i) ) {
          return array( self::FAY_SOFIT );
        }
        return array( self::PAY );
              
      case 'G':
        return array( self::GIMEL );
              
      case 'H':
        return array( self::HAY );
          
      case 'I':
        if (!$this->isAtEnd($i) && ($this->isFollowedBy( $i, 'E' ))) {
          $i++;
          if ($this->isAtStart($i)) {
            return array( self::ALEF . self::YUD . self::YUD );
          } else {
            return array( self::YUD . self::YUD, self::YUD );
          }
        } else {
          return array( self::YUD, '' );
        }
              
      case 'J':
        return array( self::GIMEL . '\'', self::YUD, self::ZAYIN . '\'' );
              
      case 'K':
        if ( $this->isFollowedBy($i, 'AHAN') ) {
          $i += 4;
          return array( self::KAF . self::HAY . self::NUN, self::KUF . self::HAY . self::NUN );
        }
        if (!$this->isAtEnd($i) && $this->isFollowedBy( $i, 'H' )) {
          $i++;
          return array( self::KAF, self::KHESS );
        } elseif ( $this->isAtEnd($i) ) {
          return array( self::KUF );
        }
        else {
          return array( self::KUF, self::KAF );
        }
              
      case 'L':
        return array( self::LAMED );
              
      case 'M':
        return array( self::MEM );
              
      case 'N':
        return array( self::NUN );
              
      case 'O':
        $letter = '';
        
        if ($this->isAtStart($i)) {
          $letter .= self::ALEF . self::VAV;
        } else {
          $letter .= self::VAV . '|';
        }

        if (!$this->isAtEnd($i) && ($this->isFollowedBy( $i, array('I','Y') ))) {
          $i++;
          $letter .= self::YUD;
        }
        
        return explode( '|', $letter );
              
      case 'P':
        if ( $this->isAtEnd($i+1) && $this->isFollowedBy( $i, 'H' ) ) {
          $i++;
          return array( self::FAY_SOFIT );
        }
        elseif ( $this->isAtEnd($i) ) {
          return array( self::PAY, self::FAY_SOFIT );
        }
        return array( self::PAY );
              
      case 'Q':
        return array( self::KUF );
          
      case 'R':
        if ( $this->isFollowedBy($i, 'OSEN') ) {
          $i += 4;
          return array( self::RAISH . self::VAV . self::ZAYIN . self::NUN );
        }
        return array( self::RAISH );
          
      case 'S':
        $letter = '';
        
        if (!$this->isAtEnd($i)) {
          if ($this->trueNextChar($i) == 'S') {
            $letter .= self::SHIN;
            $i++;
          } elseif ($this->isFollowedBy( $i, 'CH' )) {
            $letter .= self::SHIN;
            $i += 2;
          } elseif ($this->isFollowedBy( $i, array('Z','H') )) {
            $letter .= self::SHIN;
            $i++;
          } elseif ($this->isFollowedBy( $i, 'T' )) {
            $letter .= self::SHIN . self::TESS;
            $i++;
          } else {
            $letter .= self::SAMEKH . '|' . self::SHIN . '|' . self::ZAYIN . '|' . self::TAF;
          }
        } else {
          $letter .= self::SAMEKH . '|' . self::SHIN . '|' . self::ZAYIN . '|' . self::TAF;
        }
        
        return explode( '|', $letter );
              
      case 'T':
        if (!$this->isAtEnd($i) && $this->isFollowedBy( $i, array('S','Z') )) {
          $i++;
          return array( self::TSADI );
        } elseif (!$this->isAtEnd($i+1) && $this->isFollowedBy( $i, 'CH' )) {
          // do nothing (TCH is same as CH)
          return false;
        } elseif ( $this->isFollowedBy($i, 'H')) {
          $i++;
          return array( self::TESS, self::TAF, self::TESS . self::HAY, self::TAF. self::HAY );
        } else {
          return array( self::TESS, self::TAF );
        }
              
      case 'U':
        if ($this->isAtStart($i)) {
          return array( self::ALEF . self::VAV );
        } else {
          return array( self::VAV );
        }
              
      case 'V':
      case 'W':
        return array( self::VAV . self::VAV, self::VAV, self::BAIS );
              
      case 'X':
        return array( self::KUF . self::SAMEKH, self::ZAYIN );
              
      case 'Y':
        return array( self::YUD );
              
      case 'Z':
        if ( $this->isFollowedBy($i, 'H')) {
          $i++;
          return array( self::ZAYIN . '\'' );
        }

        return array( self::ZAYIN, self::TSADI );
              
      default:
        if ( ($english_letter >= '0' && $english_letter <= '9') || $english_letter == '-' ) {
          return array( $english_letter );
        }
        elseif ( $english_letter == '*' ) {
          return $english_letter;
        }
        else {
          return array( ' ' );
        }
    }
  }

  protected function getDictionaryPossibilities( &$i )
  {
    $english_letter = substr( $this->inputString, $i, 1 );

    $dictionary = array(
      'D' => array(
        'AVID' => array( self::DALET . self::VAV . self::DALET, self::DALET . self::VAV . self::YUD . self::DALET )
      ),
      'R' => array(
        'OSEN' => array( self::RAISH . self::VAV . self::ZAYIN . self::NUN )
      ),
      'T' => array(
        'IDHAR' => array( self::TAF . self::DALET . self::HAY . self::RAISH )
      ),
    );

    if ( ! isset( $dictionary[ $english_letter ] ) ) {
      return false;
    }

    $letter_entries = $dictionary[ $english_letter ];
    $input_length   = strlen( $this->inputString );

    foreach ( $letter_entries as $english => $hebrew ) {
      $english_length = strlen( $english );

      if (
        $input_length > $i + $english_length
        && substr( $this->inputString, $i+1, $english_length ) == $english
      ) {
        $i += $english_length;
        return $hebrew;
      }
    }

    return false;
  }

  protected function getSofit( $letter )
  {
    switch ( $letter ) {
      case self::KAF:
        return self::KAF_SOFIT;
        
      case self::MEM:
        return self::MEM_SOFIT;
        
      case self::NUN:
        return self::NUN_SOFIT;

//      // PAY needs special treatment because of 'p' at the end of words
//      case self::PAY:
//        return self::FAY_SOFIT;
        
      case self::TSADI:
        return self::TSADI_SOFIT;
        
      default:
        return false;
    }
  }

  protected function replaceSofits( $text )
  {
    // change last character of each word to the sofit form
    // remember, Hebrew chars are 2 bytes; see http://www.phpwact.org/php/i18n/utf-8

    $to_check_ptn = '/ (?<= \\p{L} ) [ \\p{L} \\p{N}] (?= [ \ - ] | $ )/ux';

    preg_match_all( $to_check_ptn, $text, $matches, PREG_OFFSET_CAPTURE );

    foreach ( $matches[0] as $match ) {
      $untouched = $text;
      $letter    = $match[0];
      $pos       = $match[1];

      if ( $sofit = $this->getSofit( $letter ) ) {
        $text = substr( $untouched, 0, $pos ) . $sofit;

        if ( $pos != strlen( $untouched ) - 2 ) {
          $text .= substr( $untouched, $pos + 2 );
        }
      }
    }

    return $text;
  }
}
