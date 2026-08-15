CREATE TABLE IF NOT EXISTS whatsapp_options (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    label VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    phone_number VARCHAR(20) NOT NULL,
    sort_order SMALLINT NOT NULL DEFAULT 0,
    active TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS whatsapp_option_translations (
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
