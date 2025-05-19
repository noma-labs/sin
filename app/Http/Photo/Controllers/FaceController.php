<?php

declare(strict_types=1);

namespace App\Photo\Controllers;

use App\Photo\Models\Photo;
use App\Photo\Models\PhotoEnrico;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

final class FaceController
{
    public function index(Request $request): View
    {
        $faces = DB::connection('db_foto')
            ->table('photos_people')
            ->selectRaw(
            "COALESCE(
                NULLIF(CONCAT(p.nome, ' ', p.cognome), ' '),
                photos_people.persona_nome
            ) as name, count(*) as count"
        )
            ->leftJoin('db_nomadelfia.alfa_enrico_15_feb_23 as e', 'e.FOTO', '=', 'photos_people.persona_nome')
            ->leftJoin('db_nomadelfia.persone as p', 'p.id_alfa_enrico', '=', 'e.id')
            ->groupBy('photos_people.persona_nome','p.nome', 'p.cognome')
            ->paginate(150);

        return view('photo.face.index', compact('faces'));
    }


}
