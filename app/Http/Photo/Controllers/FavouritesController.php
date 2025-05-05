<?php

declare(strict_types=1);

namespace App\Photo\Controllers;

use App\Photo\Models\Photo;
use Illuminate\Http\Request;

final class FavouritesController
{
    public function destroy(Request $request, string $sha)
    {
        $photo = Photo::where('sha', $sha)->firstOrFail();
        $photo->favorite = 0;
        $photo->save();

        return redirect()->route('photos.index')->with('success', 'Photo unfavorited successfully.');
    }

    public function store(Request $request, string $sha)
    {
        $photo = Photo::where('sha', $sha)->firstOrFail();
        $photo->favorite = 1;
        $photo->save();

        return redirect()->route('photos.index')->with('success', 'Photo favorited successfully.');
    }
}
