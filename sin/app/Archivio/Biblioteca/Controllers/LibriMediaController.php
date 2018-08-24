<?php

namespace App\Biblioteca\Controllers;


use App\Biblioteca\Models\Libro as Libro;
use Illuminate\Http\Request;
use App\Core\Controllers\BaseController as CoreBaseController;

class LibriMediaController extends CoreBaseController
{

  public function view($idLibro){
    $libro = Libro::findOrFail($idLibro);
    return view("biblioteca.libri.media", ["libro"=>$libro]);
  }

  public function store(Request $request, $idLibro){
    $libro = Libro::findOrFail($idLibro);
    $libro->addMedia($request->file)
          ->toMediaCollection();

    // fir multiple file
    // foreach ($request->file('files', []) as $key => $file) {
    //     $libro->addMedia($file)->toMediaCollection();
    // }

    // $fileAdders = $libro
    //   ->addMultipleMediaFromRequest($request->file('files', [])) //['file-one', 'file-two'])
    //   ->each(function ($fileAdder) {
    //       $fileAdder->toMediaLibrary();
    return redirect()->back()->withSucces("File digitale aggiunto a libro $libro->TITOLO");
  }

  public function destroy($idLibro, $mediaId){
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
      return redirect()->back()->withSuccess("Tutti i file digitali del libro $libro->TITOLO  sono stati eliminati");

  }
}
