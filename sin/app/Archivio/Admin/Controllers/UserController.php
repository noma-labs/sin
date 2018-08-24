<?php
namespace App\Admin\Controllers;

use Illuminate\Http\Request;
use App\Admin\Models\User;
use Auth;

use App\Admin\Models\Ruolo;
use App\Admin\Models\Risorsa;

use App\Core\Controllers\BaseController as Controller;


//Enables us to output flash messaging
// use Session;

class UserController extends Controller {

    public function __construct() {
        // $this->middleware(['auth', 'isAdmin','isMaster']); //isAdmin middleware lets only users with a //specific permission permission to access these resources
    }

    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function index() {
       //Get all users and pass it to the view
        $users = User::withTrashed()->get();  //all();
        return view('admin.auth.users.index')->with('users', $users);
    }

    /**
    * Show the form for creating a new resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function create() {
       //Get all roles and pass it to the view
        $roles = Ruolo::get();
        return view('admin.auth.users.create', ['roles'=>$roles]);
    }

    /**
    * Store a newly created resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
    public function store(Request $request) {
       // Validate name, email and password fields
        $this->validate($request, [
            'persona_id'=>'required',
            'username' => "required|max:20|unique:utenti",
            // 'email'=>'required|email', //|unique:users
            'password'=>'required|min:6|confirmed'
        ]);

        $user = User::create($request->only('email', 'username' , 'persona_id', 'password')); //Retrieving only the email and password data

        $roles = $request['roles']; //Retrieving the roles field
        //Checking if a role was selected
        if (isset($roles)) {
            foreach ($roles as $role) {
            $role_r = Ruolo::where('id', '=', $role)->firstOrFail();
            $user->assignRole($role_r); //Assigning role to user
            }
        }
        return redirect()->route('users.index')->withSuccess("Utente aggiunto correttamente");
    }

    /**
    * Display the specified resource.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function show($id) {
        return redirect('users.index');
    }

    /**
    * Show the form for editing the specified resource.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function edit($id) {
        $user = User::findOrFail($id); //Get user with specified id
        $roles = Ruolo::get(); //Get all roles
        return view('admin.auth.users.edit', compact('user', 'roles')); //pass user and roles data to view

    }

    /**
    * Update the specified resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function update(Request $request, $id) {
        $user = User::findOrFail($id); //Get role specified by id

        $this->validate($request, [
            'persona_id'=>'required',
            'email'=>'required|email', //unique:users,email,'.$id,
            'password'=>'required|min:6|confirmed'
        ]);
        $input = $request->only(['persona_id', 'username','email','password']); // 'password', Retreive the name, email and password fields
        $roles = $request['roles']; //Retreive all roles
        $user->fill($input)->save();

        if (isset($roles)) {
            $user->ruoli()->sync($roles);  //If one or more role is selected associate user to roles
        }
        else {
            $user->ruoli()->detach(); //If no role is selected remove exisiting role associated to a user
        }
        return redirect()->route('users.index')->withSuccess("Utente modificato correttamente");
    }

    /**
    * Remove the specified resource from storage.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function destroy($id)
    {
    //Find a user with a given id and delete
        $user = User::findOrFail($id);

        if ($user->username == "Admin") {
            return redirect()->route('users.index')->withError("Non puoi elimiare l'utente $user->username");
        }
        else{
        $user->delete();
        return redirect()->route('users.index')->withWarning("Utente $user->username disabilitato correttamente");
      }
    }

    public function restore($id)
    {
      $user = User::withTrashed()->where('id', $id);
      $user->restore();
      return redirect()->route('users.index')->withSuccess("Utente ripristinato correttamente");
  }

}
