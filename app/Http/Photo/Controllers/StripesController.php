<?php

declare(strict_types=1);

namespace App\Photo\Controllers;

use App\Photo\Models\DbfAll;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

final class StripesController
{
    public function index(Request $request): View
    {
        $filterYear = $request->string('year');
        $filterSource = $request->string('source', '')->toString();
        $orderBy = $request->string('order', 'datnum')->toString();
        $filterNoPhotos = $request->boolean('no_photos', false);
        $filterMismatch = $request->boolean('mismatch', false);
        $search = $request->string('q', '')->toString();

        $stripes = DbfAll::query()
            ->with(['photos' => fn ($q) => $q->orderBy('file_name')])
            ->unless($filterYear->isEmpty(), fn ($qb) => $qb->whereRaw('YEAR(data)= ?', [$filterYear]))
            ->when(! empty($filterSource), fn ($qb) => $qb->where('source', '=', $filterSource))
            ->when($filterNoPhotos, fn ($qb) => $qb->doesntHave('photos'))
            ->when($filterMismatch, fn ($qb) => $qb->whereRaw('nfo IS NOT NULL AND nfo <> (SELECT COUNT(*) FROM photos p WHERE p.dbf_id = dbf_all.id)'))
            ->when(! empty($search), function ($qb) use ($search) {
                $qb->where(function ($q) use ($search) {
                    $q->where('datnum', 'like', "%{$search}%")
                      ->orWhere('anum', 'like', "%{$search}%")
                      ->orWhere('localita', 'like', "%{$search}%")
                      ->orWhere('argomento', 'like', "%{$search}%")
                      ->orWhere('descrizione', 'like', "%{$search}%");
                });
            })
            ->orderBy($orderBy)
            ->paginate(20);

        $years = DbfAll::query()
            ->selectRaw('YEAR(data) as year, count(*) as `count` ')
            ->unless($filterYear->isEmpty(), fn ($qb) => $qb->whereRaw('YEAR(data)= ?', [$filterYear]))
            ->unless(empty($filterSource), fn ($qb) => $qb->where('source', '=', $filterSource))
            ->when($filterNoPhotos, fn ($qb) => $qb->doesntHave('photos'))
            ->when($filterMismatch, fn ($qb) => $qb->whereRaw('nfo IS NOT NULL AND nfo <> (SELECT COUNT(*) FROM photos p WHERE p.dbf_id = dbf_all.id)'))
            ->when(! empty($search), function ($qb) use ($search) {
                $qb->where(function ($q) use ($search) {
                    $q->where('datnum', 'like', "%{$search}%")
                      ->orWhere('anum', 'like', "%{$search}%")
                      ->orWhere('localita', 'like', "%{$search}%")
                      ->orWhere('argomento', 'like', "%{$search}%")
                      ->orWhere('descrizione', 'like', "%{$search}%");
                });
            })
            ->groupByRaw('YEAR(data)')
            ->get();

        return view('photo.stripe.index', [
            'years' => $years,
            'stripes' => $stripes,
            'no_photos' => $filterNoPhotos,
            'mismatch' => $filterMismatch,
        ]);
    }

    public function show(DbfAll $stripe): View
    {
        $stripe->load(['photos' => fn ($q) => $q->orderBy('file_name')]);

        return view('photo.stripe.show', [
            'stripe' => $stripe,
            'photoCount' => $stripe->photos->count(),
        ]);
    }
}
