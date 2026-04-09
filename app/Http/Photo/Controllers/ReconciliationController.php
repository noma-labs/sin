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
        $photoSearch = $request->query('photoSearch', '');
        $dbfSearch = $request->query('dbfSearch', '');

        $photoQuery = Photo::query()
            ->whereNull('dbf_id')
            ->orderBy('file_name');

        if (!empty($photoSearch)) {
            $photoQuery->where('file_name', 'LIKE', "%{$photoSearch}%");
        }

        $dbfQuery = DbfAll::query()->orderBy('datnum');

        if (!empty($dbfSearch)) {
            $dbfQuery->where('datnum', 'LIKE', "%{$dbfSearch}%")
                ->orWhere('anum', 'LIKE', "%{$dbfSearch}%")
                ->orWhere('descrizione', 'LIKE', "%{$dbfSearch}%");
        }

        $unlinkedPhotos = $photoQuery->paginate(15);
        $dbfAllRecords = $dbfQuery->with('photos')->get();

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
        $dbfAllId = (int) $request->input('selectedDbfAll', 0);

        if (empty($selectedPhotos) || $dbfAllId <= 0) {
            return redirect()->back()->with('warning', 'Please select at least one photo and one dbf record');
        }

        $dbfAll = DbfAll::find($dbfAllId);

        if ($dbfAll === null) {
            return redirect()->back()->with('error', 'Selected DbfAll record not found');
        }

        $updated = Photo::query()
            ->whereIn('id', $selectedPhotos)
            ->update(['dbf_id' => $dbfAllId]);

        return redirect()->route('photos.reconciliation')
            ->with('success', "Successfully linked {$updated} photo(s) to DbfAll record #{$dbfAllId}");
    }
}
