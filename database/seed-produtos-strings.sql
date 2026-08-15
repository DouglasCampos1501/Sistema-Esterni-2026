INSERT INTO ui_strings (string_key, group_name, description, sort_order) VALUES
('produtos.page_title', 'produtos', 'Título da página /produtos/', 1),
('produtos.sidebar_types', 'produtos', 'Sidebar — título "Tipos"', 2),
('produtos.all_products', 'produtos', 'Sidebar — "Todos os Produtos"', 3),
('produtos.sidebar_lines', 'produtos', 'Sidebar — título "Conheça as Linhas"', 4),
('produtos.all_lines', 'produtos', 'Sidebar — "Todas as Linhas"', 5),
('produtos.type_prefix', 'produtos', 'Prefixo "Tipo de Produto:" antes do nome do tipo', 6),
('produto.dimensions_title', 'produtos', 'Título "Dimensões e Medidas" na página de produto', 7),
('produto.line_cta', 'produtos', 'Botão "Conheça a linha %s" (usar %s pro nome da linha)', 8),
('produto.type_cta', 'produtos', 'Botão "Veja mais %s" (usar %s pro nome do tipo)', 9)
ON DUPLICATE KEY UPDATE description = VALUES(description);

INSERT INTO ui_string_translations (ui_string_id, language_code, value)
SELECT id, 'pt-BR', v FROM ui_strings JOIN (
  SELECT 'produtos.page_title' k, 'Produtos' v UNION ALL
  SELECT 'produtos.sidebar_types', 'Tipos' UNION ALL
  SELECT 'produtos.all_products', 'Todos os Produtos' UNION ALL
  SELECT 'produtos.sidebar_lines', 'Conheça as Linhas' UNION ALL
  SELECT 'produtos.all_lines', 'Todas as Linhas' UNION ALL
  SELECT 'produtos.type_prefix', 'Tipo de Produto:' UNION ALL
  SELECT 'produto.dimensions_title', 'Dimensões e Medidas' UNION ALL
  SELECT 'produto.line_cta', 'Conheça a linha %s' UNION ALL
  SELECT 'produto.type_cta', 'Veja mais %s'
) x ON x.k = ui_strings.string_key ON DUPLICATE KEY UPDATE value = VALUES(value);

INSERT INTO ui_string_translations (ui_string_id, language_code, value)
SELECT id, 'en', v FROM ui_strings JOIN (
  SELECT 'produtos.page_title' k, 'Products' v UNION ALL
  SELECT 'produtos.sidebar_types', 'Types' UNION ALL
  SELECT 'produtos.all_products', 'All Products' UNION ALL
  SELECT 'produtos.sidebar_lines', 'Explore the Lines' UNION ALL
  SELECT 'produtos.all_lines', 'All Lines' UNION ALL
  SELECT 'produtos.type_prefix', 'Product Type:' UNION ALL
  SELECT 'produto.dimensions_title', 'Dimensions' UNION ALL
  SELECT 'produto.line_cta', 'Discover the %s line' UNION ALL
  SELECT 'produto.type_cta', 'See more %s'
) x ON x.k = ui_strings.string_key ON DUPLICATE KEY UPDATE value = VALUES(value);

INSERT INTO ui_string_translations (ui_string_id, language_code, value)
SELECT id, 'es', v FROM ui_strings JOIN (
  SELECT 'produtos.page_title' k, 'Productos' v UNION ALL
  SELECT 'produtos.sidebar_types', 'Tipos' UNION ALL
  SELECT 'produtos.all_products', 'Todos los Productos' UNION ALL
  SELECT 'produtos.sidebar_lines', 'Conoce las Líneas' UNION ALL
  SELECT 'produtos.all_lines', 'Todas las Líneas' UNION ALL
  SELECT 'produtos.type_prefix', 'Tipo de Producto:' UNION ALL
  SELECT 'produto.dimensions_title', 'Dimensiones' UNION ALL
  SELECT 'produto.line_cta', 'Conoce la línea %s' UNION ALL
  SELECT 'produto.type_cta', 'Ver más %s'
) x ON x.k = ui_strings.string_key ON DUPLICATE KEY UPDATE value = VALUES(value);

INSERT INTO ui_string_translations (ui_string_id, language_code, value)
SELECT id, 'it', v FROM ui_strings JOIN (
  SELECT 'produtos.page_title' k, 'Prodotti' v UNION ALL
  SELECT 'produtos.sidebar_types', 'Tipi' UNION ALL
  SELECT 'produtos.all_products', 'Tutti i Prodotti' UNION ALL
  SELECT 'produtos.sidebar_lines', 'Scopri le Linee' UNION ALL
  SELECT 'produtos.all_lines', 'Tutte le Linee' UNION ALL
  SELECT 'produtos.type_prefix', 'Tipo di Prodotto:' UNION ALL
  SELECT 'produto.dimensions_title', 'Dimensioni' UNION ALL
  SELECT 'produto.line_cta', 'Scopri la linea %s' UNION ALL
  SELECT 'produto.type_cta', 'Vedi altri %s'
) x ON x.k = ui_strings.string_key ON DUPLICATE KEY UPDATE value = VALUES(value);
