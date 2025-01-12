<?php

declare(strict_types=1);

namespace App\Officina\Controllers;

use App\Officina\Models\TipoFiltro;
use App\Officina\Models\TipoGomme;
use App\Officina\Models\Veicolo;
use Illuminate\Http\Request;
use Throwable;

final class ApiController
{

    public function filtri(): array
    {
        $filtri = TipoFiltro::all()->sortBy('tipo');
        $result = [];
        foreach ($filtri as $value) {
            $result[] = $value;
        }

        return $result;
    }

    public function tipiFiltro(): array
    {
        return TipoFiltro::tipo();
    }

    public function eliminaFiltro(Request $request): array
    {
        $filtro = TipoFiltro::find($request->input('filtro'));
        try {
            $filtro->delete();
        } catch (Throwable) {
            return ['status' => 'error', 'msg' => "Errore nell'eliminazione del filtro"];
        }

        return ['status' => 'success', 'msg' => 'Filtro eliminato'];
    }
}
