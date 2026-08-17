-- Nomes de mês (abreviado e por extenso) nos 4 idiomas — usados pelas funções
-- t_month_short()/t_month_full()/format_long_date() em includes/i18n.php, pra
-- não deixar nenhuma data do site hardcoded em português.

INSERT INTO ui_strings (string_key, group_name, sort_order) VALUES
('date.month_short_01', 'datas', 1), ('date.month_short_02', 'datas', 2), ('date.month_short_03', 'datas', 3),
('date.month_short_04', 'datas', 4), ('date.month_short_05', 'datas', 5), ('date.month_short_06', 'datas', 6),
('date.month_short_07', 'datas', 7), ('date.month_short_08', 'datas', 8), ('date.month_short_09', 'datas', 9),
('date.month_short_10', 'datas', 10), ('date.month_short_11', 'datas', 11), ('date.month_short_12', 'datas', 12),
('date.month_full_01', 'datas', 13), ('date.month_full_02', 'datas', 14), ('date.month_full_03', 'datas', 15),
('date.month_full_04', 'datas', 16), ('date.month_full_05', 'datas', 17), ('date.month_full_06', 'datas', 18),
('date.month_full_07', 'datas', 19), ('date.month_full_08', 'datas', 20), ('date.month_full_09', 'datas', 21),
('date.month_full_10', 'datas', 22), ('date.month_full_11', 'datas', 23), ('date.month_full_12', 'datas', 24)
ON DUPLICATE KEY UPDATE string_key = VALUES(string_key);

INSERT INTO ui_string_translations (ui_string_id, language_code, value)
SELECT id, 'pt-BR', v FROM ui_strings JOIN (
  SELECT 'date.month_short_01' k, 'jan' v UNION ALL SELECT 'date.month_short_02', 'fev' UNION ALL
  SELECT 'date.month_short_03', 'mar' UNION ALL SELECT 'date.month_short_04', 'abr' UNION ALL
  SELECT 'date.month_short_05', 'mai' UNION ALL SELECT 'date.month_short_06', 'jun' UNION ALL
  SELECT 'date.month_short_07', 'jul' UNION ALL SELECT 'date.month_short_08', 'ago' UNION ALL
  SELECT 'date.month_short_09', 'set' UNION ALL SELECT 'date.month_short_10', 'out' UNION ALL
  SELECT 'date.month_short_11', 'nov' UNION ALL SELECT 'date.month_short_12', 'dez' UNION ALL
  SELECT 'date.month_full_01', 'janeiro' UNION ALL SELECT 'date.month_full_02', 'fevereiro' UNION ALL
  SELECT 'date.month_full_03', 'março' UNION ALL SELECT 'date.month_full_04', 'abril' UNION ALL
  SELECT 'date.month_full_05', 'maio' UNION ALL SELECT 'date.month_full_06', 'junho' UNION ALL
  SELECT 'date.month_full_07', 'julho' UNION ALL SELECT 'date.month_full_08', 'agosto' UNION ALL
  SELECT 'date.month_full_09', 'setembro' UNION ALL SELECT 'date.month_full_10', 'outubro' UNION ALL
  SELECT 'date.month_full_11', 'novembro' UNION ALL SELECT 'date.month_full_12', 'dezembro'
) x ON x.k = ui_strings.string_key ON DUPLICATE KEY UPDATE value = VALUES(value);

INSERT INTO ui_string_translations (ui_string_id, language_code, value)
SELECT id, 'en', v FROM ui_strings JOIN (
  SELECT 'date.month_short_01' k, 'Jan' v UNION ALL SELECT 'date.month_short_02', 'Feb' UNION ALL
  SELECT 'date.month_short_03', 'Mar' UNION ALL SELECT 'date.month_short_04', 'Apr' UNION ALL
  SELECT 'date.month_short_05', 'May' UNION ALL SELECT 'date.month_short_06', 'Jun' UNION ALL
  SELECT 'date.month_short_07', 'Jul' UNION ALL SELECT 'date.month_short_08', 'Aug' UNION ALL
  SELECT 'date.month_short_09', 'Sep' UNION ALL SELECT 'date.month_short_10', 'Oct' UNION ALL
  SELECT 'date.month_short_11', 'Nov' UNION ALL SELECT 'date.month_short_12', 'Dec' UNION ALL
  SELECT 'date.month_full_01', 'January' UNION ALL SELECT 'date.month_full_02', 'February' UNION ALL
  SELECT 'date.month_full_03', 'March' UNION ALL SELECT 'date.month_full_04', 'April' UNION ALL
  SELECT 'date.month_full_05', 'May' UNION ALL SELECT 'date.month_full_06', 'June' UNION ALL
  SELECT 'date.month_full_07', 'July' UNION ALL SELECT 'date.month_full_08', 'August' UNION ALL
  SELECT 'date.month_full_09', 'September' UNION ALL SELECT 'date.month_full_10', 'October' UNION ALL
  SELECT 'date.month_full_11', 'November' UNION ALL SELECT 'date.month_full_12', 'December'
) x ON x.k = ui_strings.string_key ON DUPLICATE KEY UPDATE value = VALUES(value);

INSERT INTO ui_string_translations (ui_string_id, language_code, value)
SELECT id, 'es', v FROM ui_strings JOIN (
  SELECT 'date.month_short_01' k, 'ene' v UNION ALL SELECT 'date.month_short_02', 'feb' UNION ALL
  SELECT 'date.month_short_03', 'mar' UNION ALL SELECT 'date.month_short_04', 'abr' UNION ALL
  SELECT 'date.month_short_05', 'may' UNION ALL SELECT 'date.month_short_06', 'jun' UNION ALL
  SELECT 'date.month_short_07', 'jul' UNION ALL SELECT 'date.month_short_08', 'ago' UNION ALL
  SELECT 'date.month_short_09', 'sep' UNION ALL SELECT 'date.month_short_10', 'oct' UNION ALL
  SELECT 'date.month_short_11', 'nov' UNION ALL SELECT 'date.month_short_12', 'dic' UNION ALL
  SELECT 'date.month_full_01', 'enero' UNION ALL SELECT 'date.month_full_02', 'febrero' UNION ALL
  SELECT 'date.month_full_03', 'marzo' UNION ALL SELECT 'date.month_full_04', 'abril' UNION ALL
  SELECT 'date.month_full_05', 'mayo' UNION ALL SELECT 'date.month_full_06', 'junio' UNION ALL
  SELECT 'date.month_full_07', 'julio' UNION ALL SELECT 'date.month_full_08', 'agosto' UNION ALL
  SELECT 'date.month_full_09', 'septiembre' UNION ALL SELECT 'date.month_full_10', 'octubre' UNION ALL
  SELECT 'date.month_full_11', 'noviembre' UNION ALL SELECT 'date.month_full_12', 'diciembre'
) x ON x.k = ui_strings.string_key ON DUPLICATE KEY UPDATE value = VALUES(value);

INSERT INTO ui_string_translations (ui_string_id, language_code, value)
SELECT id, 'it', v FROM ui_strings JOIN (
  SELECT 'date.month_short_01' k, 'gen' v UNION ALL SELECT 'date.month_short_02', 'feb' UNION ALL
  SELECT 'date.month_short_03', 'mar' UNION ALL SELECT 'date.month_short_04', 'apr' UNION ALL
  SELECT 'date.month_short_05', 'mag' UNION ALL SELECT 'date.month_short_06', 'giu' UNION ALL
  SELECT 'date.month_short_07', 'lug' UNION ALL SELECT 'date.month_short_08', 'ago' UNION ALL
  SELECT 'date.month_short_09', 'set' UNION ALL SELECT 'date.month_short_10', 'ott' UNION ALL
  SELECT 'date.month_short_11', 'nov' UNION ALL SELECT 'date.month_short_12', 'dic' UNION ALL
  SELECT 'date.month_full_01', 'gennaio' UNION ALL SELECT 'date.month_full_02', 'febbraio' UNION ALL
  SELECT 'date.month_full_03', 'marzo' UNION ALL SELECT 'date.month_full_04', 'aprile' UNION ALL
  SELECT 'date.month_full_05', 'maggio' UNION ALL SELECT 'date.month_full_06', 'giugno' UNION ALL
  SELECT 'date.month_full_07', 'luglio' UNION ALL SELECT 'date.month_full_08', 'agosto' UNION ALL
  SELECT 'date.month_full_09', 'settembre' UNION ALL SELECT 'date.month_full_10', 'ottobre' UNION ALL
  SELECT 'date.month_full_11', 'novembre' UNION ALL SELECT 'date.month_full_12', 'dicembre'
) x ON x.k = ui_strings.string_key ON DUPLICATE KEY UPDATE value = VALUES(value);
