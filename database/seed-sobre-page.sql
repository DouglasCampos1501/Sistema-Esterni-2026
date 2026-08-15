-- Conteúdo da página "A Esterni" (Sobre) — pt-BR (texto real do site) + EN/ES/IT.

INSERT INTO ui_strings (string_key, group_name, description, sort_order) VALUES
('sobre.page_title', 'sobre', 'Título da página (cabeçalho + <title>)', 1),
('sobre.intro_subtitle', 'sobre', 'Subtítulo pequeno acima do título da intro', 2),
('sobre.intro_title', 'sobre', 'Título da intro', 3),
('sobre.intro_text', 'sobre', 'Parágrafo da intro', 4),
('sobre.group_title', 'sobre', 'Título "Uma empresa do Grupo Technomast"', 5),
('sobre.group_text', 'sobre', 'Texto do bloco do Grupo Technomast', 6),
('sobre.group_cta', 'sobre', 'Botão "Conheça o Grupo Technomast"', 7),
('sobre.why_title', 'sobre', 'Título "Por que escolher a Esterni?"', 8),
('sobre.feat1_title', 'sobre', 'Diferencial 1 — título (Design)', 9),
('sobre.feat1_text', 'sobre', 'Diferencial 1 — texto', 10),
('sobre.feat2_title', 'sobre', 'Diferencial 2 — título (Exclusividade)', 11),
('sobre.feat2_text', 'sobre', 'Diferencial 2 — texto', 12),
('sobre.feat3_title', 'sobre', 'Diferencial 3 — título (Qualidade)', 13),
('sobre.feat3_text', 'sobre', 'Diferencial 3 — texto', 14),
('sobre.feat4_title', 'sobre', 'Diferencial 4 — título (Durabilidade)', 15),
('sobre.feat4_text', 'sobre', 'Diferencial 4 — texto', 16),
('sobre.feat5_title', 'sobre', 'Diferencial 5 — título (Fabricado no Brasil)', 17),
('sobre.feat5_text', 'sobre', 'Diferencial 5 — texto', 18),
('sobre.feat6_title', 'sobre', 'Diferencial 6 — título (Atendimento)', 19),
('sobre.feat6_text', 'sobre', 'Diferencial 6 — texto', 20)
ON DUPLICATE KEY UPDATE description = VALUES(description);

INSERT INTO ui_string_translations (ui_string_id, language_code, value)
SELECT id, 'pt-BR', v FROM ui_strings JOIN (
  SELECT 'sobre.page_title' k, 'Esterni Design e Mobiliário Urbano' v UNION ALL
  SELECT 'sobre.intro_subtitle', 'Postes Decorativos e Mobiliário Urbano' UNION ALL
  SELECT 'sobre.intro_title', 'Uma única linguagem para o seu espaço urbano' UNION ALL
  SELECT 'sobre.intro_text', 'O foco da Esterni é o embelezamento urbano. Além de dar especial atenção a qualidade e acabamento dos nossos produtos, desenhamos, em parceria com os melhores designers, linhas de produtos coordenando postes e mobiliário urbano, criando únicas linguagens para praças, parques, beira-mares, calçadões e, principalmente, condomínios fechados.<br><br>Para fazer isto, nada melhor do que criar uma linguagem comum entre todos estes elementos acessórios, além do poste: bancos, lixeiras, floreiras, pontos de ônibus e táxi, bicicletários entre outros.' UNION ALL
  SELECT 'sobre.group_title', 'Uma empresa do Grupo Technomast' UNION ALL
  SELECT 'sobre.group_text', 'A Esterni faz parte do Grupo Technomast, que atua há mais de 16 anos com a produção e fornecimento de postes e acessórios. Todos os produtos são produzidos com matérias de alta qualidade e especiais acabamentos para garantir resistência ao tempo para os nossos produtos. Se você precisa de um produto especial para a sua área urbana, entre em contato conosco e vamos desenvolver juntos!' UNION ALL
  SELECT 'sobre.group_cta', 'Conheça o Grupo Technomast' UNION ALL
  SELECT 'sobre.why_title', 'Por que escolher a Esterni?' UNION ALL
  SELECT 'sobre.feat1_title', 'Design' UNION ALL
  SELECT 'sobre.feat1_text', 'A Esterni nasceu com o intuito de produzir equipamentos com particular atenção ao design e desde o começo fez parceria com importantes escritórios de design no Brasil e no exterior, entre os quais destaca-se a Amowa Design, que foi a que primeiro acreditou no nosso projeto e desenvolveu as primeiras linhas. Além disso, sendo a Esterni uma empresa de origem italiana, continua aprimorando o próprio design, mantendo-se fortemente ligada à experiência italiana.' UNION ALL
  SELECT 'sobre.feat2_title', 'Exclusividade' UNION ALL
  SELECT 'sobre.feat2_text', 'Os produtos da Esterni nascem voltados a um mercado de alta qualidade e, consequentemente, a um público exclusivo que procura um produto de alto nível. Além disso, a Esterni disponibiliza para a própria clientela o know-how da equipe de design e engenharia para criar customizações ou até linhas de produtos totalmente exclusivas, caso o cliente tenha essa ideia e esse potencial.' UNION ALL
  SELECT 'sobre.feat3_title', 'Qualidade' UNION ALL
  SELECT 'sobre.feat3_text', 'A ESTERNI é uma marca da Technomast Indústria Metalúrgica Ltda, empresa com certificação ISO 9001 e reconhecida pelo alto padrão de qualidade dos próprios produtos. No mobiliário urbano são usados somente materiais e acabamentos de primeira qualidade: aço certificado, aço inox, madeira Cumaru (ideal para aplicações externas) e tintas de alta performance.' UNION ALL
  SELECT 'sobre.feat4_title', 'Durabilidade' UNION ALL
  SELECT 'sobre.feat4_text', 'Os produtos da ESTERNI são projetados e fabricados para alta durabilidade, sempre pensando em ambiente externo, salvo algum produto especificamente para interiores. Os tratamentos dados aos metais ou à madeira são estudados para proporcionar o máximo de vida útil ao equipamento, já que estará sujeito a condições adversas de clima por muitos anos.' UNION ALL
  SELECT 'sobre.feat5_title', 'Fabricado no Brasil' UNION ALL
  SELECT 'sobre.feat5_text', '100% dos produtos da ESTERNI são fabricados no Brasil, na nossa fábrica da região metropolitana de Curitiba, com as melhores tecnologias disponíveis no mercado. Isto permite prazos de entrega melhores e possíveis customizações sem depender de prazos de importação, e principalmente preços estáveis, sem depender de variações cambiais entre o momento do projeto e o momento da compra.' UNION ALL
  SELECT 'sobre.feat6_title', 'Atendimento' UNION ALL
  SELECT 'sobre.feat6_text', 'Todo o nosso corpo técnico e comercial está à total disposição do cliente para esclarecer dúvidas técnicas ou comerciais, assim como para aconselhar nas escolhas dos produtos mais indicados para as necessidades. Além disso, disponibilizamos nossa equipe para estudar customizações e produtos exclusivos. Nosso foco é a satisfação e a fidelização do cliente. Quem compra ESTERNI volta a comprar no próximo empreendimento!'
) x ON x.k = ui_strings.string_key ON DUPLICATE KEY UPDATE value = VALUES(value);

INSERT INTO ui_string_translations (ui_string_id, language_code, value)
SELECT id, 'en', v FROM ui_strings JOIN (
  SELECT 'sobre.page_title' k, 'Esterni Design and Urban Furniture' v UNION ALL
  SELECT 'sobre.intro_subtitle', 'Decorative Poles and Urban Furniture' UNION ALL
  SELECT 'sobre.intro_title', 'One single language for your urban space' UNION ALL
  SELECT 'sobre.intro_text', 'Esterni''s focus is urban beautification. Besides paying special attention to the quality and finish of our products, we design, in partnership with the best designers, product lines that coordinate poles and urban furniture, creating a unique language for squares, parks, waterfronts, boardwalks and, especially, gated communities.<br><br>To achieve this, nothing works better than creating a common language across all these accessory elements — besides the pole itself: benches, trash bins, planters, bus and taxi stops, bike racks, among others.' UNION ALL
  SELECT 'sobre.group_title', 'A Grupo Technomast company' UNION ALL
  SELECT 'sobre.group_text', 'Esterni is part of Grupo Technomast, which has been producing and supplying poles and accessories for over 16 years. All products are made with high-quality materials and special finishes to ensure durability over time. If you need a special product for your urban area, get in touch and let''s develop it together!' UNION ALL
  SELECT 'sobre.group_cta', 'Discover Grupo Technomast' UNION ALL
  SELECT 'sobre.why_title', 'Why choose Esterni?' UNION ALL
  SELECT 'sobre.feat1_title', 'Design' UNION ALL
  SELECT 'sobre.feat1_text', 'Esterni was born with a particular focus on design and, from the start, partnered with leading design studios in Brazil and abroad — most notably Amowa Design, the first to believe in our project and develop our earliest lines. As a company of Italian origin, Esterni keeps refining its design while staying strongly connected to Italian design heritage.' UNION ALL
  SELECT 'sobre.feat2_title', 'Exclusivity' UNION ALL
  SELECT 'sobre.feat2_text', 'Esterni products are built for a high-quality market and, consequently, for an exclusive audience looking for a premium product. We also make our design and engineering team''s know-how available to clients to create customizations, or even fully exclusive product lines, when the client has that vision and potential.' UNION ALL
  SELECT 'sobre.feat3_title', 'Quality' UNION ALL
  SELECT 'sobre.feat3_text', 'ESTERNI is a brand of Technomast Indústria Metalúrgica Ltda, an ISO 9001-certified company recognized for the high quality standard of its products. Only first-quality materials and finishes are used in our urban furniture: certified steel, stainless steel, Cumaru wood (ideal for outdoor use), and high-performance paints.' UNION ALL
  SELECT 'sobre.feat4_title', 'Durability' UNION ALL
  SELECT 'sobre.feat4_text', 'ESTERNI products are designed and manufactured for high durability, always with outdoor use in mind, unless a product is specifically designed for indoors. Metal and wood treatments are engineered to maximize the equipment''s useful life, since it will be exposed to harsh weather conditions for many years.' UNION ALL
  SELECT 'sobre.feat5_title', 'Made in Brazil' UNION ALL
  SELECT 'sobre.feat5_text', '100% of ESTERNI products are manufactured in Brazil, at our factory in the greater Curitiba area, using the best technology available on the market. This allows for better delivery times and possible customizations without depending on import lead times, and, above all, stable prices that don''t depend on exchange-rate swings between the project stage and the purchase.' UNION ALL
  SELECT 'sobre.feat6_title', 'Support' UNION ALL
  SELECT 'sobre.feat6_text', 'Our entire technical and sales team is fully available to clients to clarify technical or commercial questions, as well as to advise on the products best suited to their needs. We also make our team available to study customizations and exclusive products. Our focus is customer satisfaction and loyalty. Whoever buys ESTERNI comes back for their next project!'
) x ON x.k = ui_strings.string_key ON DUPLICATE KEY UPDATE value = VALUES(value);

INSERT INTO ui_string_translations (ui_string_id, language_code, value)
SELECT id, 'es', v FROM ui_strings JOIN (
  SELECT 'sobre.page_title' k, 'Esterni Diseño y Mobiliario Urbano' v UNION ALL
  SELECT 'sobre.intro_subtitle', 'Postes Decorativos y Mobiliario Urbano' UNION ALL
  SELECT 'sobre.intro_title', 'Un único lenguaje para tu espacio urbano' UNION ALL
  SELECT 'sobre.intro_text', 'El foco de Esterni es el embellecimiento urbano. Además de prestar especial atención a la calidad y el acabado de nuestros productos, diseñamos, en colaboración con los mejores diseñadores, líneas de productos que coordinan postes y mobiliario urbano, creando un lenguaje único para plazas, parques, paseos marítimos, paseos peatonales y, principalmente, condominios cerrados.<br><br>Para lograrlo, nada mejor que crear un lenguaje común entre todos estos elementos accesorios, además del poste: bancos, papeleras, jardineras, paradas de autobús y taxi, aparcabicicletas, entre otros.' UNION ALL
  SELECT 'sobre.group_title', 'Una empresa del Grupo Technomast' UNION ALL
  SELECT 'sobre.group_text', 'Esterni forma parte del Grupo Technomast, que opera desde hace más de 16 años en la producción y suministro de postes y accesorios. Todos los productos se fabrican con materiales de alta calidad y acabados especiales para garantizar resistencia al paso del tiempo. Si necesitas un producto especial para tu área urbana, ¡contáctanos y lo desarrollamos juntos!' UNION ALL
  SELECT 'sobre.group_cta', 'Conoce el Grupo Technomast' UNION ALL
  SELECT 'sobre.why_title', '¿Por qué elegir Esterni?' UNION ALL
  SELECT 'sobre.feat1_title', 'Diseño' UNION ALL
  SELECT 'sobre.feat1_text', 'Esterni nació con especial atención al diseño y, desde el principio, se asoció con importantes estudios de diseño en Brasil y en el exterior, entre los que destaca Amowa Design, el primero en creer en nuestro proyecto y desarrollar nuestras primeras líneas. Al ser una empresa de origen italiano, Esterni continúa perfeccionando su diseño manteniéndose fuertemente ligada a la experiencia italiana.' UNION ALL
  SELECT 'sobre.feat2_title', 'Exclusividad' UNION ALL
  SELECT 'sobre.feat2_text', 'Los productos de Esterni nacen orientados a un mercado de alta calidad y, en consecuencia, a un público exclusivo que busca un producto de alto nivel. Además, Esterni pone a disposición de sus clientes el know-how de su equipo de diseño e ingeniería para crear personalizaciones o incluso líneas de productos totalmente exclusivas, si el cliente tiene esa idea y ese potencial.' UNION ALL
  SELECT 'sobre.feat3_title', 'Calidad' UNION ALL
  SELECT 'sobre.feat3_text', 'ESTERNI es una marca de Technomast Indústria Metalúrgica Ltda, empresa certificada ISO 9001 y reconocida por el alto estándar de calidad de sus productos. En el mobiliario urbano se utilizan únicamente materiales y acabados de primera calidad: acero certificado, acero inoxidable, madera Cumaru (ideal para aplicaciones exteriores) y pinturas de alto rendimiento.' UNION ALL
  SELECT 'sobre.feat4_title', 'Durabilidad' UNION ALL
  SELECT 'sobre.feat4_text', 'Los productos de ESTERNI están diseñados y fabricados para una alta durabilidad, siempre pensando en el uso exterior, salvo algún producto específico para interiores. Los tratamientos aplicados a los metales o a la madera están estudiados para ofrecer la máxima vida útil del equipo, ya que estará expuesto a condiciones climáticas adversas durante muchos años.' UNION ALL
  SELECT 'sobre.feat5_title', 'Fabricado en Brasil' UNION ALL
  SELECT 'sobre.feat5_text', 'El 100% de los productos de ESTERNI se fabrican en Brasil, en nuestra fábrica de la región metropolitana de Curitiba, con la mejor tecnología disponible en el mercado. Esto permite mejores plazos de entrega y posibles personalizaciones sin depender de plazos de importación, y sobre todo precios estables, sin depender de variaciones cambiarias entre el momento del proyecto y el de la compra.' UNION ALL
  SELECT 'sobre.feat6_title', 'Atención' UNION ALL
  SELECT 'sobre.feat6_text', 'Todo nuestro equipo técnico y comercial está totalmente a disposición del cliente para aclarar dudas técnicas o comerciales, así como para asesorar en la elección de los productos más adecuados a sus necesidades. Además, ponemos a disposición nuestro equipo para estudiar personalizaciones y productos exclusivos. Nuestro enfoque es la satisfacción y la fidelización del cliente. ¡Quien compra ESTERNI vuelve a comprar en su próximo proyecto!'
) x ON x.k = ui_strings.string_key ON DUPLICATE KEY UPDATE value = VALUES(value);

INSERT INTO ui_string_translations (ui_string_id, language_code, value)
SELECT id, 'it', v FROM ui_strings JOIN (
  SELECT 'sobre.page_title' k, 'Esterni Design e Arredo Urbano' v UNION ALL
  SELECT 'sobre.intro_subtitle', 'Pali Decorativi e Arredo Urbano' UNION ALL
  SELECT 'sobre.intro_title', 'Un unico linguaggio per il tuo spazio urbano' UNION ALL
  SELECT 'sobre.intro_text', 'L''obiettivo di Esterni è l''abbellimento urbano. Oltre a prestare particolare attenzione alla qualità e alla finitura dei nostri prodotti, progettiamo, in collaborazione con i migliori designer, linee di prodotti che coordinano pali e arredo urbano, creando un linguaggio unico per piazze, parchi, lungomari, passeggiate e, soprattutto, condomini privati.<br><br>Per raggiungere questo obiettivo, niente di meglio che creare un linguaggio comune tra tutti questi elementi accessori, oltre al palo: panchine, cestini, fioriere, fermate di autobus e taxi, portabiciclette, tra gli altri.' UNION ALL
  SELECT 'sobre.group_title', 'Un''azienda del Gruppo Technomast' UNION ALL
  SELECT 'sobre.group_text', 'Esterni fa parte del Gruppo Technomast, che opera da oltre 16 anni nella produzione e fornitura di pali e accessori. Tutti i prodotti sono realizzati con materiali di alta qualità e finiture speciali per garantire resistenza nel tempo. Se hai bisogno di un prodotto speciale per la tua area urbana, contattaci e lo sviluppiamo insieme!' UNION ALL
  SELECT 'sobre.group_cta', 'Scopri il Gruppo Technomast' UNION ALL
  SELECT 'sobre.why_title', 'Perché scegliere Esterni?' UNION ALL
  SELECT 'sobre.feat1_title', 'Design' UNION ALL
  SELECT 'sobre.feat1_text', 'Esterni è nata con particolare attenzione al design e, fin dall''inizio, ha collaborato con importanti studi di design in Brasile e all''estero, tra cui spicca Amowa Design, la prima a credere nel nostro progetto e a sviluppare le nostre prime linee. Essendo un''azienda di origine italiana, Esterni continua a perfezionare il proprio design mantenendo un forte legame con l''esperienza italiana.' UNION ALL
  SELECT 'sobre.feat2_title', 'Esclusività' UNION ALL
  SELECT 'sobre.feat2_text', 'I prodotti Esterni nascono orientati a un mercato di alta qualità e, di conseguenza, a un pubblico esclusivo che cerca un prodotto di alto livello. Inoltre, Esterni mette a disposizione dei propri clienti il know-how del team di design e ingegneria per creare personalizzazioni o persino linee di prodotti completamente esclusive, quando il cliente ha questa visione e questo potenziale.' UNION ALL
  SELECT 'sobre.feat3_title', 'Qualità' UNION ALL
  SELECT 'sobre.feat3_text', 'ESTERNI è un marchio di Technomast Indústria Metalúrgica Ltda, azienda certificata ISO 9001 e riconosciuta per l''alto standard qualitativo dei propri prodotti. Nell''arredo urbano vengono utilizzati solo materiali e finiture di prima qualità: acciaio certificato, acciaio inossidabile, legno Cumaru (ideale per applicazioni esterne) e vernici ad alte prestazioni.' UNION ALL
  SELECT 'sobre.feat4_title', 'Durabilità' UNION ALL
  SELECT 'sobre.feat4_text', 'I prodotti ESTERNI sono progettati e realizzati per un''elevata durabilità, sempre pensando all''uso esterno, salvo prodotti specifici per interni. I trattamenti applicati ai metalli o al legno sono studiati per massimizzare la vita utile dell''attrezzatura, dato che sarà esposta a condizioni climatiche avverse per molti anni.' UNION ALL
  SELECT 'sobre.feat5_title', 'Prodotto in Brasile' UNION ALL
  SELECT 'sobre.feat5_text', 'Il 100% dei prodotti ESTERNI è fabbricato in Brasile, nel nostro stabilimento nell''area metropolitana di Curitiba, con le migliori tecnologie disponibili sul mercato. Questo consente tempi di consegna migliori e possibili personalizzazioni senza dipendere dai tempi di importazione, e soprattutto prezzi stabili, senza dipendere dalle variazioni di cambio tra il momento del progetto e quello dell''acquisto.' UNION ALL
  SELECT 'sobre.feat6_title', 'Assistenza' UNION ALL
  SELECT 'sobre.feat6_text', 'Tutto il nostro team tecnico e commerciale è pienamente a disposizione del cliente per chiarire dubbi tecnici o commerciali, oltre che per consigliare i prodotti più adatti alle proprie esigenze. Mettiamo inoltre a disposizione il nostro team per studiare personalizzazioni e prodotti esclusivi. Il nostro obiettivo è la soddisfazione e la fidelizzazione del cliente. Chi acquista ESTERNI torna ad acquistare per il prossimo progetto!'
) x ON x.k = ui_strings.string_key ON DUPLICATE KEY UPDATE value = VALUES(value);
