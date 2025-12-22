<?php

declare(strict_types=1);

namespace App\Photo\Controllers;

use App\Photo\Models\Photo;
use App\Photo\Models\PhotoEnrico;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use ZipArchive;

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

        $q = Photo::query()->oldest('taken_at')
            ->where('favorite', 1)
            ->oldest('taken_at');

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

    public function destroy(int $id): RedirectResponse
    {
        $photo = Photo::query()->findOrFail($id);
        $photo->favorite = false;
        $photo->save();

        return back()->with('success', 'Foto rimossa dai favoriti con successo.');
    }

    public function store(int $id): RedirectResponse
    {
        $photo = Photo::query()->findOrFail($id);
        $photo->favorite = true;
        $photo->save();

        return back()->with('success', 'Foto aggiunta ai favoriti.');
    }

    public function download(): BinaryFileResponse
    {
        $photos = Photo::query()->select('source_file')->where('favorite', 1)->get();

        if ($photos->isEmpty()) {
            abort(404, 'No favorite photos found.');
        }

        $zipFileName = 'favorite_photos_'.now()->format('Ymd_His').'.zip';
        $zipPath = storage_path('app/tmp/'.$zipFileName);

        // Ensure tmp directory exists
        if (! is_dir(storage_path('app/tmp'))) {
            mkdir(storage_path('app/tmp'), 0777, true);
        }

        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            foreach ($photos as $photo) {
                $filePath = Storage::disk('media_originals')->path($photo->source_file);
                if (file_exists($filePath)) {
                    $zip->addFile($filePath, basename((string) $photo->source_file));
                }
            }
            $zip->close();
        } else {
            abort(500, 'Could not create ZIP file.');
        }

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }
}
