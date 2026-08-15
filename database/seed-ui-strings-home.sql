-- Textos da homepage (heróis, títulos de seção, texto "sobre", CTA) + rótulos de linhas/tipos.

INSERT INTO ui_strings (string_key, group_name, description, sort_order) VALUES
('home.hero1.subtitle', 'home', 'Slide 1 — subtítulo', 1),
('home.hero1.title', 'home', 'Slide 1 — título', 2),
('home.hero1.cta', 'home', 'Slide 1 — botão', 3),
('home.hero2.subtitle', 'home', 'Slide 2 — subtítulo', 4),
('home.hero2.title', 'home', 'Slide 2 — título', 5),
('home.hero2.cta', 'home', 'Slide 2 — botão', 6),
('home.hero3.subtitle', 'home', 'Slide 3 — subtítulo', 7),
('home.hero3.title', 'home', 'Slide 3 — título', 8),
('home.hero3.cta', 'home', 'Slide 3 — botão', 9),
('home.lines.title', 'home', 'Título da seção de linhas', 10),
('home.see_more', 'home', '"veja mais" nos títulos de seção', 11),
('home.about.title', 'home', 'Título do bloco "sobre"', 12),
('home.about.text', 'home', 'Texto do bloco "sobre"', 13),
('home.about.cta', 'home', 'Botão do bloco "sobre"', 14),
('home.products.title', 'home', 'Título da seção de tipos de produto', 15),
('home.news.title', 'home', 'Título da seção de notícias', 16),
('home.cta.title', 'home', 'Título do CTA final', 17),
('home.cta.text', 'home', 'Texto do CTA final', 18),
('home.cta.button', 'home', 'Botão do CTA final', 19),
('line.prefix', 'home', 'Prefixo "Linha" antes do nome da linha (nomes próprios não são traduzidos)', 20),
('type.balizador', 'home', 'Tipo de produto — Balizadores', 21),
('type.banco', 'home', 'Tipo de produto — Bancos', 22),
('type.bicicletario', 'home', 'Tipo de produto — Bicicletários', 23),
('type.cerca', 'home', 'Tipo de produto — Cercas', 24),
('type.cinzeiro', 'home', 'Tipo de produto — Cinzeiros', 25),
('type.floreira', 'home', 'Tipo de produto — Floreiras', 26),
('type.lixeira', 'home', 'Tipo de produto — Lixeiras', 27),
('type.mesa', 'home', 'Tipo de produto — Mesa', 28),
('type.ponto_informacao', 'home', 'Tipo de produto — Pontos de Informações', 29),
('type.ponto_onibus', 'home', 'Tipo de produto — Pontos de Ônibus', 30),
('type.poste', 'home', 'Tipo de produto — Postes', 31);

INSERT INTO ui_string_translations (ui_string_id, language_code, value)
SELECT id, 'pt-BR', v FROM ui_strings JOIN (
  SELECT 'home.hero1.subtitle' k, 'Postes Decorativos' v UNION ALL
  SELECT 'home.hero1.title', 'Design e Mobiliário Urbano' UNION ALL
  SELECT 'home.hero1.cta', 'Conheça a Esterni' UNION ALL
  SELECT 'home.hero2.subtitle', 'Diferentes Linhas de Produtos' UNION ALL
  SELECT 'home.hero2.title', 'Para atender às mais variadas necessidades' UNION ALL
  SELECT 'home.hero2.cta', 'Conheça as linhas' UNION ALL
  SELECT 'home.hero3.subtitle', 'Produtos personalizados' UNION ALL
  SELECT 'home.hero3.title', 'Para ter um produto exclusivo no seu condomínio ou prefeitura' UNION ALL
  SELECT 'home.hero3.cta', 'Saiba mais' UNION ALL
  SELECT 'home.lines.title', 'Linhas de Produtos Esterni' UNION ALL
  SELECT 'home.see_more', 'veja mais' UNION ALL
  SELECT 'home.about.title', 'Postes decorativos e mobiliário urbano' UNION ALL
  SELECT 'home.about.text', 'Um dos principais focos do Grupo Technomast é o embelezamento urbano. Para isto criamos a marca ESTERNI, através da qual, além de dar especial atenção a qualidade e acabamento, desenhamos, em parceria com os melhores designers, linhas de produtos integrando postes e mobiliário urbano para as mais variadas aplicações, como parques, praças, condomínios ou shoppings centers.' UNION ALL
  SELECT 'home.about.cta', 'Saiba mais' UNION ALL
  SELECT 'home.products.title', 'Produtos Esterni' UNION ALL
  SELECT 'home.news.title', 'Notícias Esterni' UNION ALL
  SELECT 'home.cta.title', 'Deseja mais informações?' UNION ALL
  SELECT 'home.cta.text', 'Podemos ajudá-lo com a seleção da linha ideal para seu projeto, com a criação de novos produtos, ou com a customização das linhas de produtos Esterni.<br><br>Fale com nossa equipe, teremos prazer em receber seu contato.' UNION ALL
  SELECT 'home.cta.button', 'Fale com um consultor' UNION ALL
  SELECT 'line.prefix', 'Linha' UNION ALL
  SELECT 'type.balizador', 'Balizadores' UNION ALL
  SELECT 'type.banco', 'Bancos' UNION ALL
  SELECT 'type.bicicletario', 'Bicicletários' UNION ALL
  SELECT 'type.cerca', 'Cercas' UNION ALL
  SELECT 'type.cinzeiro', 'Cinzeiros' UNION ALL
  SELECT 'type.floreira', 'Floreiras' UNION ALL
  SELECT 'type.lixeira', 'Lixeiras' UNION ALL
  SELECT 'type.mesa', 'Mesa' UNION ALL
  SELECT 'type.ponto_informacao', 'Pontos de Informações' UNION ALL
  SELECT 'type.ponto_onibus', 'Pontos de Ônibus' UNION ALL
  SELECT 'type.poste', 'Postes'
) x ON x.k = ui_strings.string_key;

INSERT INTO ui_string_translations (ui_string_id, language_code, value)
SELECT id, 'en', v FROM ui_strings JOIN (
  SELECT 'home.hero1.subtitle' k, 'Decorative Poles' v UNION ALL
  SELECT 'home.hero1.title', 'Design and Urban Furniture' UNION ALL
  SELECT 'home.hero1.cta', 'Discover Esterni' UNION ALL
  SELECT 'home.hero2.subtitle', 'Different Product Lines' UNION ALL
  SELECT 'home.hero2.title', 'To meet the most varied needs' UNION ALL
  SELECT 'home.hero2.cta', 'Explore the lines' UNION ALL
  SELECT 'home.hero3.subtitle', 'Custom products' UNION ALL
  SELECT 'home.hero3.title', 'For an exclusive product in your condominium or city hall' UNION ALL
  SELECT 'home.hero3.cta', 'Learn more' UNION ALL
  SELECT 'home.lines.title', 'Esterni Product Lines' UNION ALL
  SELECT 'home.see_more', 'see more' UNION ALL
  SELECT 'home.about.title', 'Decorative poles and urban furniture' UNION ALL
  SELECT 'home.about.text', 'One of Grupo Technomast''s main focuses is urban beautification. That is why we created the ESTERNI brand, through which — besides paying special attention to quality and finish — we design, in partnership with the best designers, product lines that integrate poles and urban furniture for the most varied applications, such as parks, squares, condominiums or shopping centers.' UNION ALL
  SELECT 'home.about.cta', 'Learn more' UNION ALL
  SELECT 'home.products.title', 'Esterni Products' UNION ALL
  SELECT 'home.news.title', 'Esterni News' UNION ALL
  SELECT 'home.cta.title', 'Would you like more information?' UNION ALL
  SELECT 'home.cta.text', 'We can help you choose the ideal line for your project, create new products, or customize Esterni''s product lines.<br><br>Talk to our team, we will be happy to hear from you.' UNION ALL
  SELECT 'home.cta.button', 'Talk to a consultant' UNION ALL
  SELECT 'line.prefix', 'Line' UNION ALL
  SELECT 'type.balizador', 'Bollards' UNION ALL
  SELECT 'type.banco', 'Benches' UNION ALL
  SELECT 'type.bicicletario', 'Bike Racks' UNION ALL
  SELECT 'type.cerca', 'Fences' UNION ALL
  SELECT 'type.cinzeiro', 'Ashtrays' UNION ALL
  SELECT 'type.floreira', 'Planters' UNION ALL
  SELECT 'type.lixeira', 'Trash Bins' UNION ALL
  SELECT 'type.mesa', 'Table' UNION ALL
  SELECT 'type.ponto_informacao', 'Information Points' UNION ALL
  SELECT 'type.ponto_onibus', 'Bus Stops' UNION ALL
  SELECT 'type.poste', 'Poles'
) x ON x.k = ui_strings.string_key;

INSERT INTO ui_string_translations (ui_string_id, language_code, value)
SELECT id, 'es', v FROM ui_strings JOIN (
  SELECT 'home.hero1.subtitle' k, 'Postes Decorativos' v UNION ALL
  SELECT 'home.hero1.title', 'Diseño y Mobiliario Urbano' UNION ALL
  SELECT 'home.hero1.cta', 'Conoce Esterni' UNION ALL
  SELECT 'home.hero2.subtitle', 'Diferentes Líneas de Productos' UNION ALL
  SELECT 'home.hero2.title', 'Para atender las más variadas necesidades' UNION ALL
  SELECT 'home.hero2.cta', 'Conoce las líneas' UNION ALL
  SELECT 'home.hero3.subtitle', 'Productos personalizados' UNION ALL
  SELECT 'home.hero3.title', 'Para tener un producto exclusivo en tu condominio o municipio' UNION ALL
  SELECT 'home.hero3.cta', 'Saber más' UNION ALL
  SELECT 'home.lines.title', 'Líneas de Productos Esterni' UNION ALL
  SELECT 'home.see_more', 'ver más' UNION ALL
  SELECT 'home.about.title', 'Postes decorativos y mobiliario urbano' UNION ALL
  SELECT 'home.about.text', 'Uno de los principales focos del Grupo Technomast es el embellecimiento urbano. Por eso creamos la marca ESTERNI, con la que, además de prestar especial atención a la calidad y el acabado, diseñamos, en colaboración con los mejores diseñadores, líneas de productos que integran postes y mobiliario urbano para las más variadas aplicaciones, como parques, plazas, condominios o centros comerciales.' UNION ALL
  SELECT 'home.about.cta', 'Saber más' UNION ALL
  SELECT 'home.products.title', 'Productos Esterni' UNION ALL
  SELECT 'home.news.title', 'Noticias Esterni' UNION ALL
  SELECT 'home.cta.title', '¿Deseas más información?' UNION ALL
  SELECT 'home.cta.text', 'Podemos ayudarte a seleccionar la línea ideal para tu proyecto, a crear nuevos productos o a personalizar las líneas de productos Esterni.<br><br>Habla con nuestro equipo, será un placer atenderte.' UNION ALL
  SELECT 'home.cta.button', 'Habla con un asesor' UNION ALL
  SELECT 'line.prefix', 'Línea' UNION ALL
  SELECT 'type.balizador', 'Balizas' UNION ALL
  SELECT 'type.banco', 'Bancos' UNION ALL
  SELECT 'type.bicicletario', 'Aparcabicicletas' UNION ALL
  SELECT 'type.cerca', 'Cercas' UNION ALL
  SELECT 'type.cinzeiro', 'Ceniceros' UNION ALL
  SELECT 'type.floreira', 'Jardineras' UNION ALL
  SELECT 'type.lixeira', 'Papeleras' UNION ALL
  SELECT 'type.mesa', 'Mesa' UNION ALL
  SELECT 'type.ponto_informacao', 'Puntos de Información' UNION ALL
  SELECT 'type.ponto_onibus', 'Paradas de Autobús' UNION ALL
  SELECT 'type.poste', 'Postes'
) x ON x.k = ui_strings.string_key;

INSERT INTO ui_string_translations (ui_string_id, language_code, value)
SELECT id, 'it', v FROM ui_strings JOIN (
  SELECT 'home.hero1.subtitle' k, 'Pali Decorativi' v UNION ALL
  SELECT 'home.hero1.title', 'Design e Arredo Urbano' UNION ALL
  SELECT 'home.hero1.cta', 'Scopri Esterni' UNION ALL
  SELECT 'home.hero2.subtitle', 'Diverse Linee di Prodotti' UNION ALL
  SELECT 'home.hero2.title', 'Per soddisfare le esigenze più diverse' UNION ALL
  SELECT 'home.hero2.cta', 'Scopri le linee' UNION ALL
  SELECT 'home.hero3.subtitle', 'Prodotti personalizzati' UNION ALL
  SELECT 'home.hero3.title', 'Per un prodotto esclusivo nel tuo condominio o comune' UNION ALL
  SELECT 'home.hero3.cta', 'Scopri di più' UNION ALL
  SELECT 'home.lines.title', 'Linee di Prodotti Esterni' UNION ALL
  SELECT 'home.see_more', 'scopri di più' UNION ALL
  SELECT 'home.about.title', 'Pali decorativi e arredo urbano' UNION ALL
  SELECT 'home.about.text', 'Uno dei principali obiettivi del Gruppo Technomast è l''abbellimento urbano. Per questo abbiamo creato il marchio ESTERNI, attraverso il quale, oltre a prestare particolare attenzione alla qualità e alla finitura, progettiamo, in collaborazione con i migliori designer, linee di prodotti che integrano pali e arredo urbano per le più diverse applicazioni, come parchi, piazze, condomini o centri commerciali.' UNION ALL
  SELECT 'home.about.cta', 'Scopri di più' UNION ALL
  SELECT 'home.products.title', 'Prodotti Esterni' UNION ALL
  SELECT 'home.news.title', 'Notizie Esterni' UNION ALL
  SELECT 'home.cta.title', 'Desideri maggiori informazioni?' UNION ALL
  SELECT 'home.cta.text', 'Possiamo aiutarti a scegliere la linea ideale per il tuo progetto, a creare nuovi prodotti o a personalizzare le linee di prodotti Esterni.<br><br>Parla con il nostro team, saremo lieti di sentirti.' UNION ALL
  SELECT 'home.cta.button', 'Parla con un consulente' UNION ALL
  SELECT 'line.prefix', 'Linea' UNION ALL
  SELECT 'type.balizador', 'Delimitatori' UNION ALL
  SELECT 'type.banco', 'Panchine' UNION ALL
  SELECT 'type.bicicletario', 'Portabiciclette' UNION ALL
  SELECT 'type.cerca', 'Recinzioni' UNION ALL
  SELECT 'type.cinzeiro', 'Posacenere' UNION ALL
  SELECT 'type.floreira', 'Fioriere' UNION ALL
  SELECT 'type.lixeira', 'Cestini' UNION ALL
  SELECT 'type.mesa', 'Tavolo' UNION ALL
  SELECT 'type.ponto_informacao', 'Punti Informativi' UNION ALL
  SELECT 'type.ponto_onibus', 'Fermate Autobus' UNION ALL
  SELECT 'type.poste', 'Pali'
) x ON x.k = ui_strings.string_key;
