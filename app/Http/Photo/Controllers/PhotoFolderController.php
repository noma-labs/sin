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

        $photos = $q->paginate(50);

        /**
         * Build hierarchical directory tree based on current page photos.
         * Structure: children keyed by segment name; each child is an associative node array.
         *
         * @var array{children: array<string, array{label?: string, children: array<string, mixed>, photos?: array<int, Photo>, total?: int}>}
         */
        $dirTree = ['children' => []];
        foreach ($photos as $photo) {
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
            $segments = array_values(array_filter(explode('/', $dir), fn ($s) => $s !== ''));
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
         * Compute totals recursively: adds 'total' key to each node.
         *
         * @param  array{label?: string, children: array<string, mixed>, photos?: array<int, Photo>, total?: int}  $node
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
}
