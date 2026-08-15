jQuery(document).ready(function ($) {
  $(document).foundation();

  // Remove empty paragraphs left by the editor inside accordions/orbit
  $('.accordion p:empty, .orbit p:empty').remove();

  // Wrap YouTube/Vimeo iframes for responsive embedding
  $('iframe[src*="youtube.com"], iframe[src*="vimeo.com"]').each(function () {
    var $this = $(this);
    if ($this.innerWidth() / $this.innerHeight() > 1.5) {
      $this.wrap("<div class='widescreen responsive-embed'/>");
    } else {
      $this.wrap("<div class='responsive-embed'/>");
    }
  });

  // Sliders — inicializa qualquer elemento com data-slick (hero, carrossel de linhas etc.),
  // igual ao tema original: cada bloco carrega sua própria config no atributo.
  if ($.fn.slick) {
    $('[data-slick]').each(function () {
      var $el = $(this);
      if (!$el.hasClass('slick-initialized')) {
        $el.slick($el.data('slick'));
      }
    });
  }

  // Language switcher dropdown (new — not present on the original site)
  $('.lang-switcher').on('click', function (e) {
    e.stopPropagation();
    $(this).toggleClass('is-open');
  });
  $(document).on('click', function () {
    $('.lang-switcher').removeClass('is-open');
  });
});
