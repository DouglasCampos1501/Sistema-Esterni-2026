-- Meta description dedicada da página de Política de Privacidade (estava com um
-- fallback hardcoded em português em politica-de-privacidade/index.php).

INSERT INTO ui_strings (string_key, group_name, sort_order) VALUES
('privacy.meta_description', 'privacy', 3)
ON DUPLICATE KEY UPDATE string_key = VALUES(string_key);

INSERT INTO ui_string_translations (ui_string_id, language_code, value)
SELECT id, 'pt-BR', 'Conheça a Política de Privacidade da Esterni.' FROM ui_strings WHERE string_key = 'privacy.meta_description'
ON DUPLICATE KEY UPDATE value = VALUES(value);

INSERT INTO ui_string_translations (ui_string_id, language_code, value)
SELECT id, 'en', 'Read Esterni''s Privacy Policy.' FROM ui_strings WHERE string_key = 'privacy.meta_description'
ON DUPLICATE KEY UPDATE value = VALUES(value);

INSERT INTO ui_string_translations (ui_string_id, language_code, value)
SELECT id, 'es', 'Conozca la Política de Privacidad de Esterni.' FROM ui_strings WHERE string_key = 'privacy.meta_description'
ON DUPLICATE KEY UPDATE value = VALUES(value);

INSERT INTO ui_string_translations (ui_string_id, language_code, value)
SELECT id, 'it', 'Scopri l''Informativa sulla Privacy di Esterni.' FROM ui_strings WHERE string_key = 'privacy.meta_description'
ON DUPLICATE KEY UPDATE value = VALUES(value);
