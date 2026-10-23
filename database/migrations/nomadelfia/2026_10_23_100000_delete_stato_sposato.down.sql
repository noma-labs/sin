-- Reverse the migrations

-- The deleted persone_stati cannot be rebuilt: restore them from a database dump taken before the migration.
INSERT INTO stati (stato, nome) VALUES ('SPO', 'SPOSATO/A');
