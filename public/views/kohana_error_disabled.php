<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">

<html>

<head>
<title>Error | EmeraldView</title>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

<link rel="stylesheet" type="text/css"
href="<?php echo url::base() ?>views/default/css/reset.css">

<link rel="stylesheet" type="text/css" media="screen"
href="<?php echo url::base() ?>libraries/treeview/jquery.treeview.css">

<link rel="stylesheet" type="text/css"
href="<?php echo url::base() ?>views/default/css/style.css">

<link rel="stylesheet" type="text/css" media="print"
href="<?php echo url::base() ?>views/default/css/style-print.css">

<script type="text/javascript"
src="<?php echo url::base() ?>libraries/jquery.js"></script>

<script type="text/javascript"
src="<?php echo url::base() ?>libraries/treeview/jquery.treeview.js"></script>

<script type="text/javascript"
src="<?php echo url::base() ?>views/default/js/default.js"></script>

</head>

<?php if ( L10n::isLoaded() ): ?>
<body dir="<?php echo L10n::_('ltr') ?>">
<?php else: ?>
<body>
<?php endif; ?>

<div id="header">
    <span id="header-logo">
      <a href="<?php echo url::base() ?>">
        <img src="<?php echo url::base() ?>images/emeraldview2.png"
        alt="EmeraldView logo" />
      </a>
    </span>
    <div id="header-emeraldview-title">
      <div>EmeraldView</div>
    </div>
</div>

  <div id="main-content">
    <h1>Server error</h1>
    <p>We're sorry; EmeraldView encountered an error in attempting to fulfill
      your request. You may want to try again from the
      <a href="<?php echo url::base() ?>">home page</a>.
    </p>
  </div>

<div id="footer">
  <div id="footer-url">URL: <?php echo url::site( url::current( true ) ) ?></div>

  <div>Copyright &copy; <?php echo date('Y') ?>

  <?php
  if (
    EmeraldviewConfig::get('institution_name')
    && EmeraldviewConfig::get('institution_url')
  ) {
    $inst_name = EmeraldviewConfig::get('institution_name');
    $inst_url  = EmeraldviewConfig::get('institution_url');

    echo 'by ';
    echo html::anchor( $inst_url, $inst_name );
  }
  elseif (EmeraldviewConfig::get('institution_name')) {
    echo 'by ' . EmeraldviewConfig::get('institution_name') ;
  }
  ?>

  </div>

  <div>Powered by
    <a href="http://www.greenstone.org/">Greenstone</a> and
    <a href="http://bitbucket.org/yitznewton/emeraldview">EmeraldView</a>
  </div>

  <?php if ( ! IN_PRODUCTION ): ?>
  <div>
    Rendered in {execution_time} seconds, using {memory_usage} of memory
  </div>
  <?php endif; ?>
</div>

</body>
</html>
