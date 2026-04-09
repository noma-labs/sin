<?php

declare(strict_types=1);

namespace App\Photo\Controllers;

use App\Photo\Models\DbfAll;
use App\Photo\Models\Photo;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class ReconciliationController
{
    public function index(Request $request): View
    {
        $photoSearch = $request->string('photoSearch', '')->toString();
        $dbfSearch = $request->string('dbfSearch', '')->toString();

        $photoQuery = Photo::query()
            ->whereNull('dbf_id')
            ->orderBy('file_name')
            ->when($photoSearch !== '', fn ($q) => $q->where('file_name', 'LIKE', "%{$photoSearch}%"));

        $dbfQuery = DbfAll::query()
            ->orderBy('datnum')
            ->when($dbfSearch !== '', fn ($q) => $q
                ->where('datnum', 'LIKE', "%{$dbfSearch}%")
                ->orWhere('anum', 'LIKE', "%{$dbfSearch}%")
                ->orWhere('descrizione', 'LIKE', "%{$dbfSearch}%"));

        $unlinkedPhotos = $photoQuery->paginate(15);
        $dbfAllRecords = $dbfQuery->with(['photos' => fn ($q) => $q->orderBy('file_name')])->get();

        return view('photo.reconciliation.index', [
            'unlinkedPhotos' => $unlinkedPhotos,
            'dbfAllRecords' => $dbfAllRecords,
            'photoSearch' => $photoSearch,
            'dbfSearch' => $dbfSearch,
        ]);
    }

    public function link(Request $request): RedirectResponse
    {
        $selectedPhotos = $request->input('selectedPhotos', []);
        $dbfAllId = $request->integer('selectedDbfAll', 0);
        $photoSearch = $request->string('photoSearch', '')->toString();
        $dbfSearch = $request->string('dbfSearch', '')->toString();

        $filters = array_filter([
            'photoSearch' => $photoSearch,
            'dbfSearch' => $dbfSearch,
        ]);

        if (empty($selectedPhotos) || $dbfAllId <= 0) {
            return back()->with('warning', 'Please select at least one photo and one dbf record');
        }

        $dbfAll = DbfAll::query()->find($dbfAllId);

        if ($dbfAll === null) {
            return back()->with('error', 'Selected DbfAll record not found');
        }

        $updated = Photo::query()
            ->whereIn('id', $selectedPhotos)
            ->update(['dbf_id' => $dbfAllId]);

        return to_route('photos.reconciliation', $filters)
            ->with('success', "Successfully linked {$updated} photo(s) to DbfAll record #{$dbfAllId}");
    }
}
