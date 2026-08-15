jQuery(function ($) {
  var $notice = $('#cookie-notice');
  if (!$notice.length) return;

  if (!document.cookie.includes('esterni_cookies_accepted=1')) {
    $notice.removeClass('cookie-notice-hidden');
  }

  $('#cn-accept-cookie').on('click', function () {
    document.cookie = 'esterni_cookies_accepted=1; path=/; max-age=' + (60 * 60 * 24 * 365);
    $notice.addClass('cookie-notice-hidden');
  });
});
