<div id="document">

<h1><?php echo L10n::vsprintf( 'Volume %s', array( $root_node->getField('Volume') ), true ) ?></h1>

<?php if ($page->getCoverUrl()): ?>
<div id="cover-image">
  <img src="<?php echo $page->getCoverUrl() ?>" alt="cover image">
</div>
<?php endif; ?>


<?php // begin TOC section ?>
<?php if ($tree && ! $paged_urls): ?>
  <div id="toc">
    <div id="toc-header"><?php echo L10n::_('Table of contents') ?>
      <span class="toc-toggle" id="toc-hide" style="display:none">
        [<a href="#" onclick="return toggleTOC()"><?php echo L10n::_('hide') ?></a>]
      </span>
      <span class="toc-toggle" id="toc-show" style="display:none">
        [<a href="#" onclick="return toggleTOC()"><?php echo L10n::_('show') ?></a>]
      </span>
    </div>

    <div id="toc-container">
      <div id="tree-pager">
        <?php echo $tree_pager ?>
      </div>

      <?php echo $tree ?>

    </div>
  </div>
<?php endif; ?>

<?php if ($node->isPaged()): // begin PagedImage section ?>

<div id="image-pager">

<h2>
  <?php echo L10n::vsprintf( 'Page %s', array( $node->getField( 'Title' ) ), true ) ?>
</h2>

<form id="pager-form" method="get" action="<?php echo $root_page->getUrl() ?>">

<script type="text/javascript">
  doc_url = '<?php echo $root_page->getUrl() ?>';
</script>

<?php if ($paged_urls['previous']): ?>
  <span class="prev-button">
    <a href="<?php echo $paged_urls['previous'] ?>">
    <?php echo L10n::_('Previous') ?></a>
  </span>
<?php else: ?>
  <span class="prev-button inactive">
    <?php echo L10n::_('Previous') ?>
  </span>
<?php endif; ?>

<?php printf(L10n::_('Go to page %s'), '<input type="text" class="input-text" name="page">') ?>
<input type="submit" value="<?php echo L10n::_('Go') ?>">

<?php if ($paged_urls['next']): ?>
  <span class="next-button">
  <a href="<?php echo $paged_urls['next'] ?>">
    <?php echo L10n::_('Next') ?></a>
  </span>
<?php else: ?>
  <span class="next-button inactive">
  <?php echo L10n::_('Next') ?>
  </span>
<?php endif; ?>

</form>

<p><a href="http://translate.google.com/translate?u=<?php echo urlencode( $page->getUrl() ) ?>&hl=en&langpair=auto|en&tbb=1" target="_blank">Translate
this page using Google Translate</a></p>

</div>

<?php endif; // end PagedImage section ?>

<?php $source_url = $page->getSourceDocumentUrl() ?>

<?php if ( $source_url && $page->getScreenIconUrl() ): ?>
<div id="main-image">
  <a href="<?php echo $source_url ?>">
    <img src="<?php echo $page->getScreenIconUrl() ?>"
    alt="page image" />
  </a>
</div>

<?php elseif ($source_url): ?>
<div id="source-link">
  <a href="<?php echo $source_url ?>">
    Download original document
  </a>
</div>
<?php endif; ?>

<div id="body-text">
  <?php if (!$node->isPaged() && ( $node != $root_node )): ?>
    <h2><?php echo $node->getField( 'Title' ) ?></h2>
  <?php endif; ?>

  <?php echo $text ?>
</div>

<div class="clear"></div>

<?php if ($root_node != $node && $root_page->getDisplayMetadata()): ?>
<div class="metadata" dir="ltr">
  <h3><?php echo L10n::_('Document Metadata') ?></h3>

  <?php echo myview::metadata_list( $root_page->getDisplayMetadata() ) ?>

  <div class="clear"></div>
</div>
<?php endif; ?>

<?php if ($page->getDisplayMetadata()): ?>
<div class="metadata" dir="ltr">
  <?php if ($node == $root_node): ?>
  <h3><?php echo L10n::_('Document Metadata') ?></h3>
  <?php else: ?>
  <h3><?php echo L10n::_('Section Metadata') ?></h3>
  <?php endif; ?>

  <?php echo myview::metadata_list( $page->getDisplayMetadata() ) ?>

  <div class="clear"></div>
</div>
<?php endif; ?>

<h3>APA citation</h3>
<div id="apa-citation">
  <?php echo tidhar::citation( $page ) ?>
</div>

</div>
