<?php

declare(strict_types=1);

namespace App\Scuola\DataTransferObjects;

use App\Scuola\Models\Anno;
use App\Scuola\Models\Studente;
use DateTimeImmutable;
use Domain\Nomadelfia\Persona\Models\Persona;

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
        $students = Studente::join('db_scuola.alunni_classi', 'db_scuola.alunni_classi.persona_id', '=', 'persone.id')
            ->join('db_scuola.classi', 'db_scuola.classi.id', '=', 'db_scuola.alunni_classi.classe_id')
            ->join('db_scuola.tipo', 'db_scuola.tipo.id', '=', 'db_scuola.classi.tipo_id')
            ->select(
                'persone.id',
                'persone.nome',
                'persone.cognome',
                'persone.nominativo',
                'persone.data_nascita',
                'db_scuola.tipo.ciclo',
                'db_scuola.tipo.nome as classe_nome',
            )
            ->where('db_scuola.classi.anno_id', $anno->id)
            ->orderBy('db_scuola.tipo.ord')
            ->orderBy('persone.nominativo')
            ->get();

        $cicliScolastici = collect($students)
            ->groupBy('ciclo')
            ->map(function ($cicloGroup, $ciclo): CicloScolastico {
                $classi = $cicloGroup
                    ->groupBy('classe_nome')
                    ->map(function ($classeGroup, $classeNome): Classe {
                        $alunni = $classeGroup->map(fn($item): StudenteData => new StudenteData(
                            $item->id,
                            $item->nome,
                            $item->cognome,
                            $item->nominativo,
                            new DateTimeImmutable($item->data_nascita),
                            $item->ciclo,
                            $item->classe_nome,
                        ))->values()->toArray();

                        return new Classe($classeNome, $alunni);
                    })->values()->toArray();

                return new CicloScolastico($ciclo, $classi);
            });

        return new self(
            (int) $anno->id,
            (int) $students->count(),
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
        public ?array $classi,
    ) {
        $this->alunniCount = 0;
        array_map(function ($classe): void {
            $this->alunniCount += count($classe->alunni);
        }, $classi);
    }
}

final class Classe
{
    public function __construct(
        public string $nome,
        /** @var StudenteData[] */
        public array $alunni
    ) {}
}

final class StudenteData
{
    public function __construct(
        public int $id,
        public string $nome,
        public string $cognome,
        public string $nominativo,
        public DateTimeImmutable $data_nascita,
        public string $ciclo,
        public string $classe_nome,
    ) {}
}
