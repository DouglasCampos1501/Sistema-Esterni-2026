INSERT INTO ui_strings (string_key, group_name, description, sort_order) VALUES
('noticias.page_title', 'noticias', 'Título da página /noticias/', 1),
('noticias.read_more', 'noticias', 'Botão "Leia mais" no card de notícia', 2),
('noticias.more_posts', 'noticias', 'Título "Mais publicações" na página de notícia individual', 3)
ON DUPLICATE KEY UPDATE description = VALUES(description);

INSERT INTO ui_string_translations (ui_string_id, language_code, value)
SELECT id, 'pt-BR', v FROM ui_strings JOIN (
  SELECT 'noticias.page_title' k, 'Notícias' v UNION ALL
  SELECT 'noticias.read_more', 'Leia mais' UNION ALL
  SELECT 'noticias.more_posts', 'Mais publicações'
) x ON x.k = ui_strings.string_key ON DUPLICATE KEY UPDATE value = VALUES(value);

INSERT INTO ui_string_translations (ui_string_id, language_code, value)
SELECT id, 'en', v FROM ui_strings JOIN (
  SELECT 'noticias.page_title' k, 'News' v UNION ALL
  SELECT 'noticias.read_more', 'Read more' UNION ALL
  SELECT 'noticias.more_posts', 'More posts'
) x ON x.k = ui_strings.string_key ON DUPLICATE KEY UPDATE value = VALUES(value);

INSERT INTO ui_string_translations (ui_string_id, language_code, value)
SELECT id, 'es', v FROM ui_strings JOIN (
  SELECT 'noticias.page_title' k, 'Noticias' v UNION ALL
  SELECT 'noticias.read_more', 'Leer más' UNION ALL
  SELECT 'noticias.more_posts', 'Más publicaciones'
) x ON x.k = ui_strings.string_key ON DUPLICATE KEY UPDATE value = VALUES(value);

INSERT INTO ui_string_translations (ui_string_id, language_code, value)
SELECT id, 'it', v FROM ui_strings JOIN (
  SELECT 'noticias.page_title' k, 'Notizie' v UNION ALL
  SELECT 'noticias.read_more', 'Leggi di più' UNION ALL
  SELECT 'noticias.more_posts', 'Altri articoli'
) x ON x.k = ui_strings.string_key ON DUPLICATE KEY UPDATE value = VALUES(value);
