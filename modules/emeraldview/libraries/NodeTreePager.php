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
 * NodeTreePager generates a string of HTML <a> elements linking the previous
 * and next 
 *
 * @package libraries
 * @copyright  Copyright (c) 2010 Benjamin Schaffer (http://yitznewton.org/)
 * @license    http://yitznewton.org/emeraldview/index.php?title=License     New BSD License
 */
class NodeTreePager
{
  /**
   * Returns links or link placeholders corresponding to the previous and next
   * Nodes in a hierarchical document
   *
   * @param Collection $collection
   * @param Node_Document $node
   * @return string
   */
  public static function html( Collection $collection, Node_Document $node )
  {
    $output = '';
    
    $prev_node = $node->getPreviousNode();
    $next_node = $node->getNextNode();

    if ($prev_node) {
      $prev_url = NodePage::factory( $collection, $prev_node )->getUrl();
      $output .= myhtml::element(
        'a', L10n::_('Previous page'), array('href' => $prev_url)
      );
    }
    else {
      $output .= myhtml::element(
        'span', L10n::_('Previous page'), array('class' => 'inactive')
      );
    }

    if ($next_node) {
      $next_url = NodePage::factory( $collection, $next_node )->getUrl();
      $output .= myhtml::element(
        'a', L10n::_('Next page'), array('href' => $next_url)
      );
    }
    else {
      $output .= myhtml::element(
        'span', L10n::_('Next page'), array('class' => 'inactive')
      );
    }

    return $output;
  }
}
