-- Textos fixos do site público (menu, rodapé, newsletter, cookies) — pt-BR + EN/ES/IT.
-- pt-BR é o idioma "base": gravamos como tradução também (t() sempre lê de ui_string_translations,
-- não existe fallback pra coluna própria em ui_strings — só a string_key crua em último caso).

INSERT INTO ui_strings (string_key, group_name, description, sort_order) VALUES
('menu.home', 'menu', 'Menu principal — Início', 1),
('menu.about', 'menu', 'Menu principal — A Esterni', 2),
('menu.lines', 'menu', 'Menu principal — Linhas', 3),
('menu.products', 'menu', 'Menu principal — Produtos', 4),
('menu.news', 'menu', 'Menu principal — Notícias', 5),
('menu.contact', 'menu', 'Menu principal — Contato', 6),
('menu.group', 'menu', 'Menu principal — Grupo Technomast', 7),
('menu.language', 'menu', 'Menu principal — rótulo do seletor de idiomas', 8),
('newsletter.title', 'footer', 'Newsletter — título', 1),
('newsletter.placeholder', 'footer', 'Newsletter — placeholder do campo de e-mail', 2),
('newsletter.submit', 'footer', 'Newsletter — botão de envio', 3),
('footer.contacts', 'footer', 'Rodapé — título coluna de contatos', 4),
('footer.contact_site', 'footer', 'Rodapé — botão "Contato - site"', 5),
('footer.address', 'footer', 'Rodapé — título coluna de endereço', 6),
('footer.view_map', 'footer', 'Rodapé — botão "Ver no Google Maps"', 7),
('footer.social', 'footer', 'Rodapé — título coluna de redes sociais', 8),
('footer.group_companies', 'footer', 'Rodapé — título "Empresas do Grupo"', 9),
('footer.privacy_notice', 'footer', 'Rodapé — aviso de política de privacidade (com link)', 10),
('footer.developed_by', 'footer', 'Rodapé — crédito de desenvolvimento', 11),
('cookie.notice', 'cookie', 'Aviso de cookies — texto', 1),
('cookie.accept', 'cookie', 'Aviso de cookies — botão aceitar', 2);

INSERT INTO ui_string_translations (ui_string_id, language_code, value)
SELECT id, 'pt-BR', v FROM ui_strings JOIN (
  SELECT 'menu.home' k, 'Início' v UNION ALL
  SELECT 'menu.about', 'A Esterni' UNION ALL
  SELECT 'menu.lines', 'Linhas' UNION ALL
  SELECT 'menu.products', 'Produtos' UNION ALL
  SELECT 'menu.news', 'Notícias' UNION ALL
  SELECT 'menu.contact', 'Contato' UNION ALL
  SELECT 'menu.group', 'Grupo Technomast' UNION ALL
  SELECT 'menu.language', 'Idioma' UNION ALL
  SELECT 'newsletter.title', 'Assine nossa newsletter e fique por dentro das novidades' UNION ALL
  SELECT 'newsletter.placeholder', 'Seu melhor e-mail' UNION ALL
  SELECT 'newsletter.submit', 'Enviar' UNION ALL
  SELECT 'footer.contacts', 'Contatos' UNION ALL
  SELECT 'footer.contact_site', 'Contato - site' UNION ALL
  SELECT 'footer.address', 'Endereço' UNION ALL
  SELECT 'footer.view_map', 'Ver no Google Maps' UNION ALL
  SELECT 'footer.social', 'Confira as Redes' UNION ALL
  SELECT 'footer.group_companies', 'Empresas do Grupo' UNION ALL
  SELECT 'footer.privacy_notice', 'Ao navegar em nosso website você concorda com nossa <a href="/politica-de-privacidade/">Política de Privacidade</a>.' UNION ALL
  SELECT 'footer.developed_by', 'Desenvolvido por Esterni' UNION ALL
  SELECT 'cookie.notice', 'Nós utilizamos cookies para garantir que você tenha a melhor experiência em nosso site. Se você continua a usar este site, assumimos que você está satisfeito.' UNION ALL
  SELECT 'cookie.accept', 'Aceito'
) x ON x.k = ui_strings.string_key;

INSERT INTO ui_string_translations (ui_string_id, language_code, value)
SELECT id, 'en', v FROM ui_strings JOIN (
  SELECT 'menu.home' k, 'Home' v UNION ALL
  SELECT 'menu.about', 'About Esterni' UNION ALL
  SELECT 'menu.lines', 'Lines' UNION ALL
  SELECT 'menu.products', 'Products' UNION ALL
  SELECT 'menu.news', 'News' UNION ALL
  SELECT 'menu.contact', 'Contact' UNION ALL
  SELECT 'menu.group', 'Technomast Group' UNION ALL
  SELECT 'menu.language', 'Language' UNION ALL
  SELECT 'newsletter.title', 'Subscribe to our newsletter and stay up to date' UNION ALL
  SELECT 'newsletter.placeholder', 'Your best email' UNION ALL
  SELECT 'newsletter.submit', 'Send' UNION ALL
  SELECT 'footer.contacts', 'Contact' UNION ALL
  SELECT 'footer.contact_site', 'Contact - site' UNION ALL
  SELECT 'footer.address', 'Address' UNION ALL
  SELECT 'footer.view_map', 'View on Google Maps' UNION ALL
  SELECT 'footer.social', 'Follow Us' UNION ALL
  SELECT 'footer.group_companies', 'Group Companies' UNION ALL
  SELECT 'footer.privacy_notice', 'By browsing our website you agree to our <a href="/en/politica-de-privacidade/">Privacy Policy</a>.' UNION ALL
  SELECT 'footer.developed_by', 'Developed by Esterni' UNION ALL
  SELECT 'cookie.notice', 'We use cookies to ensure you get the best experience on our website. If you continue to use this site, we assume you are happy with it.' UNION ALL
  SELECT 'cookie.accept', 'Accept'
) x ON x.k = ui_strings.string_key;

INSERT INTO ui_string_translations (ui_string_id, language_code, value)
SELECT id, 'es', v FROM ui_strings JOIN (
  SELECT 'menu.home' k, 'Inicio' v UNION ALL
  SELECT 'menu.about', 'Sobre Esterni' UNION ALL
  SELECT 'menu.lines', 'Líneas' UNION ALL
  SELECT 'menu.products', 'Productos' UNION ALL
  SELECT 'menu.news', 'Noticias' UNION ALL
  SELECT 'menu.contact', 'Contacto' UNION ALL
  SELECT 'menu.group', 'Grupo Technomast' UNION ALL
  SELECT 'menu.language', 'Idioma' UNION ALL
  SELECT 'newsletter.title', 'Suscríbete a nuestro boletín y mantente al día' UNION ALL
  SELECT 'newsletter.placeholder', 'Tu mejor correo electrónico' UNION ALL
  SELECT 'newsletter.submit', 'Enviar' UNION ALL
  SELECT 'footer.contacts', 'Contacto' UNION ALL
  SELECT 'footer.contact_site', 'Contacto - sitio' UNION ALL
  SELECT 'footer.address', 'Dirección' UNION ALL
  SELECT 'footer.view_map', 'Ver en Google Maps' UNION ALL
  SELECT 'footer.social', 'Síguenos' UNION ALL
  SELECT 'footer.group_companies', 'Empresas del Grupo' UNION ALL
  SELECT 'footer.privacy_notice', 'Al navegar por nuestro sitio web, aceptas nuestra <a href="/es/politica-de-privacidade/">Política de Privacidad</a>.' UNION ALL
  SELECT 'footer.developed_by', 'Desarrollado por Esterni' UNION ALL
  SELECT 'cookie.notice', 'Utilizamos cookies para garantizar que tengas la mejor experiencia en nuestro sitio web. Si continúas usando este sitio, asumimos que estás de acuerdo.' UNION ALL
  SELECT 'cookie.accept', 'Aceptar'
) x ON x.k = ui_strings.string_key;

INSERT INTO ui_string_translations (ui_string_id, language_code, value)
SELECT id, 'it', v FROM ui_strings JOIN (
  SELECT 'menu.home' k, 'Home' v UNION ALL
  SELECT 'menu.about', 'Chi è Esterni' UNION ALL
  SELECT 'menu.lines', 'Linee' UNION ALL
  SELECT 'menu.products', 'Prodotti' UNION ALL
  SELECT 'menu.news', 'Notizie' UNION ALL
  SELECT 'menu.contact', 'Contatto' UNION ALL
  SELECT 'menu.group', 'Gruppo Technomast' UNION ALL
  SELECT 'menu.language', 'Lingua' UNION ALL
  SELECT 'newsletter.title', 'Iscriviti alla nostra newsletter e resta aggiornato' UNION ALL
  SELECT 'newsletter.placeholder', 'La tua migliore email' UNION ALL
  SELECT 'newsletter.submit', 'Invia' UNION ALL
  SELECT 'footer.contacts', 'Contatti' UNION ALL
  SELECT 'footer.contact_site', 'Contatto - sito' UNION ALL
  SELECT 'footer.address', 'Indirizzo' UNION ALL
  SELECT 'footer.view_map', 'Vedi su Google Maps' UNION ALL
  SELECT 'footer.social', 'Seguici' UNION ALL
  SELECT 'footer.group_companies', 'Aziende del Gruppo' UNION ALL
  SELECT 'footer.privacy_notice', 'Navigando nel nostro sito web accetti la nostra <a href="/it/politica-de-privacidade/">Informativa sulla Privacy</a>.' UNION ALL
  SELECT 'footer.developed_by', 'Sviluppato da Esterni' UNION ALL
  SELECT 'cookie.notice', 'Utilizziamo i cookie per garantirti la migliore esperienza sul nostro sito web. Se continui a utilizzare questo sito, assumiamo che tu sia soddisfatto.' UNION ALL
  SELECT 'cookie.accept', 'Accetto'
) x ON x.k = ui_strings.string_key;
