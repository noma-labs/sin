<?php

declare(strict_types=1);

namespace App\Scuola\DataTransferObjects;

use App\Scuola\Models\Anno;
use DateTimeImmutable;
use Domain\Nomadelfia\Persona\Models\Persona;
use Illuminate\Support\Facades\DB;

final class AnnoScolasticoData
{
    public function __construct(
        public int $id,
        public int $totalStudents,
        public AnnoScolastico $as,
        public ?Persona $responsabile,
        public DateTimeImmutable $data_inizio,
        public ?string $descrizione,
        public CicloScolastico $prescuola,
        public CicloScolastico $elementari,
        public CicloScolastico $medie,
        public CicloScolastico $superiori,
        public CicloScolastico $universita,
    ) {}

    public static function FromDatabase(Anno $anno): self
    {
        $results = DB::connection('db_scuola')
            ->table('tipo')
            ->join('classi', 'classi.tipo_id', '=', 'tipo.id')
            ->join('alunni_classi', 'alunni_classi.classe_id', '=', 'classi.id')
            ->select(
                'tipo.ciclo',
                'tipo.nome',
                DB::raw('GROUP_CONCAT(DISTINCT alunni_classi.persona_id ORDER BY alunni_classi.persona_id SEPARATOR ",") as alunni'),
                DB::raw('COUNT(DISTINCT alunni_classi.persona_id) as total_alunni')
            )
            ->where('classi.anno_id', $anno->id)
            ->groupBy('tipo.ciclo', 'tipo.nome')
            ->orderBy('tipo.ciclo')
            ->orderBy('tipo.nome')
            ->get();

        $cicliScolastici = [];
        $totAlunni = 0;
        foreach ($results as $result) {
            $totAlunni += $result->total_alunni;
            $alunniIds = array_map('intval', explode(',', $result->alunni));
            $classe = new Classe($result->nome, $alunniIds);

            if (! isset($cicliScolastici[$result->ciclo])) {
                $cicliScolastici[$result->ciclo] = new CicloScolastico($result->ciclo, []);
            }
            $ciclo = $cicliScolastici[$result->ciclo];
            $ciclo->classi[] = $classe;
            $ciclo->alunniCount += count($alunniIds);
        }

        return new self(
            (int) $anno->id,
            $totAlunni,
            AnnoScolastico::fromString($anno->scolastico),
            $anno->responsabile ? Persona::findOrFail($anno->responsabile) : null,
            new DateTimeImmutable($anno->data_inizio),
            $anno->descrizione,
            $cicliScolastici['prescuola'] ?? new CicloScolastico('prescuola', []),
            $cicliScolastici['elementari'] ?? new CicloScolastico('elementari', []),
            $cicliScolastici['medie'] ?? new CicloScolastico('medie', []),
            $cicliScolastici['superiori'] ?? new CicloScolastico('superiori', []),
            $cicliScolastici['universita'] ?? new CicloScolastico('universita', [])
        );
    }
}

final class CicloScolastico
{
    public int $alunniCount;

    public function __construct(
        public string $ciclo,
        /** @var Classe[] */
        public array $classi
    ) {
        $this->alunniCount = 0;
    }
}

final class Classe
{
    public function __construct(
        public string $nome,
        public array $alunni
    ) {}
}
