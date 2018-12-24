<?php
namespace App\Officina\Controllers;
use App\Core\Controllers\BaseController as CoreBaseController;


class PatentiController extends CoreBaseController
{

  public function patenti(){
    return view('officina.patenti.index');
  }
  
}
