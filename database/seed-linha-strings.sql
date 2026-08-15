INSERT INTO ui_strings (string_key, group_name, description, sort_order) VALUES
('line.jump_button', 'linhas', 'Botão "Produtos desta linha" (rola até a seção de produtos)', 1),
('line.products_heading', 'linhas', 'Título "Conheça a Linha de Produtos {nome}" — usar %s pro nome', 2),
('line.empty_products', 'linhas', 'Texto exibido quando a linha ainda não tem produtos cadastrados', 3)
ON DUPLICATE KEY UPDATE description = VALUES(description);

INSERT INTO ui_string_translations (ui_string_id, language_code, value)
SELECT id, 'pt-BR', v FROM ui_strings JOIN (
  SELECT 'line.jump_button' k, 'Produtos desta linha' v UNION ALL
  SELECT 'line.products_heading', 'Conheça a Linha de Produtos %s' UNION ALL
  SELECT 'line.empty_products', 'Em breve, novos produtos desta linha.'
) x ON x.k = ui_strings.string_key ON DUPLICATE KEY UPDATE value = VALUES(value);

INSERT INTO ui_string_translations (ui_string_id, language_code, value)
SELECT id, 'en', v FROM ui_strings JOIN (
  SELECT 'line.jump_button' k, 'Products in this line' v UNION ALL
  SELECT 'line.products_heading', 'Discover the %s Product Line' UNION ALL
  SELECT 'line.empty_products', 'New products for this line coming soon.'
) x ON x.k = ui_strings.string_key ON DUPLICATE KEY UPDATE value = VALUES(value);

INSERT INTO ui_string_translations (ui_string_id, language_code, value)
SELECT id, 'es', v FROM ui_strings JOIN (
  SELECT 'line.jump_button' k, 'Productos de esta línea' v UNION ALL
  SELECT 'line.products_heading', 'Conoce la Línea de Productos %s' UNION ALL
  SELECT 'line.empty_products', 'Pronto, nuevos productos de esta línea.'
) x ON x.k = ui_strings.string_key ON DUPLICATE KEY UPDATE value = VALUES(value);

INSERT INTO ui_string_translations (ui_string_id, language_code, value)
SELECT id, 'it', v FROM ui_strings JOIN (
  SELECT 'line.jump_button' k, 'Prodotti di questa linea' v UNION ALL
  SELECT 'line.products_heading', 'Scopri la Linea di Prodotti %s' UNION ALL
  SELECT 'line.empty_products', 'Presto nuovi prodotti per questa linea.'
) x ON x.k = ui_strings.string_key ON DUPLICATE KEY UPDATE value = VALUES(value);
