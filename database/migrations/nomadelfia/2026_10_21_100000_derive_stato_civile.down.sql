-- Reverse the migrations

-- The deleted celibe/nubile/sposato/vedovo rows cannot be rebuilt: restore them from a database dump taken before the migration.
INSERT INTO stati (stato, nome) VALUES
    ('CDE', 'Celibe di elezione'),
    ('CEL', 'Celibe'),
    ('NUB', 'Nubile'),
    ('SPO', 'SPOSATO/A'),
    ('VED', 'VEDOVO/A');
