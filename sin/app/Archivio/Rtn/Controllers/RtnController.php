<?php
namespace App\Rtn\Controllers;

use App\Core\Controllers\BaseController;

/**
 * Controller per gestire le funzioni base di Rtn
 *
 * @author Josè Juan 18/09/2018
 */
class RtnController extends BaseController
{
	public function index(){
		return view('rtn.index');
	}
}