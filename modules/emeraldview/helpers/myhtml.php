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
 * @package helpers
 */
/**
 * myhtml_Core provides several functions for HTML generation
 *
 * @package helpers
 * @copyright  Copyright (c) 2010 Benjamin Schaffer (http://yitznewton.org/)
 * @license    http://yitznewton.org/emeraldview/index.php?title=License     New BSD License
 */
class myhtml {
  /**
   * Returns specified HTML enclosed by HTML tags as specified by $tag, with
   * specified aattributes
   *
   * @param string $tag
   * @param string $contents The HTML to be contained within this element
   * @param array $attributes An associative array of attribute keys and values
   * @return string
   */
  public static function element(
    $tag, $contents, array $attributes = array()
  )
  {
    $attribute_string = '';

    foreach ($attributes as $key => $value) {
      if ($value !== null) {
        $attribute_string .= ' ' . $key . '="' . $value . '"';
      }
    }

    $element  = '<' . $tag . $attribute_string;

    if ($contents === null) {
      $element .= " />\n";
    }
    else {
      $element .= ">\n" . $contents . "</$tag>\n";
    }

    return $element;
  }

  /**
   * Returns an HTML <select> element including <option> elements corresponding
   * to specified options, and with specified attributes
   *
   * @param array $options An associative array containing keys and values of the options
   * @param array $attributes
   * @param string $default The key of the option to set as selected
   * @return string
   */
  public static function select_element(
    array $options, array $attributes = array(), $default = null
  )
  {
    $option_string = '';

    foreach ( $options as $key => $name ) {
      $option_attributes = array(
        'value' => $key,
      );

      if ( $key === $default ) {
        $option_attributes['selected'] = 'selected';
      }

      $option_string .= myhtml::element('option', $name, $option_attributes);
    }

    return myhtml::element('select', $option_string, $attributes);
  }

  /**
   * Returns the URL for the current page, adding a query parameter for
   * interface language
   *
   * @param string $language_code The language to request
   * @return string
   */
  public static function language_url( $language_code )
  {
    if ( ! is_string( $language_code ) ) {
      throw new InvalidArgumentException( 'Argument must be a string' );
    }

    $url = url::site( url::current( true ) );
    $parsed_url = parse_url( $url );

    $query = isset( $parsed_url[ 'query' ] ) ? $parsed_url[ 'query' ] : '';

    if ( ! $query ) {
      return $url . '?language=' . $language_code;
    }
    elseif( preg_match( '/&?language=/', $query ) ) {
      $ptn = '/(&?language=)[^&]*/';
      return preg_replace( $ptn, '\\1'.$language_code, $url );
    }
    else {
      return $url . '&language=' . $language_code;
    }
  }

  /**
   * Returns an HTML <select> element corresponding to given interface languages
   *
   * @param array $languages An associative array of language keys and values
   * @param string $default The key of the language to set as selected
   * @return string
   */
  public static function language_select( array $languages, $default = null )
  {
    if ( count( $languages ) < 2 ) {
      return '';
    }

    $options = array();

    foreach ($languages as $l) {
      $language_name = EmeraldviewConfig::get("languages.$l");

      if ( $language_name ) {
        $options[ $l ] = L10n::_( $language_name );
      }
    }

    $select_attr = array(
      'name' => 'language',
      'id'   => 'language-select-select',
    );

    $select_element = myhtml::select_element(
      $options, $select_attr, $default
    );

    $submit_attr = array(
      'id'    => 'language-select-submit',
      'type'  => 'submit',
      'value' => L10n::_('Submit'),
    );

    $submit_element = myhtml::element('input', null, $submit_attr);

    $form_attr = array(
      'id'     => 'language-select-form',
      'action' => '',
    );

    $form_element = myhtml::element(
      'form', $select_element . $submit_element, $form_attr
    );

    $div_element = myhtml::element(
       'div', $form_element, array('id' => 'language-select')
    );

    return $div_element;
  }

  /**
   * Returns an HTML <iframe> element requesting a PDF with given options
   *
   * @param string $url The URL of the PDF
   * @param array string[] $attributes HTML attributes for the iframe
   * @param array string[] $pdf_options Parameters to pass to the PDF reader
   * @return string
   */
  public static function iframe_pdf(
    $url, $attributes = array(), $pdf_options = array()
  ) {
    $default_pdf_options = array(
      'view'      => 'Fit',
      'scrollbar' => '0',
      'navpanes'  => '0',
    );

    $pdf_options = array_merge( $default_pdf_options, $pdf_options );

    if ( isset( $pdf_options['search'] ) && ! $pdf_options['search'] ) {
      unset( $pdf_options['search'] );
    }

    // we can't use http_build_query() because we need to preserve spaces
    // in #search, and not replace them with '+'
    $pdf_options_string = '';

    foreach ( $pdf_options as $key => $value ) {
      $pdf_options_string .= "&$key=$value";
    }

    if ( $pdf_options_string ) {
      // trim extra '&'
      $pdf_options_string = '#' . substr( $pdf_options_string, 1 );
    }

    $attributes['src'] = $url . $pdf_options_string;

    $iframe_element = myhtml::element(
      'iframe', '', $attributes
    );

    return $iframe_element;
  }
}
