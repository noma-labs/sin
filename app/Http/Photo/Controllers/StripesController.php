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
        $filterSourceParam = mb_strtolower($request->string('source')->toString());
        $sourceMap = [
            'slide' => 'slide',
            'dia120' => 'dia120',
            'foto' => 'foto',
        ];
        $filterSource = $sourceMap[$filterSourceParam] ?? null;

        $orderParam = mb_strtolower($request->string('order')->toString());
        $allowedOrders = ['datnum', 'data'];
        $orderBy = in_array($orderParam, $allowedOrders, true) ? $orderParam : 'datnum';

        $stripes = DbfAll::query()
            ->with('photos')
            ->unless($filterYear->isEmpty(), function ($qb) use ($filterYear) {
                $qb->whereRaw('YEAR(data)= ?', [$filterYear]);
            })
            ->when(! empty($filterSource), function ($qb) use ($filterSource) {
                $qb->where('source', '=', $filterSource);
            })
            ->orderBy($orderBy)
            ->paginate(20);

        $years = DbfAll::query()
            ->selectRaw('YEAR(data) as year, count(*) as `count` ')
            ->unless($filterYear->isEmpty(), function ($qb) use ($filterYear) {
                $qb->whereRaw('YEAR(data)= ?', [$filterYear]);
            })
            ->when(! empty($filterSource), function ($qb) use ($filterSource) {
                $qb->where('source', '=', $filterSource);
            })
            ->groupByRaw('YEAR(data)')
            ->get();

        return view('photo.stripe.index', [
            'years' => $years,
            'stripes' => $stripes,
            'source' => $filterSourceParam,
            'order' => $orderBy,
        ]);
    }
}
