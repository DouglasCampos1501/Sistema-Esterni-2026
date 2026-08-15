-- Conteúdo da página de Contato (título, textos fixos, sidebar) + traduções dos
-- campos do formulário dinâmico (contact_form_field_translations).

INSERT INTO ui_strings (string_key, group_name, description, sort_order) VALUES
('contato.page_title', 'contato', 'Título da página de contato', 1),
('contato.form_title', 'contato', '"Envie uma mensagem"', 2),
('contato.form_intro', 'contato', 'Texto acima do formulário', 3),
('contato.consent', 'contato', 'Texto de consentimento LGPD do formulário de contato', 4),
('contato.submit', 'contato', 'Botão de envio do formulário', 5),
('contato.success', 'contato', 'Mensagem exibida após envio com sucesso', 6),
('contato.error', 'contato', 'Mensagem exibida se algo der errado no envio', 7),
('contato.location_title', 'contato', '"Localização" (sidebar)', 8),
('contato.phones_title', 'contato', '"Telefones" (sidebar)', 9)
ON DUPLICATE KEY UPDATE description = VALUES(description);

INSERT INTO ui_string_translations (ui_string_id, language_code, value)
SELECT id, 'pt-BR', v FROM ui_strings JOIN (
  SELECT 'contato.page_title' k, 'Contato' v UNION ALL
  SELECT 'contato.form_title', 'Envie uma mensagem' UNION ALL
  SELECT 'contato.form_intro', 'Envie uma mensagem para a <b>Esterni</b>. Retornaremos o seu contato o mais breve possível.' UNION ALL
  SELECT 'contato.consent', 'Ao enviar este formulário, concordo que li e estou de acordo com a <a href="/politica-de-privacidade/" target="_blank">Política de Privacidade</a>.' UNION ALL
  SELECT 'contato.submit', 'Enviar mensagem' UNION ALL
  SELECT 'contato.success', 'Mensagem enviada! Obrigado pelo contato — retornaremos o mais breve possível.' UNION ALL
  SELECT 'contato.error', 'Não foi possível enviar sua mensagem. Confira os campos e tente novamente.' UNION ALL
  SELECT 'contato.location_title', 'Localização' UNION ALL
  SELECT 'contato.phones_title', 'Telefones'
) x ON x.k = ui_strings.string_key ON DUPLICATE KEY UPDATE value = VALUES(value);

INSERT INTO ui_string_translations (ui_string_id, language_code, value)
SELECT id, 'en', v FROM ui_strings JOIN (
  SELECT 'contato.page_title' k, 'Contact' v UNION ALL
  SELECT 'contato.form_title', 'Send a message' UNION ALL
  SELECT 'contato.form_intro', 'Send a message to <b>Esterni</b>. We will get back to you as soon as possible.' UNION ALL
  SELECT 'contato.consent', 'By submitting this form, I agree that I have read and accept the <a href="/en/politica-de-privacidade/" target="_blank">Privacy Policy</a>.' UNION ALL
  SELECT 'contato.submit', 'Send message' UNION ALL
  SELECT 'contato.success', 'Message sent! Thank you for reaching out — we will get back to you soon.' UNION ALL
  SELECT 'contato.error', 'We could not send your message. Please check the fields and try again.' UNION ALL
  SELECT 'contato.location_title', 'Location' UNION ALL
  SELECT 'contato.phones_title', 'Phone'
) x ON x.k = ui_strings.string_key ON DUPLICATE KEY UPDATE value = VALUES(value);

INSERT INTO ui_string_translations (ui_string_id, language_code, value)
SELECT id, 'es', v FROM ui_strings JOIN (
  SELECT 'contato.page_title' k, 'Contacto' v UNION ALL
  SELECT 'contato.form_title', 'Envía un mensaje' UNION ALL
  SELECT 'contato.form_intro', 'Envía un mensaje a <b>Esterni</b>. Te responderemos lo antes posible.' UNION ALL
  SELECT 'contato.consent', 'Al enviar este formulario, acepto que he leído y estoy de acuerdo con la <a href="/es/politica-de-privacidade/" target="_blank">Política de Privacidad</a>.' UNION ALL
  SELECT 'contato.submit', 'Enviar mensaje' UNION ALL
  SELECT 'contato.success', '¡Mensaje enviado! Gracias por contactarnos — te responderemos lo antes posible.' UNION ALL
  SELECT 'contato.error', 'No pudimos enviar tu mensaje. Revisa los campos e inténtalo de nuevo.' UNION ALL
  SELECT 'contato.location_title', 'Ubicación' UNION ALL
  SELECT 'contato.phones_title', 'Teléfono'
) x ON x.k = ui_strings.string_key ON DUPLICATE KEY UPDATE value = VALUES(value);

INSERT INTO ui_string_translations (ui_string_id, language_code, value)
SELECT id, 'it', v FROM ui_strings JOIN (
  SELECT 'contato.page_title' k, 'Contatto' v UNION ALL
  SELECT 'contato.form_title', 'Invia un messaggio' UNION ALL
  SELECT 'contato.form_intro', 'Invia un messaggio a <b>Esterni</b>. Ti risponderemo il prima possibile.' UNION ALL
  SELECT 'contato.consent', 'Inviando questo modulo, accetto di aver letto e di essere d''accordo con la <a href="/it/politica-de-privacidade/" target="_blank">Informativa sulla Privacy</a>.' UNION ALL
  SELECT 'contato.submit', 'Invia messaggio' UNION ALL
  SELECT 'contato.success', 'Messaggio inviato! Grazie per averci contattato — ti risponderemo il prima possibile.' UNION ALL
  SELECT 'contato.error', 'Non è stato possibile inviare il messaggio. Controlla i campi e riprova.' UNION ALL
  SELECT 'contato.location_title', 'Posizione' UNION ALL
  SELECT 'contato.phones_title', 'Telefono'
) x ON x.k = ui_strings.string_key ON DUPLICATE KEY UPDATE value = VALUES(value);

-- Traduções dos rótulos/placeholders do formulário dinâmico (EN/ES/IT — pt-BR já é a base em contact_form_fields)
INSERT INTO contact_form_field_translations (field_id, language_code, label, placeholder)
SELECT id, 'en', l, p FROM contact_form_fields JOIN (
  SELECT 'name' k, 'Name' l, 'Your full name' p UNION ALL
  SELECT 'email', 'Email', 'Your best email' UNION ALL
  SELECT 'phone', 'Phone', '(00) 0000-0000' UNION ALL
  SELECT 'message', 'Message', ''
) x ON x.k = contact_form_fields.field_key
ON DUPLICATE KEY UPDATE label = VALUES(label), placeholder = VALUES(placeholder);

INSERT INTO contact_form_field_translations (field_id, language_code, label, placeholder)
SELECT id, 'es', l, p FROM contact_form_fields JOIN (
  SELECT 'name' k, 'Nombre' l, 'Tu nombre completo' p UNION ALL
  SELECT 'email', 'Correo electrónico', 'Tu mejor correo electrónico' UNION ALL
  SELECT 'phone', 'Teléfono', '(00) 0000-0000' UNION ALL
  SELECT 'message', 'Mensaje', ''
) x ON x.k = contact_form_fields.field_key
ON DUPLICATE KEY UPDATE label = VALUES(label), placeholder = VALUES(placeholder);

INSERT INTO contact_form_field_translations (field_id, language_code, label, placeholder)
SELECT id, 'it', l, p FROM contact_form_fields JOIN (
  SELECT 'name' k, 'Nome' l, 'Il tuo nome completo' p UNION ALL
  SELECT 'email', 'Email', 'La tua migliore email' UNION ALL
  SELECT 'phone', 'Telefono', '(00) 0000-0000' UNION ALL
  SELECT 'message', 'Messaggio', ''
) x ON x.k = contact_form_fields.field_key
ON DUPLICATE KEY UPDATE label = VALUES(label), placeholder = VALUES(placeholder);
