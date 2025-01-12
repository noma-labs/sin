<?php

declare(strict_types=1);

namespace App\Biblioteca\Controllers;

use App\Biblioteca\Models\ViewCollocazione as ViewCollocazione;
use Illuminate\Http\Request;

final class ApiController
{
    public function autocompleteCollocazione(Request $request)
    {
        // ?term=q       => return all the lettere that start with "q"
        // ?lettere=XXX  => return the numeri (liberi, asseganti, nuovo numero) for the selected lettere
        // ?lettere=XXX&soloassegnati=true => return only the numbers assigned for the letters
        if ($request->has('term')) {
            $CollocazioneLettere = ViewCollocazione::lettere()
                ->where('lettere', 'LIKE', $request->input('term').'%')
                ->get();
            $results[] = ['value' => 'null', 'label' => 'SENZA COLLOCAZIONE'];
            foreach ($CollocazioneLettere as $lettere) {
                $results[] = ['value' => $lettere->lettere, 'label' => $lettere->lettere];
            }

            return response()->json($results);
        }
        if ($request->has('lettere')) {
            $lettere = $request->input('lettere');
            $max = ViewCollocazione::MaxForLettere($lettere); // max numero associated with the lettere
            $numeri = ViewCollocazione::numeri($lettere)->get()->pluck('numeri')->toArray();
            $arr2 = range(1, $max);
            $res = array_diff($arr2, $numeri);

            $result = [
                'numeriAssegnati' => ($request->input('assegnati', 'true') === 'true') ? $numeri : null,
                //'Off';  // $numeri,
                'numeriMancanti' => ($request->input('mancanti', 'true') === 'true') ? $res : null,
                'numeroNuovo' => ($request->input('nuovo', 'true') === 'true') ? $max + 1 : null,
            ];

            return response()->json($result);
        }
    }
}
