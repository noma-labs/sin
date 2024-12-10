<?php
declare(strict_types=1);

namespace App\Scuola\DataTransferObjects;
use App\Scuola\DataTransferObjects\AnnoScolastico;
use App\Scuola\Models\Anno;
use Carbon\Carbon;
use Domain\Nomadelfia\Persona\Models\Persona;
use Illuminate\Support\Facades\DB;

final class AnnoScolasticoData
{
    public function __construct(
        public int $id,
        public AnnoScolastico $as,
        public ?Persona $responsabile,
        public Carbon $data_inizio,
        public string $descrizione,
        public CicloScolastico $prescuola,
        public CicloScolastico $elementari,
        public CicloScolastico $medie,
        public CicloScolastico $superiori,
        public CicloScolastico $universita,
    )
    { }

    public static  function FromDatabase(Anno $anno): self
    {
        return new self(
            (int)$anno->id,
            AnnoScolastico::fromString($anno->scolastico),
            $anno->responsabile ? Persona::findOrFail($anno->responsabile): null,
            Carbon::parse($anno->data_inizio),
            $anno->descrizione,
            self::getCicloScolastico($anno,'prescuola'),
            self::getCicloScolastico($anno,'elementari'),
            self::getCicloScolastico($anno,'medie'),
            self::getCicloScolastico($anno,'superiori'),
            self::getCicloScolastico($anno,'universita'),
        );
    }

    private static function getCicloScolastico(Anno $a, string $ciclo): CicloScolastico {
        $results = DB::connection('db_scuola')
            ->table('tipo')
            ->join('classi', 'classi.tipo_id', '=', 'tipo.id')
            ->join('alunni_classi', 'alunni_classi.classe_id', '=', 'classi.id')
            ->select(
                'tipo.nome',
                DB::raw('GROUP_CONCAT(DISTINCT alunni_classi.persona_id ORDER BY alunni_classi.persona_id SEPARATOR ", ") as alunni')
            )
            ->where('tipo.ciclo', $ciclo)
            ->where('classi.anno_id', $a->id)
            ->groupBy('tipo.ciclo', 'tipo.nome')
            ->orderBy('tipo.nome')
            ->get();

        $alunniClasse = [];
        foreach ($results as $result) {
            $alunniIds = array_map('intval', explode(',', $result->alunni));
            $alunniClasse[] = new Classe($result->nome, $alunniIds);
        }

        return new CicloScolastico($ciclo, $alunniClasse);
    }
}

class CicloScolastico
{
    public string $ciclo;
    public int $alunniCount;
    /** @var Classe[] */
    public array $classi;

    public function __construct(string $ciclo, array $classi)
    {
        $this->ciclo = $ciclo;
        $this->classi = $classi;
        for ($i = 0; $i < count($classi); $i++) {
            $this->alunniCount += count($classi[$i]->alunni);
        }
    }
}

class Classe
{
    public string $nome; // Nome della classe. 1 elementare, 2 elementare, ecc.
    /** @var int[] */
    public array $alunni;

    public function __construct(string $nome, array $alunni)
    {
        $this->nome = $nome;
        $this->alunni = $alunni;
    }
}
