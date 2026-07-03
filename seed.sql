INSERT INTO usuarios (username, password_hash, rol)
VALUES ('admin', '$2y$10$U2lmZWU0eHl6MTIzNDU2N7O0WubwWfT3LhF6KveI3O/4e2Yv9S2by', 'ADMIN')
    ON CONFLICT (username) DO NOTHING;
