-- Corrige os textos da seção de newsletter pra bater com o formulário real do site
-- (Fluent Forms: "Receba por e-mail:" + Nome completo + E-mail + "Quero receber!" + consentimento LGPD).

UPDATE ui_strings SET description = 'Newsletter — rótulo "Receba por e-mail:"' WHERE string_key = 'newsletter.title';
UPDATE ui_strings SET description = 'Newsletter — placeholder do campo de e-mail' WHERE string_key = 'newsletter.placeholder';
UPDATE ui_strings SET description = 'Newsletter — botão "Quero receber!"' WHERE string_key = 'newsletter.submit';

INSERT INTO ui_strings (string_key, group_name, description, sort_order) VALUES
('newsletter.name_placeholder', 'footer', 'Newsletter — placeholder do campo de nome', 2),
('newsletter.consent', 'footer', 'Newsletter — texto de consentimento LGPD (com link pra política de privacidade)', 4)
ON DUPLICATE KEY UPDATE description = VALUES(description);

-- pt-BR
UPDATE ui_string_translations t JOIN ui_strings s ON s.id = t.ui_string_id
SET t.value = 'Receba por e-mail:' WHERE s.string_key = 'newsletter.title' AND t.language_code = 'pt-BR';
UPDATE ui_string_translations t JOIN ui_strings s ON s.id = t.ui_string_id
SET t.value = 'Quero receber!' WHERE s.string_key = 'newsletter.submit' AND t.language_code = 'pt-BR';

INSERT INTO ui_string_translations (ui_string_id, language_code, value)
SELECT id, 'pt-BR', v FROM ui_strings JOIN (
  SELECT 'newsletter.name_placeholder' k, 'Nome completo' v UNION ALL
  SELECT 'newsletter.consent', 'Ao me cadastrar, concordo que li e estou de acordo com a <a href="/politica-de-privacidade/" target="_blank">Política de Privacidade</a>.'
) x ON x.k = ui_strings.string_key
ON DUPLICATE KEY UPDATE value = VALUES(value);

-- en
UPDATE ui_string_translations t JOIN ui_strings s ON s.id = t.ui_string_id
SET t.value = 'Get updates by email:' WHERE s.string_key = 'newsletter.title' AND t.language_code = 'en';
UPDATE ui_string_translations t JOIN ui_strings s ON s.id = t.ui_string_id
SET t.value = 'I want it!' WHERE s.string_key = 'newsletter.submit' AND t.language_code = 'en';

INSERT INTO ui_string_translations (ui_string_id, language_code, value)
SELECT id, 'en', v FROM ui_strings JOIN (
  SELECT 'newsletter.name_placeholder' k, 'Full name' v UNION ALL
  SELECT 'newsletter.consent', 'By signing up, I agree that I have read and accept the <a href="/en/politica-de-privacidade/" target="_blank">Privacy Policy</a>.'
) x ON x.k = ui_strings.string_key
ON DUPLICATE KEY UPDATE value = VALUES(value);

-- es
UPDATE ui_string_translations t JOIN ui_strings s ON s.id = t.ui_string_id
SET t.value = 'Recibe por correo electrónico:' WHERE s.string_key = 'newsletter.title' AND t.language_code = 'es';
UPDATE ui_string_translations t JOIN ui_strings s ON s.id = t.ui_string_id
SET t.value = '¡Quiero recibir!' WHERE s.string_key = 'newsletter.submit' AND t.language_code = 'es';

INSERT INTO ui_string_translations (ui_string_id, language_code, value)
SELECT id, 'es', v FROM ui_strings JOIN (
  SELECT 'newsletter.name_placeholder' k, 'Nombre completo' v UNION ALL
  SELECT 'newsletter.consent', 'Al registrarme, acepto que he leído y estoy de acuerdo con la <a href="/es/politica-de-privacidade/" target="_blank">Política de Privacidad</a>.'
) x ON x.k = ui_strings.string_key
ON DUPLICATE KEY UPDATE value = VALUES(value);

-- it
UPDATE ui_string_translations t JOIN ui_strings s ON s.id = t.ui_string_id
SET t.value = 'Ricevi via email:' WHERE s.string_key = 'newsletter.title' AND t.language_code = 'it';
UPDATE ui_string_translations t JOIN ui_strings s ON s.id = t.ui_string_id
SET t.value = 'Voglio riceverlo!' WHERE s.string_key = 'newsletter.submit' AND t.language_code = 'it';

INSERT INTO ui_string_translations (ui_string_id, language_code, value)
SELECT id, 'it', v FROM ui_strings JOIN (
  SELECT 'newsletter.name_placeholder' k, 'Nome completo' v UNION ALL
  SELECT 'newsletter.consent', 'Iscrivendomi, accetto di aver letto e di essere d''accordo con la <a href="/it/politica-de-privacidade/" target="_blank">Informativa sulla Privacy</a>.'
) x ON x.k = ui_strings.string_key
ON DUPLICATE KEY UPDATE value = VALUES(value);
