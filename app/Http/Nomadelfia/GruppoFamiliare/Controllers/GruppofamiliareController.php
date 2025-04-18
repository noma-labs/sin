<?php

declare(strict_types=1);

namespace App\Nomadelfia\GruppoFamiliare\Controllers;

use App\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;

final class GruppofamiliareController
{
    public function index()
    {
        $g = GruppoFamiliare::countComponenti();

        return view('nomadelfia.gruppifamiliari.index', compact('g'));
    }

    public function show($id)
    {
        $gruppo = GruppoFamiliare::findOrFail($id);
        $single = GruppoFamiliare::single($gruppo)->get();

        $fams = GruppoFamiliare::families($gruppo)->get();
        $famiglie = collect($fams)->groupBy('famiglia_id');

        return view('nomadelfia.gruppifamiliari.show', compact('gruppo', 'single', 'famiglie'));
    }
}
