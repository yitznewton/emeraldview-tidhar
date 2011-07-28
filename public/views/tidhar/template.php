<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
  "http://www.w3.org/TR/html4/loose.dtd">

<html>

  <head>
    <title><?php echo $page_title ?></title>

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="author" content="David Tidhar">
    <meta name="author" lang="he" dir="rtl" content="דוד תדהר">
    <meta name="author" content="Touro College Libraries">
    <meta name="description" content="A biographical dictionary or encyclopedia of 19th and 20th century Israelis">
    <meta name="keywords" content="israel, biographies, jews, hebrew, history, jewish, genealogy">

    <link rel="stylesheet" type="text/css" href="/views/tidhar/css/aesthete.css">
    <link rel="stylesheet" type="text/css" href="/views/tidhar/css/keyboard.css">
    <?php echo $css_includes ?>
    <?php echo $js_includes ?>
    <script type="text/javascript" src="/views/tidhar/js/jquery.cookie.js"></script>

  </head>

  <body dir="<?php echo L10n::_('ltr') ?>">

    <div id="page">
      <div id="wrapper">
        <div id="header">
          <a href="/" class="headerimg">
            <img src="/views/tidhar/images/banner.jpg" border="0" class="headerimg" alt="encyclopedia banner"/>
          </a>
          <div class="headertitle">
            <a href="/">
              <img src="/views/tidhar/images/banner_title.png"
                   alt="Encyclopedia of the Founders and Builders of Israel" />
            </a>
          </div>
             
         <div class="clear"></div>

        </div>


        <div id="whitepage">
          <div id="whitepagesidebg">
            <div id="whitepagetopbg">
              <div id="whitepagebottombg">
                <div id="navdiv">
                  <ul id="nav">
                    <li class="page_item">
                      <a href="/" title="Home"><?php echo L10n::_('Home') ?></a>
                    </li>

                    <li class="page_item">
                      <a href="#"><?php echo L10n::_('Browse') ?></a>
                      <ul>
                        <li class="page_item">
                          <a href="<?php echo $collection->getUrl() ?>/browse/entries" title="By entry"><?php echo L10n::vsprintf('By %s', array('entry'), true) ?></a>
                        </li>
                        <li class="page_item">
                          <a href="<?php echo $collection->getUrl() ?>/browse/volume" title="By volume"><?php echo L10n::vsprintf('By %s', array('volume'), true) ?></a>
                        </li>
                      </ul>
                    </li>

                    <li class="page_item">
                      <a href="<?php echo $collection->getUrl() ?>/contact"><?php echo L10n::_('Contact') ?></a>
                    </li>

                    <li class="page_item" id="touro-link">
                      A project of <a href="http://www.tourolib.org/">Touro College Libraries</a>
                    </li>
                  </ul>
                  <div class="clear"></div>
                </div>


                <!-- end header --><div id="leftcontent">

                  <?php echo $content ?>
                </div>
                <div id="sidebar">
                  <div class="widget widget_categories">
                    <div class="ornament random-entries">
                      <h3><?php echo L10n::_('Random entries') ?></h3>
                      <?php echo tidhar::random_entries( $collection, 3 ) ?>
                    </div>
                  </div>
                  <div class="widget widget_categories">
                    <div class="share-discovery">
                      Did you find something remarkable?
                      <a href="/tidhar/contact">Share your discovery</a>
                      with us!

                      <?php echo tidhar::discoveries( $collection ) ?>
                    </div>
                  </div>

                </div>
                <div class="clear"></div>
                <div id="footer">
                  <div id="footer-url">URL: <?php echo url::site( url::current( true ) ) ?></div>

                  <div><?php echo L10n::vsprintf( 'Copyright %s', array( date('Y') ) ) ?></div>

                  <?php if ( ! IN_PRODUCTION ): ?>
                  <div>
                    Rendered in {execution_time} seconds, using {memory_usage} of memory
                  </div>
                  <?php endif; ?>
                </div>
              </div> <!-- "whitepagebottombg -->
            </div>

          </div>
        </div>
      </div>
    </div>

    <?php if ( $language != 'he' ): ?>
    <div id="keyboard-container">
      <div id="keyboard">
        <a class="disable-keyboard-link" title="Disable keyboard (you will be able to re-enable it)"></a>
        <a class="key row1 col2 wide-2 backspace" title="backspace"></a>
        <a class="key row1 col3 alef" title="&#1488;"></a>
        <a class="key row1 col4 bet" title="&#1489;"></a>
        <a class="key row1 col5 gimel" title="&#1490;"></a>
        <a class="key row1 col6 dalet" title="&#1491;"></a>
        <a class="key row1 col7 he" title="&#1492;"></a>
        <a class="key row1 col8 vav" title="&#1493;"></a>
        <a class="key row1 col9 zayin" title="&#1494;"></a>
        <a class="key row2 col1 het" title="&#1495;"></a>
        <a class="key row2 col2 tet" title="&#1496;"></a>
        <a class="key row2 col3 yod" title="&#1497;"></a>
        <a class="key row2 col4 khaf" title="&#1499;"></a>
        <a class="key row2 col5 khaf-sofit" title="&#1498;"></a>
        <a class="key row2 col6 lamed" title="&#1500;"></a>
        <a class="key row2 col7 mem" title="&#1502;"></a>
        <a class="key row2 col8 mem-sofit" title="&#1501;"></a>
        <a class="key row2 col9 nun" title="&#1504;"></a>
        <a class="key row3 col1 nun-sofit" title="&#1503;"></a>
        <a class="key row3 col2 samekh" title="&#1505;"></a>
        <a class="key row3 col3 ayin" title="&#1506;"></a>
        <a class="key row3 col4 pe" title="&#1508;"></a>
        <a class="key row3 col5 pe-sofit" title="&#1507;"></a>
        <a class="key row3 col6 tsadi" title="&#1510;"></a>
        <a class="key row3 col7 tsadi-sofit" title="&#1509;"></a>
        <a class="key row3 col8 kuf" title="&#1511;"></a>
        <a class="key row3 col9 resh" title="&#1512;"></a>
        <a class="key row4 col1 shin" title="&#1513;"></a>
        <a class="key row4 col2 tav" title="&#1514;"></a>
        <a class="key row4 col6 spacebar wide-4" title=" "></a>
        <a class="key row4 col7 hyphen" title="-"></a>
        <a class="key row4 col8 quote" title="'"></a>
        <a class="key row4 col9 apostrophe" title="'"></a>
      </div>
    </div>
    <?php endif; ?>

    <?php @include 'analytics.inc' ?>
  </body>
</html>
