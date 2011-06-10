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
 * Highlighter adds HTML <span> tags around a supplied string based on
 * supplied search terms
 *
 * @package libraries
 * @copyright  Copyright (c) 2010 Benjamin Schaffer (http://yitznewton.org/)
 * @license    http://yitznewton.org/emeraldview/index.php?title=License     New BSD License
 */
class Highlighter_Text extends Highlighter
{
  /**
   * The supplied text
   *
   * @var string
   */
  protected $document;

  /**
   * @return string
   */
  public function execute()
  {
    $term_pattern  = implode( '|', $this->terms );
    
    // frame the term pattern with appropriate word boundaries
    $search = sprintf( Hit::HIT_PATTERN, $term_pattern );
    $replace = "<span class=\"highlight\">\\1</span>";

    return preg_replace( $search, $replace, $this->document );
  }

  /**
   * @return string
   */
  public function getDocument()
  {
    return $this->document;
  }

  /**
   * @param string $document
   */
  public function setDocument( $document )
  {
    if ( ! is_string( $document ) || $document === '' ) {
      throw new InvalidArgumentException( 'Argument must be a non-empty string' );
    }

    $this->document = $document;
  }
}
