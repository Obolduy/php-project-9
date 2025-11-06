CREATE TABLE IF NOT EXISTS urls (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_urls_name ON urls(name);

CREATE TABLE IF NOT EXISTS urls_analyses (
    id BIGSERIAL PRIMARY KEY,
    url_id BIGINT NOT NULL,
    response_code INTEGER,
    h1 VARCHAR(255),
    title VARCHAR(255),
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (url_id) REFERENCES urls(id) ON DELETE CASCADE
);

CREATE INDEX IF NOT EXISTS idx_urls_analyses_url_id ON urls_analyses(url_id);

CREATE INDEX IF NOT EXISTS idx_urls_analyses_created_at ON urls_analyses(created_at DESC);
