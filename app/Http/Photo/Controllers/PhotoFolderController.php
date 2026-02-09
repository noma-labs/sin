<?php

declare(strict_types=1);

namespace App\Photo\Controllers;

use App\Photo\Models\Photo;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

final class PhotoFolderController
{
    public function index(Request $request): View
    {
        $filterYear = $request->string('year');
        $filterPersonName = $request->string('name');
        $orderBy = $request->string('order', 'source_file');
        $currentView = $request->get('view', 'grid');

        // Paginated list preserved for UI controls/pagination if needed (applies filters)
        $photos = Photo::query()
            ->with('strip')
            ->oldest('taken_at')
            ->orderBy($orderBy->toString())
            ->unless($filterYear->isEmpty(), static function ($query) use ($filterYear) {
                $query->whereRaw('YEAR(taken_at)= ?', [$filterYear]);
            })
            ->when(! $filterPersonName->isEmpty(), static function ($query) use ($filterPersonName) {
                $query->where('subjects', 'like', '%'.$filterPersonName->toString().'%');
            })
            ->paginate(50);

        // Build top-level folders using directory field across the entire dataset (ignore filters)
        /** @var array{children: array<string, array{label: string, children: array<string, mixed>, total?: int, preview?: Photo}>} $dirTree */
        $dirTree = ['children' => []];

        $topFolders = Photo::query()
            ->selectRaw("TRIM(BOTH '/' FROM SUBSTRING_INDEX(directory, '/', 1)) AS top")
            ->selectRaw('COUNT(*) AS cnt')
            ->whereNotNull('directory')
            ->whereRaw("directory <> ''")
            ->groupBy('top')
            ->orderBy('top')
            ->get();

        foreach ($topFolders as $row) {
            $top = (string) $row->top;
            $dirTree['children'][$top] = [
                'label' => $top,
                'children' => [],
                'total' => (int) $row->cnt,
            ];
            // Preview: earliest photo under this top folder
            $preview = Photo::query()
                ->where('directory', 'like', $top.'%')
                ->oldest('taken_at')
                ->orderBy('source_file')
                ->first();
            if ($preview !== null) {
                $dirTree['children'][$top]['preview'] = $preview;
            }
        }

        // Special bucket for photos without directory
        $noDirCount = Photo::query()
            ->whereNull('directory')
            ->orWhere('directory', '=', '')
            ->count();
        if ($noDirCount > 0) {
            $dirTree['children']['__no_directory__'] = [
                'label' => 'Senza Cartella',
                'children' => [],
                'total' => (int) $noDirCount,
            ];
            $noDirPreview = Photo::query()
                ->whereNull('directory')
                ->orWhere('directory', '=', '')
                ->oldest('taken_at')
                ->orderBy('source_file')
                ->first();
            if ($noDirPreview !== null) {
                $dirTree['children']['__no_directory__']['preview'] = $noDirPreview;
            }
        }

        $qYears = Photo::query()
            ->selectRaw('YEAR(taken_at) as year, count(*) as `count` ')
            ->groupByRaw('YEAR(taken_at)')
            ->orderByRaw('YEAR(taken_at)');
        if (! $filterPersonName->isEmpty()) {
            $qYears->where('subjects', 'like', '%'.$filterPersonName->toString().'%');
        }
        $years = $qYears->get();

        return view('photo.folders.index', [
            'photos' => $photos,
            'years' => $years,
            'dirTree' => $dirTree,
            'currentView' => $currentView,
        ]);
    }

    public function show(Request $request, string $path): View
    {
        $currentView = $request->get('view', 'grid');

        // Normalize path
        $normalized = mb_trim($path, '/');
        $segments = array_values(array_filter(explode('/', $normalized), static fn ($s) => $s !== ''));

        // Build immediate subfolders using directory field, ignore pagination
        /** @var array{children: array<string, array{label: string, children: array<string, mixed>, total?: int, preview?: Photo}>} $dirTree */
        $dirTree = ['children' => []];

        $childrenRows = Photo::query()
            ->selectRaw("SUBSTRING_INDEX(REPLACE(directory, CONCAT(?, '/'), ''), '/', 1) AS child", [$normalized])
            ->selectRaw('COUNT(*) AS cnt')
            ->where('directory', 'like', $normalized.'/%')
            ->groupBy('child')
            ->orderBy('child')
            ->get();

        foreach ($childrenRows as $row) {
            $child = (string) $row->child;
            if ($child === '') {
                continue;
            }
            $dirTree['children'][$child] = [
                'label' => $child,
                'children' => [],
                'total' => (int) $row->cnt,
            ];
            $preview = Photo::query()
                ->where('directory', 'like', $normalized.'/'.$child.'%')
                ->oldest('taken_at')
                ->orderBy('source_file')
                ->first();
            if ($preview !== null) {
                $dirTree['children'][$child]['preview'] = $preview;
            }
        }

        // Leaf photos: only photos directly in this folder (exact match)
        $photos = Photo::query()
            ->with('strip')
            ->where('directory', '=', $normalized)
            ->oldest('taken_at')
            ->orderBy('source_file')
            ->paginate(50);

        return view('photo.folders.show', [
            'photos' => $photos,
            'currentView' => $currentView,
            'dirTree' => $dirTree,
            'path' => $normalized,
        ]);
    }
}
