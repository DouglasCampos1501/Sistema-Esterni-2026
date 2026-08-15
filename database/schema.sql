-- Esterni — schema inicial
-- Reaproveita o "motor" genérico já validado no projeto Technomast
-- (auth, mídia, i18n, activity log, menu, contato, configurações) e
-- adiciona o modelo de catálogo próprio do Esterni: produtos cruzados
-- em duas taxonomias — Linhas (coleções de design: Misan, Vega, S-Park...)
-- e Tipos (categoria funcional: banco, lixeira, bicicletário...).

SET NAMES utf8mb4;

-- ============================================================
-- Núcleo administrativo (idêntico ao Technomast)
-- ============================================================

CREATE TABLE admin_users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(190) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    last_login_at DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Registro de tentativas de login malsucedidas, para bloqueio temporário por
-- força bruta (ver login_is_locked_out()/register_failed_login() em includes/auth.php).
CREATE TABLE login_attempts (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ip_address VARCHAR(45) NOT NULL,
    email VARCHAR(190) NULL,
    attempted_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_ip_time (ip_address, attempted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Log de atividades do dashboard (quem fez o quê e quando)
CREATE TABLE activity_log (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    admin_user_id INT UNSIGNED NULL,
    admin_name VARCHAR(120) NULL,
    action VARCHAR(20) NOT NULL,       -- create | update | delete | login
    entity_type VARCHAR(50) NOT NULL,  -- 'page', 'product', 'product_line', 'product_type', 'post', 'media', 'user', 'menu_item'...
    entity_id INT UNSIGNED NULL,
    entity_label VARCHAR(255) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_user_id) REFERENCES admin_users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Biblioteca central de mídia, reaproveitada por páginas, produtos, notícias etc.
CREATE TABLE media (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    filename VARCHAR(255) NOT NULL,
    path VARCHAR(500) NOT NULL,
    alt_text VARCHAR(255) NULL,
    description TEXT NULL,
    mime_type VARCHAR(100) NOT NULL,
    size_bytes INT UNSIGNED NOT NULL DEFAULT 0,
    width SMALLINT UNSIGNED NULL,
    height SMALLINT UNSIGNED NULL,
    uploaded_by INT UNSIGNED NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (uploaded_by) REFERENCES admin_users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- Idiomas / i18n (idêntico ao Technomast)
-- ============================================================

CREATE TABLE languages (
    code VARCHAR(5) NOT NULL PRIMARY KEY,   -- 'pt-BR', 'en', 'es', 'it'
    name VARCHAR(50) NOT NULL,
    flag_emoji VARCHAR(10) NOT NULL,
    flag_image VARCHAR(255) NULL,
    is_default TINYINT(1) NOT NULL DEFAULT 0,
    sort_order SMALLINT NOT NULL DEFAULT 0,
    active TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO languages (code, name, flag_emoji, flag_image, is_default, sort_order, active) VALUES
('pt-BR', 'Português', '🇧🇷', '/assets/img/flags/br.png', 1, 0, 1),
('en', 'English', '🇺🇸', '/assets/img/flags/us.png', 0, 1, 1),
('es', 'Español', '🇪🇸', '/assets/img/flags/es.png', 0, 2, 1),
('it', 'Italiano', '🇮🇹', '/assets/img/flags/it.png', 0, 3, 1);

-- Textos fixos do site (menu, rodapé, botões, avisos...)
CREATE TABLE ui_strings (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    string_key VARCHAR(100) NOT NULL UNIQUE,
    group_name VARCHAR(50) NOT NULL,
    description VARCHAR(255) NULL,
    sort_order SMALLINT NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE ui_string_translations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ui_string_id INT UNSIGNED NOT NULL,
    language_code VARCHAR(5) NOT NULL,
    value TEXT NULL,
    UNIQUE KEY uniq_string_lang (ui_string_id, language_code),
    FOREIGN KEY (ui_string_id) REFERENCES ui_strings(id) ON DELETE CASCADE,
    FOREIGN KEY (language_code) REFERENCES languages(code) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- Páginas institucionais (Sobre, Política de Privacidade, Contato...)
-- ============================================================

CREATE TABLE pages (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(190) NOT NULL UNIQUE,
    title VARCHAR(255) NOT NULL,
    content MEDIUMTEXT NULL,
    featured_image_id INT UNSIGNED NULL,
    meta_title VARCHAR(255) NULL,
    meta_description VARCHAR(500) NULL,
    og_image_id INT UNSIGNED NULL,
    status ENUM('draft','published') NOT NULL DEFAULT 'draft',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (featured_image_id) REFERENCES media(id) ON DELETE SET NULL,
    FOREIGN KEY (og_image_id) REFERENCES media(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE page_translations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    page_id INT UNSIGNED NOT NULL,
    language_code VARCHAR(5) NOT NULL,
    title VARCHAR(255) NULL,
    content MEDIUMTEXT NULL,
    meta_title VARCHAR(255) NULL,
    meta_description VARCHAR(500) NULL,
    UNIQUE KEY uniq_page_lang (page_id, language_code),
    FOREIGN KEY (page_id) REFERENCES pages(id) ON DELETE CASCADE,
    FOREIGN KEY (language_code) REFERENCES languages(code) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- Catálogo de produtos — modelo próprio do Esterni.
-- Cada produto pertence a UMA Linha (coleção de design: Misan, Vega...)
-- e UM Tipo (categoria funcional: banco, lixeira, bicicletário...).
-- As páginas /linhas/{slug}/ e /tipos/{slug}/ listam os produtos que
-- cruzam com aquela taxonomia.
-- ============================================================

CREATE TABLE product_lines (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(190) NOT NULL UNIQUE,
    name VARCHAR(150) NOT NULL,
    description TEXT NULL,
    featured_image_id INT UNSIGNED NULL,     -- imagem de capa/banner da linha
    sort_order SMALLINT NOT NULL DEFAULT 0,
    active TINYINT(1) NOT NULL DEFAULT 1,
    FOREIGN KEY (featured_image_id) REFERENCES media(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE product_line_translations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    line_id INT UNSIGNED NOT NULL,
    language_code VARCHAR(5) NOT NULL,
    name VARCHAR(150) NULL,
    description TEXT NULL,
    UNIQUE KEY uniq_line_lang (line_id, language_code),
    FOREIGN KEY (line_id) REFERENCES product_lines(id) ON DELETE CASCADE,
    FOREIGN KEY (language_code) REFERENCES languages(code) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE product_types (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(190) NOT NULL UNIQUE,
    name VARCHAR(150) NOT NULL,
    icon_image_id INT UNSIGNED NULL,         -- ícone usado no menu/listagem de tipos
    sort_order SMALLINT NOT NULL DEFAULT 0,
    active TINYINT(1) NOT NULL DEFAULT 1,
    FOREIGN KEY (icon_image_id) REFERENCES media(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE product_type_translations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    type_id INT UNSIGNED NOT NULL,
    language_code VARCHAR(5) NOT NULL,
    name VARCHAR(150) NULL,
    UNIQUE KEY uniq_type_lang (type_id, language_code),
    FOREIGN KEY (type_id) REFERENCES product_types(id) ON DELETE CASCADE,
    FOREIGN KEY (language_code) REFERENCES languages(code) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE products (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    line_id INT UNSIGNED NOT NULL,
    type_id INT UNSIGNED NOT NULL,
    slug VARCHAR(190) NOT NULL UNIQUE,
    name VARCHAR(255) NOT NULL,
    summary VARCHAR(500) NULL,
    description MEDIUMTEXT NULL,             -- material, tratamento de superfície, fixação etc.
    featured_image_id INT UNSIGNED NULL,
    dimensions_image_id INT UNSIGNED NULL,   -- desenho técnico "Dimensões e Medidas"
    meta_title VARCHAR(255) NULL,
    meta_description VARCHAR(500) NULL,
    status ENUM('draft','published') NOT NULL DEFAULT 'draft',
    sort_order SMALLINT NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (line_id) REFERENCES product_lines(id) ON DELETE RESTRICT,
    FOREIGN KEY (type_id) REFERENCES product_types(id) ON DELETE RESTRICT,
    FOREIGN KEY (featured_image_id) REFERENCES media(id) ON DELETE SET NULL,
    FOREIGN KEY (dimensions_image_id) REFERENCES media(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE product_translations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id INT UNSIGNED NOT NULL,
    language_code VARCHAR(5) NOT NULL,
    name VARCHAR(255) NULL,
    summary VARCHAR(500) NULL,
    description MEDIUMTEXT NULL,
    meta_title VARCHAR(255) NULL,
    meta_description VARCHAR(500) NULL,
    UNIQUE KEY uniq_product_lang (product_id, language_code),
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (language_code) REFERENCES languages(code) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE product_images (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id INT UNSIGNED NOT NULL,
    media_id INT UNSIGNED NOT NULL,
    sort_order SMALLINT NOT NULL DEFAULT 0,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (media_id) REFERENCES media(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- Notícias
-- ============================================================

CREATE TABLE posts (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(190) NOT NULL UNIQUE,
    title VARCHAR(255) NOT NULL,
    excerpt VARCHAR(500) NULL,
    content MEDIUMTEXT NULL,
    featured_image_id INT UNSIGNED NULL,
    meta_title VARCHAR(255) NULL,
    meta_description VARCHAR(500) NULL,
    status ENUM('draft','published') NOT NULL DEFAULT 'draft',
    published_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (featured_image_id) REFERENCES media(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE post_translations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    post_id INT UNSIGNED NOT NULL,
    language_code VARCHAR(5) NOT NULL,
    title VARCHAR(255) NULL,
    excerpt VARCHAR(500) NULL,
    content MEDIUMTEXT NULL,
    meta_title VARCHAR(255) NULL,
    meta_description VARCHAR(500) NULL,
    UNIQUE KEY uniq_post_lang (post_id, language_code),
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (language_code) REFERENCES languages(code) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- Menu do site
-- ============================================================

CREATE TABLE menu_items (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    parent_id INT UNSIGNED NULL,
    link_type ENUM('custom','page','logo') NOT NULL DEFAULT 'custom',
    url VARCHAR(500) NULL,
    page_slug VARCHAR(190) NULL,
    image_path VARCHAR(500) NULL,
    open_new_tab TINYINT(1) NOT NULL DEFAULT 0,
    sort_order SMALLINT NOT NULL DEFAULT 0,
    active TINYINT(1) NOT NULL DEFAULT 1,
    FOREIGN KEY (parent_id) REFERENCES menu_items(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE menu_item_translations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    menu_item_id INT UNSIGNED NOT NULL,
    language_code VARCHAR(5) NOT NULL,
    label VARCHAR(150) NULL,
    UNIQUE KEY uniq_menu_lang (menu_item_id, language_code),
    FOREIGN KEY (menu_item_id) REFERENCES menu_items(id) ON DELETE CASCADE,
    FOREIGN KEY (language_code) REFERENCES languages(code) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- Configurações globais, redirects, formulário de contato
-- ============================================================

CREATE TABLE settings (
    setting_key VARCHAR(100) NOT NULL PRIMARY KEY,
    setting_value TEXT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE redirects (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    from_path VARCHAR(500) NOT NULL UNIQUE,
    to_path VARCHAR(500) NOT NULL,
    status_code SMALLINT NOT NULL DEFAULT 301
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE contact_form_fields (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    field_key VARCHAR(40) NOT NULL UNIQUE,
    field_type ENUM('text','email','tel','textarea') NOT NULL DEFAULT 'text',
    label VARCHAR(150) NOT NULL,
    placeholder VARCHAR(200) NULL,
    is_required TINYINT(1) NOT NULL DEFAULT 0,
    sort_order INT NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE contact_form_field_translations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    field_id INT UNSIGNED NOT NULL,
    language_code VARCHAR(5) NOT NULL,
    label VARCHAR(150) NULL,
    placeholder VARCHAR(200) NULL,
    UNIQUE KEY uniq_field_lang (field_id, language_code),
    FOREIGN KEY (field_id) REFERENCES contact_form_fields(id) ON DELETE CASCADE,
    FOREIGN KEY (language_code) REFERENCES languages(code) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE contact_messages (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(190) NOT NULL,
    phone VARCHAR(40) NULL,
    company VARCHAR(190) NULL,
    message TEXT NOT NULL,
    language_code VARCHAR(5) NOT NULL DEFAULT 'pt-BR',
    email_sent TINYINT(1) NOT NULL DEFAULT 0,
    ip_address VARCHAR(45) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_ip_time (ip_address, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- Seed inicial
-- ============================================================

INSERT INTO settings (setting_key, setting_value) VALUES
('site_name', 'Esterni'),
('phone', ''),
('address', ''),
('whatsapp', ''),
('footer_text', ''),
('contact_recipient_email', ''),
('social_facebook', ''),
('social_instagram', '');

INSERT INTO contact_form_fields (field_key, field_type, label, placeholder, is_required, sort_order) VALUES
('name', 'text', 'Nome', 'Seu nome completo', 1, 1),
('email', 'email', 'Email', 'Seu melhor e-mail', 1, 2),
('phone', 'tel', 'Telefone', '(00) 0000-0000', 0, 3),
('company', 'text', 'Empresa', 'Nome da empresa em que trabalha', 0, 4),
('message', 'textarea', 'Mensagem', '', 1, 5);

-- ============================================================
-- Widget flutuante de WhatsApp (botão no canto da tela, com balões
-- de "assunto" configuráveis — cada um com seu próprio número e
-- mensagem pré-preenchida). Editável em /admin/whatsapp/.
-- ============================================================

CREATE TABLE whatsapp_options (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    label VARCHAR(100) NOT NULL,          -- texto do balão, ex: "Atendimento / Informações"
    message TEXT NOT NULL,                -- mensagem pré-preenchida ao abrir o WhatsApp
    phone_number VARCHAR(20) NOT NULL,    -- só dígitos, com DDI, ex: 5541995967801
    sort_order SMALLINT NOT NULL DEFAULT 0,
    active TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE whatsapp_option_translations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    whatsapp_option_id INT UNSIGNED NOT NULL,
    language_code VARCHAR(5) NOT NULL,
    label VARCHAR(100) NULL,
    message TEXT NULL,
    UNIQUE KEY uniq_option_lang (whatsapp_option_id, language_code),
    FOREIGN KEY (whatsapp_option_id) REFERENCES whatsapp_options(id) ON DELETE CASCADE,
    FOREIGN KEY (language_code) REFERENCES languages(code) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO whatsapp_options (label, message, phone_number, sort_order, active) VALUES
('Atendimento / Informações', 'Olá, gostaria de falar com um consultor da Esterni.', '5541995967801', 1, 1),
('Assuntos financeiros', 'Olá, gostaria de falar com o departamento financeiro da Esterni.', '5541995967801', 2, 1),
('Projetos', 'Olá, gostaria de falar com a Esterni sobre projetos.', '5541995967801', 3, 1);

-- ============================================================
-- Newsletter (formulário "Receba por e-mail" do rodapé)
-- ============================================================

CREATE TABLE newsletter_subscribers (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(190) NOT NULL UNIQUE,
    language_code VARCHAR(5) NOT NULL DEFAULT 'pt-BR',
    ip_address VARCHAR(45) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
