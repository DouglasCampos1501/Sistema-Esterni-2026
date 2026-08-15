// Componente genérico de abas de idioma, usado em qualquer tela do admin
// que tenha blocos com a classe .i18n-tabs (Textos do Site, Páginas, Produtos, Notícias...).
document.addEventListener('click', function (e) {
    var btn = e.target.closest('.i18n-tab-btn');
    if (!btn) return;
    e.preventDefault();

    var wrap = btn.closest('.i18n-tabs');
    wrap.querySelectorAll('.i18n-tab-btn').forEach(function (b) {
        b.classList.toggle('active', b === btn);
    });

    var lang = btn.dataset.lang;
    wrap.querySelectorAll('.i18n-tab-panel').forEach(function (p) {
        p.hidden = p.dataset.lang !== lang;
    });
});

// Marca com uma bolinha verde as abas de idioma que já têm algum campo preenchido,
// para o admin bater o olho e saber onde falta traduzir sem precisar clicar em cada uma.
function markFilledLanguageTabs() {
    document.querySelectorAll('.i18n-tabs').forEach(function (wrap) {
        wrap.querySelectorAll('.i18n-tab-panel').forEach(function (panel) {
            var lang = panel.dataset.lang;
            var hasValue = Array.from(panel.querySelectorAll('input, textarea')).some(function (field) {
                return field.value.trim() !== '';
            });
            var btn = wrap.querySelector('.i18n-tab-btn[data-lang="' + lang + '"]');
            if (btn) btn.classList.toggle('has-content', hasValue);
        });
    });
}

document.addEventListener('DOMContentLoaded', markFilledLanguageTabs);
