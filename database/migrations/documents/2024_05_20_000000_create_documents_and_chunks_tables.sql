CREATE TABLE embeddings (
    doc_id TEXT,
    chunk_index INT UNSIGNED NOT NULL,
    embedding VECTOR(834) NOT NULL,
    VECTOR INDEX (embedding) M=8 DISTANCE=cosine
);
