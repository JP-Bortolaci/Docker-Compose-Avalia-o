CREATE TABLE users (
    id VARCHAR(50) PRIMARY KEY,
    name VARCHAR(255),
    email VARCHAR(255)
);

INSERT INTO users (id, name, email) VALUES
('1', 'João', 'joao@exemplo.com'),
('2', 'Maria', 'maria@exemplo.com');