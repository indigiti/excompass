CREATE TABLE cities (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(120) NOT NULL UNIQUE,
    name VARCHAR(190) NOT NULL,
    state VARCHAR(190) NULL,
    country VARCHAR(120) NOT NULL DEFAULT 'India',
    latitude DECIMAL(10,7) NULL,
    longitude DECIMAL(10,7) NULL,
    status ENUM('draft','active','archived') NOT NULL DEFAULT 'draft',
    is_default TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_city_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE verticals (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(120) NOT NULL UNIQUE,
    name VARCHAR(190) NOT NULL,
    icon_key VARCHAR(80) NOT NULL,
    accent VARCHAR(20) NOT NULL,
    prompt VARCHAR(255) NULL,
    status ENUM('draft','active','archived') NOT NULL DEFAULT 'draft',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE localities (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    city_id BIGINT UNSIGNED NOT NULL,
    slug VARCHAR(160) NOT NULL,
    name VARCHAR(190) NOT NULL,
    latitude DECIMAL(10,7) NULL,
    longitude DECIMAL(10,7) NULL,
    status ENUM('draft','active','archived') NOT NULL DEFAULT 'draft',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_locality_city_slug (city_id, slug),
    KEY idx_locality_city_status (city_id, status),
    CONSTRAINT fk_locality_city FOREIGN KEY (city_id) REFERENCES cities(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE entities (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    city_id BIGINT UNSIGNED NOT NULL,
    vertical_id BIGINT UNSIGNED NOT NULL,
    primary_locality_id BIGINT UNSIGNED NULL,
    slug VARCHAR(190) NOT NULL,
    name VARCHAR(255) NOT NULL,
    status ENUM('draft','review','approved','published','archived') NOT NULL DEFAULT 'draft',
    summary TEXT NULL,
    website_url VARCHAR(2048) NULL,
    phone VARCHAR(60) NULL,
    email VARCHAR(190) NULL,
    commercial_status ENUM('editorial','featured','sponsored') NOT NULL DEFAULT 'editorial',
    published_at DATETIME NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_entity_city_vertical_slug (city_id, vertical_id, slug),
    KEY idx_entity_city_status (city_id, status),
    KEY idx_entity_locality (primary_locality_id),
    CONSTRAINT fk_entity_city FOREIGN KEY (city_id) REFERENCES cities(id),
    CONSTRAINT fk_entity_vertical FOREIGN KEY (vertical_id) REFERENCES verticals(id),
    CONSTRAINT fk_entity_locality FOREIGN KEY (primary_locality_id) REFERENCES localities(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE attribute_definitions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    vertical_id BIGINT UNSIGNED NOT NULL,
    attribute_key VARCHAR(120) NOT NULL,
    label VARCHAR(190) NOT NULL,
    data_type ENUM('text','number','boolean','date','json','url') NOT NULL DEFAULT 'text',
    is_filterable TINYINT(1) NOT NULL DEFAULT 0,
    sort_order INT NOT NULL DEFAULT 0,
    UNIQUE KEY uq_attribute_definition (vertical_id, attribute_key),
    CONSTRAINT fk_attribute_vertical FOREIGN KEY (vertical_id) REFERENCES verticals(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE entity_attributes (
    entity_id BIGINT UNSIGNED NOT NULL,
    attribute_definition_id BIGINT UNSIGNED NOT NULL,
    value_text TEXT NULL,
    value_number DECIMAL(18,4) NULL,
    value_boolean TINYINT(1) NULL,
    value_date DATE NULL,
    value_json JSON NULL,
    PRIMARY KEY (entity_id, attribute_definition_id),
    CONSTRAINT fk_entity_attribute_entity FOREIGN KEY (entity_id) REFERENCES entities(id) ON DELETE CASCADE,
    CONSTRAINT fk_entity_attribute_definition FOREIGN KEY (attribute_definition_id) REFERENCES attribute_definitions(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE evidence (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    entity_id BIGINT UNSIGNED NOT NULL,
    evidence_type ENUM('url','document','editorial_note','data_source') NOT NULL,
    title VARCHAR(255) NOT NULL,
    source_url VARCHAR(2048) NULL,
    source_date DATE NULL,
    verification_status ENUM('pending','verified','rejected','expired') NOT NULL DEFAULT 'pending',
    verified_by BIGINT UNSIGNED NULL,
    verified_at DATETIME NULL,
    notes TEXT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_evidence_entity_status (entity_id, verification_status),
    CONSTRAINT fk_evidence_entity FOREIGN KEY (entity_id) REFERENCES entities(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE ranking_criteria (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    vertical_id BIGINT UNSIGNED NOT NULL,
    criterion_key VARCHAR(120) NOT NULL,
    label VARCHAR(190) NOT NULL,
    maximum_score DECIMAL(8,2) NOT NULL,
    weight DECIMAL(8,2) NOT NULL,
    evidence_required TINYINT(1) NOT NULL DEFAULT 1,
    guidance TEXT NULL,
    sort_order INT NOT NULL DEFAULT 0,
    UNIQUE KEY uq_ranking_criterion (vertical_id, criterion_key),
    CONSTRAINT fk_criterion_vertical FOREIGN KEY (vertical_id) REFERENCES verticals(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE score_versions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    entity_id BIGINT UNSIGNED NOT NULL,
    version VARCHAR(80) NOT NULL,
    total_score DECIMAL(8,2) NOT NULL DEFAULT 0,
    status ENUM('draft','submitted','approved','superseded') NOT NULL DEFAULT 'draft',
    reviewer_id BIGINT UNSIGNED NULL,
    reviewed_at DATETIME NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_entity_score_version (entity_id, version),
    CONSTRAINT fk_score_version_entity FOREIGN KEY (entity_id) REFERENCES entities(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE score_items (
    score_version_id BIGINT UNSIGNED NOT NULL,
    ranking_criterion_id BIGINT UNSIGNED NOT NULL,
    raw_score DECIMAL(8,2) NOT NULL,
    weighted_score DECIMAL(8,2) NOT NULL,
    rationale TEXT NULL,
    PRIMARY KEY (score_version_id, ranking_criterion_id),
    CONSTRAINT fk_score_item_version FOREIGN KEY (score_version_id) REFERENCES score_versions(id) ON DELETE CASCADE,
    CONSTRAINT fk_score_item_criterion FOREIGN KEY (ranking_criterion_id) REFERENCES ranking_criteria(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE ranking_versions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    city_id BIGINT UNSIGNED NOT NULL,
    locality_id BIGINT UNSIGNED NULL,
    vertical_id BIGINT UNSIGNED NOT NULL,
    scope_key VARCHAR(190) NOT NULL DEFAULT 'city',
    version VARCHAR(80) NOT NULL,
    title VARCHAR(255) NOT NULL,
    status ENUM('draft','published','superseded') NOT NULL DEFAULT 'draft',
    published_at DATETIME NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_ranking_scope_version (city_id, vertical_id, scope_key, version),
    KEY idx_ranking_city_vertical_status (city_id, vertical_id, status),
    CONSTRAINT fk_ranking_version_city FOREIGN KEY (city_id) REFERENCES cities(id),
    CONSTRAINT fk_ranking_version_locality FOREIGN KEY (locality_id) REFERENCES localities(id),
    CONSTRAINT fk_ranking_version_vertical FOREIGN KEY (vertical_id) REFERENCES verticals(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE ranking_entries (
    ranking_version_id BIGINT UNSIGNED NOT NULL,
    entity_id BIGINT UNSIGNED NOT NULL,
    score_version_id BIGINT UNSIGNED NOT NULL,
    rank_position INT UNSIGNED NOT NULL,
    editorial_note TEXT NULL,
    PRIMARY KEY (ranking_version_id, entity_id),
    UNIQUE KEY uq_ranking_position (ranking_version_id, rank_position),
    CONSTRAINT fk_ranking_entry_version FOREIGN KEY (ranking_version_id) REFERENCES ranking_versions(id) ON DELETE CASCADE,
    CONSTRAINT fk_ranking_entry_entity FOREIGN KEY (entity_id) REFERENCES entities(id),
    CONSTRAINT fk_ranking_entry_score FOREIGN KEY (score_version_id) REFERENCES score_versions(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE roles (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(80) NOT NULL UNIQUE,
    name VARCHAR(120) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(190) NOT NULL,
    email VARCHAR(190) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    status ENUM('active','disabled') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE role_user (
    role_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (role_id, user_id),
    CONSTRAINT fk_role_user_role FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
    CONSTRAINT fk_role_user_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE leads (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    city_id BIGINT UNSIGNED NOT NULL,
    locality_id BIGINT UNSIGNED NULL,
    entity_id BIGINT UNSIGNED NULL,
    lead_type ENUM('enquiry','callback','visit','appointment','sponsored-enquiry') NOT NULL DEFAULT 'enquiry',
    name VARCHAR(190) NOT NULL,
    phone VARCHAR(60) NULL,
    email VARCHAR(190) NULL,
    consent_at DATETIME NULL,
    status ENUM('new','contacted','qualified','appointment','converted','closed','spam') NOT NULL DEFAULT 'new',
    source_url VARCHAR(2048) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_lead_city_status_created (city_id, status, created_at),
    CONSTRAINT fk_lead_city FOREIGN KEY (city_id) REFERENCES cities(id),
    CONSTRAINT fk_lead_locality FOREIGN KEY (locality_id) REFERENCES localities(id) ON DELETE SET NULL,
    CONSTRAINT fk_lead_entity FOREIGN KEY (entity_id) REFERENCES entities(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE audit_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL,
    action VARCHAR(120) NOT NULL,
    subject_type VARCHAR(120) NOT NULL,
    subject_id BIGINT UNSIGNED NULL,
    before_json JSON NULL,
    after_json JSON NULL,
    ip_hash CHAR(64) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_audit_subject (subject_type, subject_id),
    KEY idx_audit_created (created_at),
    CONSTRAINT fk_audit_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
