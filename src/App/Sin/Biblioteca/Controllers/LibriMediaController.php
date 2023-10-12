<?php

namespace App\Biblioteca\Controllers;

use App\Biblioteca\Models\Libro as Libro;
use App\Core\Controllers\BaseController as CoreBaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LibriMediaController extends CoreBaseController
{
    public function view($idLibro)
    {
        $libro = Libro::findOrFail($idLibro);

        return view('biblioteca.libri.media', compact('libro'));
    }

    public function store(Request $request, $idLibro)
    {
        $libro = Libro::findOrFail($idLibro);
        // $libro->addMedia($request->file)
        //       ->toMediaCollection('default','ftp'); //,"ftp");

        $libro->addMedia($request->file)
            ->toMediaCollection('default', 'ftp');

        $url = 'http://images.famigliacristiana.it/2018/5/nomadelfia-caimi_piccinni_2406463.jpg';
        $libro
            ->addMediaFromUrl($url)
            ->toMediaCollection('default', 'ftp');

        // Storage::disk('ftp')->put("didoprova.jpg",  $request->file);

        // fir multiple file
        // foreach ($request->file('files', []) as $key => $file) {
        //     $libro->addMedia($file)->toMediaCollection();
        // }

        // $fileAdders = $libro
        //   ->addMultipleMediaFromRequest($request->file('files', [])) //['file-one', 'file-two'])
        //   ->each(function ($fileAdder) {
        //       $fileAdder->toMediaLibrary();
        // return view("biblioteca.libri.media", compact("libro"));
        return redirect()->back()->withSucces('File digitale aggiunto correttament al libro');
    }

    public function destroy($idLibro, $mediaId)
    {
        $libro = Libro::findOrFail($idLibro);
        $libro->getMedia()
            ->keyBy('id')
            ->get($mediaId)
            ->delete();

        return redirect()->back()->withSuccess('File digitale eliminato');
    }

    public function destroyAll($idLibro)
    {
        $libro = Libro::findOrFail($idLibro);
        $libro->clearMediaCollection();

        return redirect()->back()->withSuccess("Tutti i file digitali del libro $libro->titolo  sono stati eliminati");

    }
}
