<?php
if ( isset( $search_handler ) ) {
  switch ( get_class( $search_handler->getQuery() ) ) {
    case 'Query_Tidhar_Simple':
      $form_id = 'search-form-simple';
      $link_id = 'search-form-link-simple';

      break;
    case 'Query_Tidhar_Fielded':
      $form_id = 'search-form-fielded';
      $link_id = 'search-form-link-fielded';
      
      break;
    default:
      $form_id = 'search-form-simple';
  }
}
else {
  $form_id = 'search-form-simple';
}
?>

<script type="text/javascript">
  simple_default_text = '<?php echo L10n::_('Hebrew characters only here...') ?>';
  roman_default_text  = '<?php echo L10n::_('...or enter name in English here') ?>';

  $(document).ready( function() {
    chooseSearchForm( "<?php echo $form_id ?>" );
    
    $('#<?php echo isset($link_id) ? $link_id : 'search-form-link-simple' ?>').addClass('active');
  });
</script>

<div id="main-content">

<div id="about-search-outer-container">

<div id="search-search-container">
  <h2><?php echo L10n::_('Search') ?></h2>

  <?php echo tidhar::form_simple(  $collection, $search_handler ) ?>
  <?php echo tidhar::form_fielded( $collection, $search_handler ) ?>
  
  <?php echo tidhar::chooser() ?>

  <?php if ( $language != 'he' ): ?>
    <?php echo tidhar::roman_name_search( $collection, $search_handler ) ?>
  <?php endif; ?>

  <div class="search-tips-show-link search-tips-link">
    <a href="/tidhar/search/tips"><?php echo L10n::_('Tips on using search') ?></a>
  </div>

  <div class="search-tips-hide-link search-tips-link" style="display:none">
    <a href="#"><?php echo L10n::_('Hide search tips') ?></a>
  </div>

  <ul class="search-tips" style="display:none">
    <li>Wildcards * and ? are supported in the Hebrew search forms, but not
    in the English form.</li>
    <li>The English name search works best with European surnames and place
    names.</li>
  </ul>
</div>

<?php if ( ! empty( $search_history ) ): ?>
  <div id="about-search-history-container">
    <h3><?php echo L10n::_('Recent searches') ?></h3>
    <?php echo tidhar::history( $collection, $search_history ) ?>
  </div>
<?php endif; ?>

</div>

<div id="search-results-container">
  <div id="search-results-count">
    <?php echo tidhar::result_summary( $hits_page, $search_handler ) ?>
  </div>

  <?php if ($hits_page->hits): ?>
  <ol id="search-hits" start="<?php echo $hits_page->firstHit ?>">
    <?php foreach ($hits_page->hits as $hit): ?>
      <li>
        <div><?php echo $hit->link ?></div>

        <?php if ($hit->snippet): ?>
          <div class="search-snippet"><?php echo $hit->snippet ?></div>
        <?php endif; ?>
      </li>
    <?php endforeach; ?>
  </ol>

  <?php echo search::pager( $hits_page, $collection ) ?>
  
  <?php endif; ?>
</div>

</div>
