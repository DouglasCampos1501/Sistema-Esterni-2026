INSERT INTO ui_strings (string_key, group_name, description, sort_order) VALUES
('error.not_found_title', 'geral', 'Título da página <title> quando o conteúdo não é encontrado (404)', 1)
ON DUPLICATE KEY UPDATE description = VALUES(description);

INSERT INTO ui_string_translations (ui_string_id, language_code, value)
SELECT id, 'pt-BR', 'Página não encontrada — Esterni' FROM ui_strings WHERE string_key='error.not_found_title'
ON DUPLICATE KEY UPDATE value = VALUES(value);
INSERT INTO ui_string_translations (ui_string_id, language_code, value)
SELECT id, 'en', 'Page not found — Esterni' FROM ui_strings WHERE string_key='error.not_found_title'
ON DUPLICATE KEY UPDATE value = VALUES(value);
INSERT INTO ui_string_translations (ui_string_id, language_code, value)
SELECT id, 'es', 'Página no encontrada — Esterni' FROM ui_strings WHERE string_key='error.not_found_title'
ON DUPLICATE KEY UPDATE value = VALUES(value);
INSERT INTO ui_string_translations (ui_string_id, language_code, value)
SELECT id, 'it', 'Pagina non trovata — Esterni' FROM ui_strings WHERE string_key='error.not_found_title'
ON DUPLICATE KEY UPDATE value = VALUES(value);
