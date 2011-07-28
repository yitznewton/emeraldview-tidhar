<script type="text/javascript">
  simple_default_text = '<?php echo L10n::_('Hebrew characters only here...') ?>';
  roman_default_text  = '<?php echo L10n::_('...or enter name in English here') ?>';

  $(document).ready( function() {
    chooseSearchForm( 'search-form-simple' );
    $('#search-form-link-simple').addClass('active');
  });
</script>

<div id="about-search-outer-container">

<div id="about-search-container">
  <h2><?php echo L10n::_('Search') ?></h2>

  <?php echo tidhar::form_simple(  $collection ) ?>
  <?php echo tidhar::form_fielded( $collection ) ?>

  <?php echo tidhar::chooser() ?>

  <?php if ( $language != 'he' ): ?>
    <?php echo tidhar::roman_name_search( $collection ) ?>
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

  <form id="pager-form" method="get" action="/tidhar/view/1">
    <script type="text/javascript">
      doc_url = '/tidhar/view/1';
    </script>

    <?php printf(L10n::_('Go to page %s'), '<input type="text" class="input-text" name="page" />') ?>
    <input type="submit" value="<?php echo L10n::_('Go') ?>">
  </form>

</div>

<?php if ( ! empty( $search_history ) ): ?>

<div id="about-search-history-container">
  <h3><?php echo L10n::_('Recent searches') ?></h3>
  <?php echo tidhar::history( $collection, $search_history ) ?>
</div>

<?php endif; ?>

</div>

<div id="about-description">
  <!-- <h2><?php echo L10n::_('About David Tidhar') ?></h2> -->

  <p>The monumental 19-volume <em>Encyclopedia of the Founders and Builders of
  Israel</em> was compiled and published by David Tidhar (1897-1970) over the 23
  years from 1947 until his death. His varied careers as British policeman,
  <a href="/views/tidhar/images/tidhar_with_books.jpg">
  <img src="/views/tidhar/images/tidhar_with_books.jpg"
       alt="<?php echo L10n::_('David Tidhar with his Encyclopedia') ?>"
       title="<?php echo L10n::_('David Tidhar with his Encyclopedia') ?>"
       width="250" />
  </a>
  private detective, author, and communal leader made Tidhar ideally suited
  to compile this Who’s Who of the Jewish community of the Land of Israel
  (<em>Erets-Yisrael</em> in Hebrew). In addition to original articles about
  political activists and other of his contemporaries,
  the first several volumes
  also contain material about 19th century settlers culled from local
  histories published in the preceding two decades.
  In each volume, Tidhar requested submission of biographical information
  and photographs from relatives of early settlers<a id="archives-note-ref" href="#archives-note">*</a>
  which he used to
  compile some 6,000 biographies. His work represents the only biographical
  source for many of those included. 
  Though Tidhar is famously scant in providing sources, his
  <em>Encyclopedia</em> is itself widely cited, having become the preeminent source
  in this area. With the dissemination of this online version, Touro College
  in conjunction with the Tidhar family is proud to make this classic
  reference work freely available to the public.</p>
  <p>Born Todrosovitz, David was already active in his youth in communal
  affairs, including driving out Christian missionaries set on ensnaring
  Jewish souls in <em>Erets-Yisrael</em>. Already by World War I, he had begun his
  long career of establishing organizationsI: one to provide clothing and
  shoes to the Jewish poor, and another, a sanitary corps to provide
  instruction on avoiding cholera during the 1916 epidemic. Tidhar
  volunteered in 1918 for the Jewish Legion, and was also among the
  defenders of Jaffa’s Jews during the 1921 Arab riots. He was an early
  member of the Haganah self-defense organization. He joined the British-run
  Palestine Police in 1921, where he served as the commanding officer in
  the New City of Jerusalem until 1925. Throughout the years he put his
  particular knowledge of Arab affairs and of the British Mandatory
  government at the disposal of the Jewish community and its institutions.
  Once, in 1924, Officer Tidhar entered the lion’s den of anti-Semitism by
  approaching the car of the blood-thirsty Mufti Amin al-Husayni, and
  demanded from him to stop the bloodshed immediately. He helped all who
  asked his assistance, which included aiding Jerusalem’s Orthodox Jewish
  community.</p>
  <p>Tidhar was not a member of any political party, was friends with all
  factions, and assisted everyone. He was actively involved with numerous
  organizations and was constantly at work. In 1924, he published (in
  Hebrew) <em>Criminals and Crimes in Erets-Yisrael</em> which was translated into
  <a href="/views/tidhar/images/tidhar_british_police.jpg">
  <img src="/views/tidhar/images/tidhar_british_police.jpg"
       alt="<?php echo L10n::_('David Tidhar as British policeman') ?>"
       title="<?php echo L10n::_('David Tidhar as British policeman') ?>"
       height="250" />
  </a>

  Arabic and English, and was the first such work written by a Jewish
  officer. Though this was to be the start of a prolific writing career, he
  continued to be active in numerous organizations throughout his life. In
  1926, he opened a private investigation bureau, the first of its kind in
  <em>Erets-Yisrael</em>. In the early 1930s, the newspaper reporter Shlomoh
  Ben-Yisrael featured Tidhar as the protagonist of his fictitious weekly
  detective novelettes. This
  effort is considered to be the progenitor of Hebrew detective literature.
  Tidhar based the stories on his experiences in the British constabulary.
  During World War II, he tried to destroy the Nazi fifth column among the
  Arabs of <em>Erets-Yisrael</em> and in neighboring countries by uncovering their
  weapon suppliers and reporting them to the British police. He did all of
  this and more without reaping any personal reward.</p>
  <p>With the success of his <em>Encyclopedia,</em> he turned to full-time writing
  and publishing in 1950. His other published works include: <em>Between Hammer
  and Anvil</em> (1932), a collection of articles; <em>In and Out of Uniform,</em>
  memoirs of his public activity from 1912 until 1937; <em>The Maccabi Album,</em>
  <em>Jaffa-Tel-Aviv</em> (1906-1956) and <em>In the Service of My Country,</em> containing
  memoirs, documents, and photographs from 1912-1960.</p>
  <p id="archives-note"><a href="#archives-note-ref">*</a> 
    His large collection of documents and photographs now resides at the
  <a href="http://www.jnul.huji.ac.il/heb/archives-db.html#T">Archives
  Department of the National Library of Israel</a> in Jerusalem as the
  David Tidhar archive, ARC. 4º 1489.</p>

  <div>
    <h2>Acknowledgments</h2>
    <p>The staff of Touro College Libraries and the Encyclopedia project team
    would like to extend our thanks to the many people who assisted in
    bringing this project to fruition, particularly Bezalel Tidhar and
    Esther-Rachel Weitz, the children of David Tidhar;
    Gal Almog, the son-in-law of Bezalel Tidhar;
    Benjamin Ronn; David Ronn and Dr. Jacqueline Maxin. Special thanks to
    <a href="http://stevemorse.org/">Stephen Morse</a>,
    who graciously allowed us to adapt his
    <a href="http://stevemorse.org/hebrew/eng2heb.html">English-to-Hebrew
    transliteration algorithm</a> for our site.</p>
  </div>

  <div id="about-banner-container">
    <h2><?php echo L10n::_('About our banner') ?></h2>
    <p class="about-banner"><a href="/views/tidhar/images/banner-large.jpg">
      <img src="/views/tidhar/images/banner.jpg" alt="encyclopedia banner"/></a></p>
    <p>Pictured in our site banner, from left to right:</p>
    <ul>
      <li><a href="/tidhar/view/1/294">Shlomoh Shtamfer</a></li>
      <li><a href="/tidhar/view/6/2715">Baron Edmond de
          Rothschild</a> (C) visiting Erets Yisrael, pictured with
          <a href="/tidhar/view/1/486">Avraham Shapira</a> (R)</li>
      <li><a href="/tidhar/view/1/37">Rabbi Haim-Hezkiyahu Medini</a></li>
      <li>The original Carmel Winery in Zikhron Yaakov in 1890s, founded by
        <a href="/tidhar/view/6/2715">
          Baron Edmond de Rothschild</a></li>
      <li><a href="/tidhar/view/1/195">Mosheh Shmuel Raab</a></li>
    </ul>
  </div>
</div>
