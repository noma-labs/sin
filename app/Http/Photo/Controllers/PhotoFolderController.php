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

        $q = Photo::query()
            ->with('strip')
            ->oldest('taken_at');

        if (! $filterYear->isEmpty()) {
            $q->whereRaw('YEAR(taken_at)= ?', [$filterYear]);
        }
        if (! $filterPersonName->isEmpty()) {
            $q->where('subjects', 'like', '%'.$filterPersonName->toString().'%');
        }

        $q->orderBy($orderBy->toString());

        // Paginated list preserved for UI controls/pagination if needed (applies filters)
        $photos = $q->paginate(50);

        // Build folder tree across the entire dataset (ignoring filters)
        $allPhotos = Photo::query()
            ->with('strip')
            ->orderBy('source_file')
            ->get();

        /**
         * Build hierarchical directory tree based on current page photos.
         * Structure: children keyed by segment name; each child is an associative node array.
         *
         * @var array{children: array<string, array{label?: string, children: array<string, mixed>, photos?: array<int, Photo>, total?: int}>}
         */
        $dirTree = ['children' => []];
        foreach ($allPhotos as $photo) {
            /** @var Photo $photo */
            $dirRaw = $photo->getAttribute('directory');
            $dir = is_string($dirRaw) ? mb_trim($dirRaw) : '';
            if ($dir === '') {
                if (! isset($dirTree['children']['__no_directory__'])) {
                    $dirTree['children']['__no_directory__'] = [
                        'label' => 'Senza Cartella',
                        'children' => [],
                        'photos' => [],
                    ];
                }
                $dirTree['children']['__no_directory__']['photos'][] = $photo;

                continue;
            }
            $segments = array_values(array_filter(explode('/', $dir), static fn ($s) => $s !== ''));
            /** @var array{label?: string, children: array<string, mixed>, photos?: array<int, Photo>, total?: int} $node */
            $node = &$dirTree;
            foreach ($segments as $seg) {
                if (! isset($node['children'][$seg])) {
                    $node['children'][$seg] = [
                        'label' => $seg,
                        'children' => [],
                        'photos' => [],
                    ];
                }
                /** @var array{label?: string, children: array<string, mixed>, photos?: array<int, Photo>, total?: int} $node */
                $node = &$node['children'][$seg];
            }
            $node['photos'][] = $photo;
            unset($node);
        }

        /**
         * Compute totals recursively and set preview image for each node.
         *
         * @param  array{label?: string, children: array<string, mixed>, photos?: array<int, Photo>, total?: int, preview?: Photo}  $node
         * @return int
         */
        $computeTotals = function (array &$node) use (&$computeTotals): int {
            $own = isset($node['photos']) ? count($node['photos']) : 0;
            $sum = $own;
            if (isset($node['children'])) {
                foreach ($node['children'] as &$child) {
                    $sum += $computeTotals($child);
                }
            }
            $node['total'] = $sum;

            if (! isset($node['preview'])) {
                if ($own > 0) {
                    /** @var Photo $first */
                    $first = $node['photos'][0];
                    $node['preview'] = $first;
                } elseif (isset($node['children'])) {
                    foreach ($node['children'] as $child) {
                        if (isset($child['preview'])) {
                            $node['preview'] = $child['preview'];
                            break;
                        }
                    }
                }
            }

            return $sum;
        };
        $computeTotals($dirTree);

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

        $q = Photo::query()->with('strip')->where('directory', 'like', $normalized.'%')->oldest('taken_at');
        $q->orderBy('source_file');
        $photos = $q->paginate(50);
        // Fetch all matching photos for accurate subfolder listing (beyond current page)
        $allPhotos = (clone $q)->get();

        // Build a tree starting at this path only for sub-folders and leaf detection
        /**
         * @var array{children: array<string, array{label?: string, children: array<string, mixed>, photos?: array<int, Photo>, total?: int}>}
         */
        $dirTree = ['children' => []];
        foreach ($allPhotos as $photo) {
            /** @var Photo $photo */
            $dirRaw = $photo->getAttribute('directory');
            $dir = is_string($dirRaw) ? mb_trim($dirRaw) : '';
            if ($dir === '' || mb_strpos($dir, $normalized) !== 0) {
                // Skip photos not under the requested path
                continue;
            }

            $sub = mb_substr($dir, mb_strlen($normalized));
            $sub = mb_ltrim($sub, '/');
            $subSegments = $sub === '' ? [] : array_values(array_filter(explode('/', $sub), static fn ($s) => $s !== ''));

            /** @var array{label?: string, children: array<string, mixed>, photos?: array<int, Photo>, total?: int} $node */
            $node = &$dirTree;
            foreach ($subSegments as $seg) {
                if (! isset($node['children'][$seg])) {
                    $node['children'][$seg] = [
                        'label' => $seg,
                        'children' => [],
                        'photos' => [],
                    ];
                }
                /** @var array{label?: string, children: array<string, mixed>, photos?: array<int, Photo>, total?: int} $node */
                $node = &$node['children'][$seg];
            }
            if ($sub === '') {
                // leaf-level photos (in the requested folder itself)
                if (! isset($node['photos'])) {
                    $node['photos'] = [];
                }
                $node['photos'][] = $photo;
            }
            unset($node);
        }

        // Compute totals for display counters and previews
        $computeTotals = function (array &$node) use (&$computeTotals): int {
            $own = isset($node['photos']) ? count($node['photos']) : 0;
            $sum = $own;
            if (isset($node['children'])) {
                foreach ($node['children'] as &$child) {
                    $sum += $computeTotals($child);
                }
            }
            $node['total'] = $sum;

            if (! isset($node['preview'])) {
                if ($own > 0) {
                    /** @var Photo $first */
                    $first = $node['photos'][0];
                    $node['preview'] = $first;
                } elseif (isset($node['children'])) {
                    foreach ($node['children'] as $child) {
                        if (isset($child['preview'])) {
                            $node['preview'] = $child['preview'];
                            break;
                        }
                    }
                }
            }

            return $sum;
        };
        $computeTotals($dirTree);

        return view('photo.folders.show', [
            'photos' => $photos,
            'currentView' => $currentView,
            'dirTree' => $dirTree,
            'path' => $normalized,
        ]);
    }
}
