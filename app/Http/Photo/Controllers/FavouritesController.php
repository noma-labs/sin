<?php

declare(strict_types=1);

namespace App\Photo\Controllers;

use App\Photo\Models\Photo;
use App\Photo\Models\PhotoEnrico;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class FavouritesController
{
    public function index(Request $request): View
    {
        $filterYear = $request->string('year');
        $withEnricoMetadata = $request->input('with_metadata', false);

        $enrico = null;
        if ($withEnricoMetadata) {
            $enrico = PhotoEnrico::query();

            if (! $filterYear->isEmpty()) {
                $enrico = $enrico->orWhere('descrizione', 'like', "%$filterYear%");
                $enrico = $enrico->orWhereRaw('YEAR(data)= ?', [$filterYear]);
            }
            $enrico->orderBy('data');
            $enrico = $enrico->get();
        }

        $q = Photo::query()->orderBy('taken_at')
            ->where('favorite', 1)
            ->orderBy('taken_at');

        if (! $filterYear->isEmpty()) {
            $q->whereRaw('YEAR(taken_at)= ?', [$filterYear]);
        }

        $photos = $q->paginate(50);
        $photos_count = $q->count();

        $years = Photo::query()
            ->selectRaw('YEAR(taken_at) as year, count(*) as `count` ')
            ->groupByRaw('YEAR(taken_at)')
            ->where('favorite', 1)
            ->orderByRaw('YEAR(taken_at)')
            ->get();

        return view('photo.favorite.index', compact('photos', 'photos_count', 'years', 'enrico'));
    }

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
