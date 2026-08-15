INSERT INTO ui_strings (string_key, group_name, description, sort_order) VALUES
('newsletter.success', 'footer', 'Newsletter — mensagem de sucesso após inscrição', 5),
('newsletter.error', 'footer', 'Newsletter — mensagem de erro (e-mail inválido ou já cadastrado)', 6)
ON DUPLICATE KEY UPDATE description = VALUES(description);

INSERT INTO ui_string_translations (ui_string_id, language_code, value)
SELECT id, 'pt-BR', v FROM ui_strings JOIN (
  SELECT 'newsletter.success' k, 'Inscrição confirmada! Obrigado por acompanhar a Esterni.' v UNION ALL
  SELECT 'newsletter.error', 'Não foi possível concluir sua inscrição. Confira o e-mail e tente novamente.'
) x ON x.k = ui_strings.string_key ON DUPLICATE KEY UPDATE value = VALUES(value);

INSERT INTO ui_string_translations (ui_string_id, language_code, value)
SELECT id, 'en', v FROM ui_strings JOIN (
  SELECT 'newsletter.success' k, 'Subscription confirmed! Thanks for following Esterni.' v UNION ALL
  SELECT 'newsletter.error', 'We could not complete your subscription. Please check the email and try again.'
) x ON x.k = ui_strings.string_key ON DUPLICATE KEY UPDATE value = VALUES(value);

INSERT INTO ui_string_translations (ui_string_id, language_code, value)
SELECT id, 'es', v FROM ui_strings JOIN (
  SELECT 'newsletter.success' k, '¡Inscripción confirmada! Gracias por seguir a Esterni.' v UNION ALL
  SELECT 'newsletter.error', 'No pudimos completar tu inscripción. Revisa el correo e inténtalo de nuevo.'
) x ON x.k = ui_strings.string_key ON DUPLICATE KEY UPDATE value = VALUES(value);

INSERT INTO ui_string_translations (ui_string_id, language_code, value)
SELECT id, 'it', v FROM ui_strings JOIN (
  SELECT 'newsletter.success' k, 'Iscrizione confermata! Grazie per seguire Esterni.' v UNION ALL
  SELECT 'newsletter.error', 'Non è stato possibile completare l''iscrizione. Controlla l''email e riprova.'
) x ON x.k = ui_strings.string_key ON DUPLICATE KEY UPDATE value = VALUES(value);
