<?php
declare(strict_types=1);
// Espera que header-public.php já tenha rodado ($lang, current_language() etc. disponíveis).
$lang = $lang ?? current_language();
?>
<div class="block newsletter">
<div class="grid-container">
<div class="grid-x grid-padding-x align-middle">
<div class="small-12 large-shrink cell">
<strong class="upper small-text"><?= e(t('newsletter.title')) ?></strong>
</div>
<div class="small-12 large-auto cell">
<form action="/newsletter.php" method="post" class="newsletter-form">
<div class="grid-x grid-padding-x">
<div class="small-12 medium-4 cell">
<input type="text" name="name" placeholder="<?= e(t('newsletter.name_placeholder')) ?>" required>
</div>
<div class="small-12 medium-4 cell">
<input type="email" name="email" placeholder="<?= e(t('newsletter.placeholder')) ?>" required>
</div>
<div class="small-12 medium-4 cell">
<button class="button small hollow expanded" type="submit"><?= e(t('newsletter.submit')) ?></button>
</div>
</div>
<div class="grid-x grid-padding-x">
<div class="small-12 cell">
<label class="newsletter-consent">
<input type="checkbox" name="consent" required>
<span><?= t('newsletter.consent') ?></span>
</label>
</div>
</div>
</form>
</div>
</div>
</div>
</div>

<div class="block footer">
<div class="small-text">
<div class="grid-container">
<div class="grid-x grid-padding-x align-center align-middle medium-text-center large-text-left">
<div class="large-4 medium-shrink small-12 cell text-center">
<div style="width:70vw; max-width: 17rem; margin: 0 auto;">
<img src="/assets/public/img/logo-esterni-v.png" alt="Esterni Design e Mobiliário Urbano">
</div>
<div class="spacer1"></div>
</div>
</div>
<div class="grid-x grid-padding-x align-center align-top medium-text-center large-text-left">
<div class="large-4 medium-4 small-12 cell">
<div class="left-column">
<div class="footer-padding-box text-center">
<div><strong class="upper"><?= e(t('footer.contacts')) ?>:</strong></div>
<div class="spacer05"></div>
<div><a href="tel:(41) 3195-4348"><i class="fab icon-phone-circled"></i> (41) 3195-4348 <span class="smaller-text"> (Grupo Technomast)</span></a></div>
<div class="spacer05"></div>
<div><a href="mailto:comercial@esterni.ind.br"><i class="fab icon-email"></i> comercial@esterni.ind.br</a></div>
<div class="spacer05"></div>
<div class="text-center">
<a href="https://wa.me/5541995967801?text=Ol%C3%A1%2C+gostaria+de+falar+com+um+consultor+da+Esterni." target="_blank" rel="noopener" class="button hollow tiny"><i class="fab fa-whatsapp"></i> WhatsApp</a>
</div>
<div><a href="<?= e(home_url()) ?>contato/" class="button hollow tiny"><?= e(t('footer.contact_site')) ?></a></div>
</div>
</div>
<div class="spacer2 hide-for-medium"></div>
</div>
<div class="large-4 medium-4 small-12 cell text-center">
<div class="footer-padding-box">
<strong class="upper"><?= e(t('footer.address')) ?>:</strong>
<div class="spacer05"></div>
<div><i class="fab icon-location-2"></i> GRUPO TECHNOMAST<br>
Rod. PR 423, KM 24.3, Jardim das Acácias<br>
Campo Largo, Paraná, Brasil<br>
CEP: 83603-000</div>
<div><a href="https://goo.gl/maps/Dg7wqdM2CdxDr4cq8" target="_blank" rel="noopener" class="button hollow tiny"><?= e(t('footer.view_map')) ?></a></div>
</div>
<div class="spacer2 hide-for-medium"></div>
</div>
<div class="large-4 medium-4 small-12 cell text-center">
<div class="right-column">
<div class="footer-padding-box">
<strong class="upper"><?= e(t('footer.social')) ?>:</strong>
<div class="spacer05"></div>
<ul class="menu vertical social-icons align-center">
<li><a href="https://www.instagram.com/esterni_design/" target="_blank" rel="noopener"><i class="fab fa-instagram"></i> Instagram</a></li>
<li><a href="https://www.youtube.com/channel/UC9bHyin4rQilgr68zh_5uug" target="_blank" rel="noopener"><i class="fab fa-youtube"></i> YouTube</a></li>
<li><a href="https://www.linkedin.com/company/esterni-mobiliario-urbano" target="_blank" rel="noopener"><i class="fab fa-linkedin"></i> LinkedIn</a></li>
</ul>
</div>
</div>
<div class="spacer2 hide-for-medium"></div>
</div>
<div class="large-12 cell text-center text group-links">
<div class="footer-padding-box">
<strong class="upper"><?= e(t('footer.group_companies')) ?>:</strong>
<div class="spacer05"></div>
<div class="grid-x grid-padding-x align-center">
<div class="shrink cell"><a href="https://technomast.com.br/" target="_blank" rel="noopener"><img src="/assets/public/img/grupo-technomast.png" alt="Grupo Technomast"></a></div>
<div class="shrink cell"><a href="https://technofibra.com.br/" target="_blank" rel="noopener"><img src="/assets/public/img/technofibra.png" alt="Technofibra"></a></div>
</div>
</div>
</div>
<div class="large-12 cell text-center text">
<div class="spacer2"></div>
<div style="margin: 0 auto;"><hr></div>
<div class="spacer2"></div>
<div>
<p style="font-size: .95em; max-width: 90%; margin: 0 auto .5rem auto; font-weight:400;"><?= t('footer.privacy_notice') ?></p>
<p class="developer"><?= e(t('footer.developed_by')) ?></p>
</div>
</div>
</div>
</div>
</div>
</div>

<div id="cookie-notice" role="dialog" class="cookie-notice-hidden" aria-label="Cookie Notice">
<div class="cookie-notice-container">
<span class="cn-text-container"><?= e(t('cookie.notice')) ?></span>
<span class="cn-buttons-container">
<button id="cn-accept-cookie" class="cn-set-cookie cn-button"><?= e(t('cookie.accept')) ?></button>
</span>
</div>
</div>

<?php require __DIR__ . '/whatsapp-floater.php'; ?>

<script src="/assets/vendor/jquery.min.js"></script>
<script src="/assets/vendor/jquery-migrate.min.js"></script>
<script src="/assets/vendor/jquery.fancybox.min.js"></script>
<script src="/assets/vendor/foundation.js"></script>
<script src="/assets/vendor/twentytwenty.js"></script>
<script src="/assets/vendor/slick.min.js"></script>
<script src="/assets/vendor/parallax.js"></script>
<script src="/assets/public/js/site.js"></script>
<script src="/assets/public/js/cookie-notice.js"></script>
</body>
</html>
