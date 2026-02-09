<?php

declare(strict_types=1);

namespace App\Photo\Controllers;

use App\Photo\Models\Photo;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

final class PhotoController
{
    public function index(Request $request): View
    {
        $filterYear = $request->string('year');
        $filterPersonName = $request->string('name');
        $orderBy = $request->string('order', 'source_file');
        $view = $request->get('view', 'grid');
        // Deprecated: "no_strip" filter was replaced by grouped view that includes a "Senza Striscia" section.
        $filterNoStrip = false;
        $groupBy = $request->string('group'); // 'stripe' | 'directory' | ''

        $q = Photo::query()
            ->with('strip')
            ->oldest('taken_at');
        // no_strip removed: keep showing all; grouping handles visualization of "Senza Striscia"

        if (! $filterYear->isEmpty()) {
            $q->whereRaw('YEAR(taken_at)= ?', [$filterYear]);
        }
        if (! $filterPersonName->isEmpty()) {
            $q->where('subjects', 'like', '%'.$filterPersonName->toString().'%');
        }

        $q->orderBy($orderBy->toString());

        $photos = $q->paginate(50);
        $photos_count = $q->count();

        // Build grouped structure for the requested grouping mode (computed on current page for simplicity)
        /** @var array<string|int, array{label:string,meta:mixed,photos:array<int,Photo>}> $groups */
        $groups = [];
        /**
         * Hierarchical directory tree structure when grouping by directory.
         *
         * @var array{children: array<string, array{label:string, children: array<string, array{label:string, children: array<string, mixed>, photos: array<int, Photo> }>, photos: array<int, Photo>}>}
         */
        $dirTree = ['children' => []];
        if (! $groupBy->isEmpty()) {
            if ($groupBy->toString() === 'stripe') {
                // Group by associated stripe; include special "Senza Striscia" group for null dbf_id
                /** @var Photo $photo */
                foreach ($photos as $photo) {
                    $dbfIdRaw = $photo->getAttribute('dbf_id');
                    $dbfId = is_int($dbfIdRaw) ? $dbfIdRaw : null;
                    $key = is_null($dbfId) ? 'no_stripe' : (string) $dbfId;
                    if (! isset($groups[$key])) {
                        $groups[$key] = [
                            'label' => $photo->strip ? ($photo->strip->datnum) : 'Senza Striscia',
                            'meta' => $photo->strip ?? null,
                            'photos' => [],
                        ];
                    }
                    $groups[$key]['photos'][] = $photo;
                }
            } elseif ($groupBy->toString() === 'directory') {
                // Hierarchical grouping by directory path segments
                /** @var Photo $photo */
                foreach ($photos as $photo) {
                    $dirRaw = $photo->getAttribute('directory');
                    $dir = is_string($dirRaw) ? mb_trim($dirRaw) : '';
                    if ($dir === '') {
                        // Put photos without directory under a dedicated node
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
                    /** @var array{children: array<string, array{label:string, children: array<string, array{label:string, children: array<string, mixed>, photos: array<int, Photo> }>, photos: array<int, Photo>}>} $node */
                    $node = &$dirTree;
                    foreach ($segments as $seg) {
                        if (! isset($node['children'][$seg])) {
                            $node['children'][$seg] = [
                                'label' => $seg,
                                'children' => [],
                                'photos' => [],
                            ];
                        }
                        /** @var array{label:string, children: array<string, array{label:string, children: array<string, mixed>, photos: array<int, Photo> }>, photos: array<int, Photo>} $node */
                        $node = &$node['children'][$seg];
                    }
                    $node['photos'][] = $photo;
                    unset($node);
                }
                /**
                 * Compute aggregated photo counts for each directory node.
                 * Adds a 'total' key with the sum of this node's photos and all descendants.
                 *
                 * @param  array{label?:string, children: array<string, array>, photos?: array<int, Photo>, total?: int}  $node
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
            }
        }

        $qYears = Photo::query()
            ->selectRaw('YEAR(taken_at) as year, count(*) as `count` ')
            ->groupByRaw('YEAR(taken_at)')
            ->orderByRaw('YEAR(taken_at)');
        // no_strip removed for years aggregation
        if (! $filterPersonName->isEmpty()) {
            $qYears->where('subjects', 'like', '%'.$filterPersonName->toString().'%');
        }
        $years = $qYears->get();

        return view('photo.index', [
            'photos' => $photos,
            'photos_count' => $photos_count,
            'years' => $years,
            'group' => $groupBy->toString(),
            'groups' => $groups,
            'dirTree' => $dirTree,
        ]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'taken_at' => ['nullable', 'date'],
            'description' => ['nullable', 'string'],
            'location' => ['nullable', 'string'],
        ]);

        $photo = Photo::query()->findOrFail($id);

        if ($request->filled('taken_at')) {
            $photo->taken_at = \Illuminate\Support\Facades\Date::parse($request->string('taken_at')->toString());
        }

        if ($request->filled('description')) {
            $photo->description = $request->input('description');
        }

        if ($request->filled('location')) {
            $photo->location = $request->input('location');
        }

        $photo->save();

        return back()->with('success', 'Foto aggiornata correttamente');
    }

    public function show(int $id): View
    {
        $photo = Photo::query()->findOrFail($id);

        if (! Storage::disk('photos')->exists($photo->source_file)) {
            abort(404, 'File not found.');
        }

        $people = DB::connection('db_foto')
            ->table('photos_people')
            ->select('p.id', 'p.nome', 'p.cognome', 'e.ALIAS', 'photos_people.persona_nome')
            ->leftJoin('db_nomadelfia.alfa_enrico_15_feb_23 as e', 'e.FOTO', '=', 'photos_people.persona_nome')
            ->leftJoin('db_nomadelfia.persone as p', 'p.id_alfa_enrico', '=', 'e.id')
            ->where('photos_people.photo_id', '=', $photo->id)
            ->orderby('photos_people.persona_nome')
            ->get();

        return view('photo.show', [
            'photo' => $photo,
            'people' => $people,
        ]);
    }

    public function preview(Request $request, int $id): Response
    {
        $drawFaces = $request->boolean('draw_faces', false);

        $photo = Photo::query()->findOrFail($id);

        $fileContent = Storage::disk('photos')->get($photo->source_file);
        $filePath = Storage::disk('photos')->path($photo->source_file);
        $mimeType = mime_content_type($filePath);

        if (! $drawFaces) {
            return response()->make($fileContent, 200)->header('Content-Type', $mimeType);
        }

        if ($photo->region_info) {
            $regionInfo = $photo->region_info;
            $image = imagecreatefromjpeg($filePath);
            if (! $image) {
                exit('Failed to load image.');
            }

            $imageWidth = imagesx($image);
            $imageHeight = imagesy($image);

            $white = imagecolorallocate($image, 255, 255, 255);

            foreach ($regionInfo->RegionList as $region) {
                $area = $region->Area;
                // Convert normalized values to pixel dimensions
                $centerX = $area['X'] * $imageWidth;
                $centerY = $area['Y'] * $imageHeight;
                $width = $area['W'] * $imageWidth;
                $height = $area['H'] * $imageHeight;
                // Calculate top-left and bottom-right corners
                $x1 = (int) ($centerX - $width / 2);
                $y1 = (int) ($centerY - $height / 2);
                $x2 = (int) ($centerX + $width / 2);
                $y2 = (int) ($centerY + $height / 2);

                // Draw 3 rectangles to simulate a 3px thick border
                for ($i = 0; $i < 5; $i++) {
                    imagerectangle($image, $x1 - $i, $y1 - $i, $x2 + $i, $y2 + $i, $white);
                }

                if (isset($region->Name)) {
                    $name = $region->Name;
                    $font = 5; // Built-in font size
                    $textWidth = imagefontwidth($font) * mb_strlen($name);
                    $textHeight = imagefontheight($font);
                    $textX = (int) max(0, $x1 + ($width - $textWidth) / 2);
                    $textY = (int) max(0, $y1 - $textHeight - 4); // 4px padding
                    imagestring($image, $font, $textX, $textY, $name, $white);
                }
            }

            ob_start();
            imagejpeg($image);
            $imageData = ob_get_clean();

            imagedestroy($image);

            return response()->make($imageData, 200)->header('Content-Type', $mimeType);
        }

        return response()->make($fileContent, 200)->header('Content-Type', $mimeType);

    }

    public function download(int $id): BinaryFileResponse
    {
        $photo = Photo::query()->findOrFail($id);

        if (! Storage::disk('photos')->exists($photo->source_file)) {
            abort(404, 'File not found.');
        }

        $filePath = Storage::disk('photos')->path($photo->source_file);

        return response()->download($filePath, $photo->file_name);

    }
}
