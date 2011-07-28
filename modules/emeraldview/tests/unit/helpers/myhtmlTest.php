<?php
class myhtmlTest extends PHPUnit_Framework_TestCase
{
  public function testIframe_pdf()
  {
    $this->assertInternalType( 'string',
      myhtml::iframe_pdf( 'someurl', array(), array() ) );
    $this->assertInternalType( 'string',
      myhtml::iframe_pdf( 'someurl', array('class' => 'foo'), array() ) );
    $this->assertInternalType( 'string',
      myhtml::iframe_pdf( 'someurl', array(), array('search' => 'foo bar') ) );
    $this->assertNotRegExp( '/search=/',
      myhtml::iframe_pdf( 'someurl', array(), array('view' => 'Fit') ) );
    $this->assertRegExp( '/search=/',
      myhtml::iframe_pdf( 'someurl', array(), array('search' => 'foo bar') ) );
  }
}
