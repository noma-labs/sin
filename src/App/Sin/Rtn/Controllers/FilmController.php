<?php

namespace App\Rtn\Controllers;

use App\Core\Controllers\BaseController;
use App\Rtn\Models\Trasmissione;
use Illuminate\Http\Request;

/**
 * Controller per la gestione dei film
 *
 * @author Josè Juan 18/09/2018
 */
class FilmController extends BaseController
{
    public function search(Request $request)
    {
        $serie = Trasmissione::serie();

        return view('rtn.film.search', compact('serie'));
    }
}
