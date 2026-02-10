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
        /** @var array<int, array{dirName: string, total: int, preview?: Photo}> $dirTree */
        $dirTree = [];

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
            $preview = Photo::query()
                ->whereRaw("TRIM(LEADING '/' FROM directory) LIKE ?", [$top.'%'])
                ->orderBy('source_file')
                ->first();

            $dirTree[] = [
                'dirName' => $top,
                'total' => (int) $row->cnt,
                'preview' => $preview,
            ];
        }

        return view('photo.folders.index', ['dirTree' => $dirTree]);
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
                $q->whereRaw("TRIM(LEADING '/' FROM directory) LIKE ?", [$normalized.'/%']);
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
               $q->whereRaw("TRIM(LEADING '/' FROM directory) = ?", [$normalized]);
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
