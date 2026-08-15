INSERT INTO ui_strings (string_key, group_name, description, sort_order) VALUES
('whatsapp.welcome', 'whatsapp', 'Widget de WhatsApp — mensagem de boas-vindas acima dos balões de assunto', 1),
('whatsapp.button_label', 'whatsapp', 'Widget de WhatsApp — texto do botão flutuante fechado', 2)
ON DUPLICATE KEY UPDATE description = VALUES(description);

INSERT INTO ui_string_translations (ui_string_id, language_code, value)
SELECT id, 'pt-BR', v FROM ui_strings JOIN (
  SELECT 'whatsapp.welcome' k, 'Olá, seja bem vindo(a)!<br><strong>Clique sobre o assunto</strong> de seu interesse para iniciar uma conversa no WhatsApp (app ou web).' v UNION ALL
  SELECT 'whatsapp.button_label', 'WhatsApp'
) x ON x.k = ui_strings.string_key ON DUPLICATE KEY UPDATE value = VALUES(value);

INSERT INTO ui_string_translations (ui_string_id, language_code, value)
SELECT id, 'en', v FROM ui_strings JOIN (
  SELECT 'whatsapp.welcome' k, 'Hello, welcome!<br><strong>Click on the topic</strong> you are interested in to start a conversation on WhatsApp (app or web).' v UNION ALL
  SELECT 'whatsapp.button_label', 'WhatsApp'
) x ON x.k = ui_strings.string_key ON DUPLICATE KEY UPDATE value = VALUES(value);

INSERT INTO ui_string_translations (ui_string_id, language_code, value)
SELECT id, 'es', v FROM ui_strings JOIN (
  SELECT 'whatsapp.welcome' k, '¡Hola, bienvenido(a)!<br><strong>Haz clic en el asunto</strong> de tu interés para iniciar una conversación en WhatsApp (app o web).' v UNION ALL
  SELECT 'whatsapp.button_label', 'WhatsApp'
) x ON x.k = ui_strings.string_key ON DUPLICATE KEY UPDATE value = VALUES(value);

INSERT INTO ui_string_translations (ui_string_id, language_code, value)
SELECT id, 'it', v FROM ui_strings JOIN (
  SELECT 'whatsapp.welcome' k, 'Ciao, benvenuto(a)!<br><strong>Clicca sull''argomento</strong> di tuo interesse per avviare una conversazione su WhatsApp (app o web).' v UNION ALL
  SELECT 'whatsapp.button_label', 'WhatsApp'
) x ON x.k = ui_strings.string_key ON DUPLICATE KEY UPDATE value = VALUES(value);
