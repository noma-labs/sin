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

        // Paginated list preserved for UI controls/pagination if needed (applies filters)
        $query = Photo::query()
            ->with('strip')
            ->oldest('taken_at')
            ->orderBy($orderBy->toString());

        if (! $filterYear->isEmpty()) {
            $query->whereRaw('YEAR(taken_at)= ?', [$filterYear]);
        }
        if (! $filterPersonName->isEmpty()) {
            $query->where('subjects', 'like', '%'.$filterPersonName->toString().'%');
        }

        $photos = $query->paginate(50);

        // Build top-level folders using directory field across the entire dataset (ignore filters)
        /** @var array{children: array<string, array{label: string, children: array<string, mixed>, total?: int, preview?: Photo}>} $dirTree */
        $dirTree = ['children' => []];

        $topFolders = Photo::query()
            ->selectRaw("SUBSTRING_INDEX(TRIM(LEADING '/' FROM directory), '/', 1) AS top")
            ->selectRaw('COUNT(*) AS cnt')
            ->whereNotNull('directory')
            ->whereRaw("directory <> ''")
            ->whereRaw("TRIM(LEADING '/' FROM directory) <> ''")
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
                ->whereRaw("TRIM(LEADING '/' FROM directory) LIKE ?", [$top.'%'])
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
        ]);
    }

    public function show(Request $request, string $path): View
    {
        $currentView = $request->get('view', 'grid');

        // Decode and normalize path to handle special characters (spaces, parentheses, etc.)
        $decoded = rawurldecode($path);
        $normalized = mb_trim($decoded, '/');
        $segments = array_values(array_filter(explode('/', $normalized), static fn ($s) => $s !== ''));

        // Build immediate subfolders using directory field, ignore pagination
        /** @var array{children: array<string, array{label: string, children: array<string, mixed>, total?: int, preview?: Photo}>} $dirTree */
        $dirTree = ['children' => []];

        $childrenRows = Photo::query()
            ->selectRaw("SUBSTRING_INDEX(REPLACE(TRIM(LEADING '/' FROM directory), CONCAT(?, '/'), ''), '/', 1) AS child", [$normalized])
            ->selectRaw('COUNT(*) AS cnt')
            ->where(function ($q) use ($normalized) {
                $q->whereRaw("TRIM(LEADING '/' FROM directory) LIKE ?", [$normalized.'/%'])
                    ->orWhere(function ($qq) use ($normalized) {
                        // Special folder bucket for no-directory
                        if ($normalized === '__no_directory__') {
                            $qq->where(function ($x) {
                                $x->whereNull('directory')->orWhere('directory', '=', '');
                            });
                        }
                    });
            })
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
                ->whereRaw("TRIM(LEADING '/' FROM directory) LIKE ?", [$normalized.'/'.$child.'%'])
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
            ->where(function ($q) use ($normalized) {
                if ($normalized === '__no_directory__') {
                    $q->whereNull('directory')->orWhere('directory', '=', '');
                } else {
                    $q->whereRaw("TRIM(LEADING '/' FROM directory) = ?", [$normalized]);
                }
            })
            ->oldest('taken_at')
            ->orderBy('source_file')
            ->paginate(50)
            ->withQueryString();

        return view('photo.folders.show', [
            'photos' => $photos,
            'currentView' => $currentView,
            'dirTree' => $dirTree,
            'path' => $normalized,
        ]);
    }
}
