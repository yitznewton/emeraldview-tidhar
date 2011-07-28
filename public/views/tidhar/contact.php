<h1><?php echo L10n::_('Contact Us') ?></h1>

<?php if ( $submitted && empty( $errors ) ): ?>

<p>Thank you for your message! We will respond as soon as possible.</p>

<?php else: ?>

<p>We hope to enable user comments in the future; please hold additions
and corrections to articles until then. Regarding translations of the Encyclopedia, we
are unable to provide this service, and are not aware of any efforts to
translate this work. You may find <a href="http://translate.google.com/">Google
Translate</a> to be of use.</p>

<form method="POST" id="contact-form">
  <div class="clear">
    <label for="contact-email">Your email*</label>
    <div class="left">
      <?php if ( isset( $errors['email'] ) ): ?>
        <div class="contact-errors">
          <?php echo $errors['email'] ?>
        </div>
      <?php endif; ?>
      <input type="text" id="contact-email" name="email" value="<?php echo isset($email)?$email:'' ?>" />
    </div>
  </div>
  <div class="clear">
    <label for="contact-name">Your name</label>
    <div class="left">
      <?php if ( isset( $errors['name'] ) ): ?>
        <div class="contact-errors">
          <?php echo $errors['name'] ?>
        </div>
      <?php endif; ?>
      <input type="text" id="contact-name" name="name" value="<?php echo isset($name)?$name:'' ?>" />
    </div>
  </div>
  <div class="clear">
    <label for="contact-institution">Institutional affiliation</label>
    <div class="left">
      <?php if ( isset( $errors['institution'] ) ): ?>
        <div class="contact-errors">
          <?php echo $errors['institution'] ?>
        </div>
      <?php endif; ?>
      <input type="text" id="contact-institution" name="institution" value="<?php echo isset($institution)?$institution:'' ?>" />
    </div>
  </div>
  <div class="clear">
    <label for="contact-position">Position</label>
    <div class="left">
      <?php if ( isset( $errors['position'] ) ): ?>
        <div class="contact-errors">
          <?php echo $errors['position'] ?>
        </div>
      <?php endif; ?>
      <input type="text" id="contact-position" name="position" value="<?php echo isset($position)?$position:'' ?>" />
    </div>
  </div>
  <div class="clear">
    <label for="contact-message">Message</label>
    <div class="left">
      <?php if ( isset( $errors['message'] ) ): ?>
        <div class="contact-errors">
          <?php echo $errors['message'] ?>
        </div>
      <?php endif; ?>
      <textarea id="contact-message" name="message"><?php echo isset($message)?$message:'' ?></textarea>
    </div>
  </div>
  <div class="clear">
    <label for="contact-cc">Send me a copy</label>
    <input type="checkbox" id="contact-cc" name="cc" />
  </div>
  <div class="clear">
    <label for="contact-publicize">Allow Touro to share this on its websites</label>
    <input type="checkbox" id="contact-publicize" name="publicize" />
  </div>
  <div id="contact-captcha-container" class="clear">
    <label for="contact-captcha">Type the text</label>
    <?php if ( isset( $errors['captcha'] ) ): ?>
      <div class="contact-errors">
        <?php echo $errors['captcha'] ?>
      </div>
    <?php endif; ?>
    <div>
      <?php $captcha = new Captcha(); echo $captcha->render() ?>
    </div>
    <div>
      <input type="text" id="contact-captcha" name="captcha" />
    </div>
  </div>
  <div class="clear">
    <input type="submit" id="contact-submit" value="Submit" />
  </div>
</form>

<?php endif; ?>
