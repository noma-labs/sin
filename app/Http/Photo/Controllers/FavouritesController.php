<?php

declare(strict_types=1);

namespace App\Photo\Controllers;

use App\Photo\Models\Photo;
use Illuminate\Http\RedirectResponse;

final class FavouritesController
{
    public function destroy(string $sha): RedirectResponse
    {
        $photo = Photo::query()->where('sha', $sha)->firstOrFail();
        $photo->favorite = false;
        $photo->save();

        return redirect()->back()->with('success', 'Foto rimossa dai favoriti con successo.');
    }

    public function store(string $sha): RedirectResponse
    {
        $photo = Photo::query()->where('sha', $sha)->firstOrFail();
        $photo->favorite = true;
        $photo->save();

        return redirect()->back()->with('success', 'Foto aggiunta ai favoriti.');
    }
}
