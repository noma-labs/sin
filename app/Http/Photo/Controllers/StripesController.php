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

        $stripes = DbfAll::query()
            ->with('photos')
            ->when(! $filterYear->isEmpty(), function ($qb) use ($filterYear) {
                return $qb->whereRaw('YEAR(data)= ?', [$filterYear]);
            })
            ->when(! empty($filterSource), function ($qb) use ($filterSource) {
                return $qb->where('source', '=', $filterSource);
            })
            ->when($filterNoPhotos, function ($qb) {
                return $qb->doesntHave('photos');
            })
            ->orderBy($orderBy)
            ->paginate(20);

        $years = DbfAll::query()
            ->selectRaw('YEAR(data) as year, count(*) as `count` ')
            ->when(! $filterYear->isEmpty(), function ($qb) use ($filterYear) {
                return $qb->whereRaw('YEAR(data)= ?', [$filterYear]);
            })
            ->when(! empty($filterSource), function ($qb) use ($filterSource) {
                return $qb->where('source', '=', $filterSource);
            })
            ->when($filterNoPhotos, function ($qb) {
                return $qb->doesntHave('photos');
            })
            ->groupByRaw('YEAR(data)')
            ->get();

        return view('photo.stripe.index', [
            'years' => $years,
            'stripes' => $stripes,
            'no_photos' => $filterNoPhotos,
        ]);
    }
}
