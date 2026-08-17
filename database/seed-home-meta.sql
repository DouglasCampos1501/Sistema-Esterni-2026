-- Título/descrição SEO da home, traduzidos por idioma. Antes estavam hardcoded
-- em português direto no index.php, então a aba do navegador e a meta description
-- ficavam em PT mesmo em /en/, /es/, /it/.

INSERT INTO ui_strings (string_key, group_name, sort_order) VALUES
('home.meta_title', 'home', 1),
('home.meta_description', 'home', 2)
ON DUPLICATE KEY UPDATE string_key = VALUES(string_key);

INSERT INTO ui_string_translations (ui_string_id, language_code, value)
SELECT id, 'pt-BR', 'Esterni Design e Mobiliário Urbano - Campo Largo / Paraná' FROM ui_strings WHERE string_key = 'home.meta_title'
ON DUPLICATE KEY UPDATE value = VALUES(value);
INSERT INTO ui_string_translations (ui_string_id, language_code, value)
SELECT id, 'pt-BR', 'Esterni Design e Mobiliário Urbano. Harmonia entre postes e mobiliário, criando equilíbrio para praças, parques, beira-mares, calçadões e condomínios fechados.' FROM ui_strings WHERE string_key = 'home.meta_description'
ON DUPLICATE KEY UPDATE value = VALUES(value);

INSERT INTO ui_string_translations (ui_string_id, language_code, value)
SELECT id, 'en', 'Esterni Design e Mobiliário Urbano - Campo Largo / Paraná, Brazil' FROM ui_strings WHERE string_key = 'home.meta_title'
ON DUPLICATE KEY UPDATE value = VALUES(value);
INSERT INTO ui_string_translations (ui_string_id, language_code, value)
SELECT id, 'en', 'Esterni Design e Mobiliário Urbano. Harmony between poles and street furniture, creating balance for squares, parks, waterfronts, boardwalks and gated communities.' FROM ui_strings WHERE string_key = 'home.meta_description'
ON DUPLICATE KEY UPDATE value = VALUES(value);

INSERT INTO ui_string_translations (ui_string_id, language_code, value)
SELECT id, 'es', 'Esterni Design e Mobiliário Urbano - Campo Largo / Paraná, Brasil' FROM ui_strings WHERE string_key = 'home.meta_title'
ON DUPLICATE KEY UPDATE value = VALUES(value);
INSERT INTO ui_string_translations (ui_string_id, language_code, value)
SELECT id, 'es', 'Esterni Design e Mobiliário Urbano. Armonía entre postes y mobiliario, creando equilibrio para plazas, parques, malecones, paseos marítimos y condominios cerrados.' FROM ui_strings WHERE string_key = 'home.meta_description'
ON DUPLICATE KEY UPDATE value = VALUES(value);

INSERT INTO ui_string_translations (ui_string_id, language_code, value)
SELECT id, 'it', 'Esterni Design e Mobiliário Urbano - Campo Largo / Paraná, Brasile' FROM ui_strings WHERE string_key = 'home.meta_title'
ON DUPLICATE KEY UPDATE value = VALUES(value);
INSERT INTO ui_string_translations (ui_string_id, language_code, value)
SELECT id, 'it', 'Esterni Design e Mobiliário Urbano. Armonia tra pali e arredo urbano, creando equilibrio per piazze, parchi, lungomari, passeggiate e condomini privati.' FROM ui_strings WHERE string_key = 'home.meta_description'
ON DUPLICATE KEY UPDATE value = VALUES(value);
