<?php
namespace App\Patente\Controllers;
use App\Patente\Models\Patente;
use App\Patente\Models\CategoriaPatente;
use App\Nomadelfia\Models\Persona;
use Illuminate\Http\Request;

use App\Core\Controllers\BaseController as CoreBaseController;
use Validator;


class ApiController extends CoreBaseController
{
    public function patente(Request $request,$numero)
    {
        $p = Patente::where("numero_patente",$numero)->with("categorie")->first();
        return response()->json($p);
    }

    /**
     * Find a permission by its name.
     *
     * @param string $name
     * @param string|null $guardName
     *
     * @throws \Spatie\Permission\Exceptions\PermissionDoesNotExist
     *
     * @return Permission
     */

    /**
	* Ritorna le categorie di una patente.
	*    /?filtro=possibili : ritorna le categorie non ancora assegnate alla patente 
    * @param string $numero 

    * @return array  $Patente
    
	* @author Davide Neri
	**/
    public function patenteCategorie(Request $request,$numero)
    {
        if($request->input('filtro') == "possibili")
            $p = CategoriaPatente::whereDoesntHave('patenti', function($query) use ($numero){
                $query->where('patenti_categorie.numero_patente', '=', $numero);
                })->get();
        else
            $p = Patente::where("numero_patente",$numero)->with("categorie")->first();
    

        return response()->json($p);
    }

}