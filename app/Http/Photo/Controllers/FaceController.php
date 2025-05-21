<?php

declare(strict_types=1);

namespace App\Photo\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

final class FaceController
{
    public function index(Request $request): View
    {
        $search = $request->string('search');
        $query = DB::connection('db_foto')
            ->table('photos_people')
            ->selectRaw('p.id, p.nome, p.cognome, photos_people.persona_nome as name, count(*) as count')
            ->leftJoin('db_nomadelfia.alfa_enrico_15_feb_23 as e', 'e.FOTO', '=', 'photos_people.persona_nome')
            ->leftJoin('db_nomadelfia.persone as p', 'p.id_alfa_enrico', '=', 'e.id')
            ->groupBy('p.id', 'p.nome', 'p.cognome', 'photos_people.persona_nome')
            ->orderBY('photos_people.persona_nome');

        if (! $search->isEmpty()) {
            $query->where(function ($q) use ($search) {
                $s = $search->toString();
                $q->where('photos_people.persona_nome', 'like', "$s%")
                    ->orWhere('p.nome', 'like', "$s%")
                    ->orWhere('p.cognome', 'like', "$s%")
                    ->orWhere('e.ALIAS', 'like', "$s%");
            });
        }
        $faces = $query->paginate(200);

        return view('photo.face.index', compact('faces'));
    }
}
