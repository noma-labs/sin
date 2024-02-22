<?php

namespace Domain\Photo\Actions;

use Illuminate\Support\Facades\DB;

class ExtractSubjectsFromPhotoAction
{
    public function execute(): int
    {
        // FIXME: sh: 1: Syntax error: "(" unexpected
        // TODO: on conflict do nothing
        DB::connection('db_foto')->raw( `INSERT INTO foto_persone (photo_id, persona_nome)
SELECT uid, SUBSTRING_INDEX(SUBSTRING_INDEX(names, ',', numbers.n), ',', -1) AS name
FROM (
    SELECT uid, subject AS names from photos
) AS t
JOIN (
    SELECT (a.N + b.N * 10 + 1) AS n
    FROM
        (SELECT 0 AS N UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS a
        ,(SELECT 0 AS N UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS b
    ORDER BY n
) AS numbers
ON CHAR_LENGTH(names) - CHAR_LENGTH(REPLACE(names, ',', '')) >= numbers.n - 1; `);

        return 0;
    }
}
