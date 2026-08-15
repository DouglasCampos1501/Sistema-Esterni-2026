ALTER TABLE contact_messages ADD COLUMN company VARCHAR(190) NULL AFTER phone;

INSERT INTO contact_form_fields (field_key, field_type, label, placeholder, is_required, sort_order) VALUES
('company', 'text', 'Empresa', 'Nome da empresa em que trabalha', 0, 5);
