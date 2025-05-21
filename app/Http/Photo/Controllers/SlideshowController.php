<?php

declare(strict_types=1);

namespace App\Photo\Controllers;

use App\Photo\Models\Photo;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

final class SlideshowController
{
    public function index(Request $request): View
    {
        $filterYear = $request->string('year');
        $filterPersonName = $request->string('name');
        $favorite = $request->boolean('favorite');
        $every = $request->input('every', 5000);

        $q = Photo::query()
            ->orderBy('taken_at')
            ->orderBy('taken_at');

        if (! $filterYear->isEmpty()) {
            $q->whereRaw('YEAR(taken_at)= ?', [$filterYear]);
        }
        if (! $filterPersonName->isEmpty()) {
            $q->where('subjects', 'like', '%'.$filterPersonName->toString().'%');
        }
        if ($favorite) {
            $q->where('favorite', true);
        }
        $photos = $q->paginate(50);

        return view('photo.slideshow.index', compact('photos', 'every'));
    }
}
