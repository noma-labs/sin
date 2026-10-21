-- Reverse the migrations

-- The deleted celibe/nubile/sposato/vedovo rows cannot be rebuilt: restore them from a database dump taken before the migration.
INSERT INTO stati (stato, nome) VALUES
    ('CDE', 'Celibe di elezione'),
    ('CEL', 'Celibe'),
    ('NUB', 'Nubile');

UPDATE stati SET nome = 'SPOSATO/A' WHERE stato = 'SPO';
UPDATE stati SET nome = 'VEDOVO/A' WHERE stato = 'VED';
