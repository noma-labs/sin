<?php

namespace App\Admin\Models;

use Domain\Nomadelfia\Persona\Models\Persona;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\Traits\CausesActivity;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use CausesActivity;
    use Notifiable;
    use SoftDeletes;
    use HasRoles;

    protected $connection = 'db_auth';

    protected $table = 'utenti';

    protected $primaryKey = 'id';

    protected $dates = ['deleted_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'username', 'persona_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function setPasswordAttribute($password)// mutator that encrypt all the password of the users
    {

        $this->attributes['password'] = bcrypt($password);
    }

    public function persona()
    {
        return $this->hasOne(Persona::class, 'id', 'persona_id');
    }

    /**
     * An user may have multiple roles.
     */
    public function ruoli()
    {
        return $this->belongsToMany(
            Ruolo::class,
            'utenti_ruoli',
            'utente_id',
            'ruolo_id'
        );
    }

    //    /**
    //     * Determine if the model has (one of) the given role(s).
    //     *
    //     * @param string|array|\Spatie\Permission\Contracts\Role|\Illuminate\Support\Collection $roles
    //     *
    //     * @return bool
    //     */
    //    public function hasRole($roles): bool
    //    {
    //        if (is_string($roles) && false !== strpos($roles, '|')) {
    //            $roles = $this->convertPipeToArray($roles);
    //        }
    //        if (is_string($roles)) {
    //            return $this->ruoli->contains('nome', $roles);
    //        }
    //        if ($roles instanceof Ruolo) {
    //            return $this->ruoli->contains('id', $roles->id);
    //        }
    //        if (is_array($roles)) {
    //            foreach ($roles as $role) {
    //                if ($this->hasRole($role)) {
    //                    return true;
    //                }
    //            }
    //            return false;
    //        }
    //        return $roles->intersect($this->ruoli)->isNotEmpty();
    //    }
    //
    //    /**
    //     * Determine if the model has any of the given role(s).
    //     *
    //     * @param string|array|\Spatie\Permission\Contracts\Role|\Illuminate\Support\Collection $roles
    //     *
    //     * @return bool
    //     */
    //    public function hasAnyRole($roles): bool
    //    {
    //        return $this->hasRole($roles);
    //    }
    //
    //
    //    /**
    //     * Determine if the user has the permesso to a given risorsa.
    //     *
    //     * @param string|\Spatie\Permission\Contracts\Permission $risorsa
    //     * @param string $permesso
    //     *
    //     * @return bool
    //     */
    //    public function hasPermissionTo($risorsa, $permesso): bool
    //    {
    //        $risorsa = Risorsa::findByName($risorsa);
    //        $ruoli = $risorsa->getRuoliForPermesso($permesso)->get();
    //        return $ruoli->intersect($this->ruoli)->isNotEmpty();
    //    }
    //
    //    /**
    //     * Assign the given role to the model.
    //     *
    //     * @param array|string|\Spatie\Permission\Contracts\Role ...$roles
    //     *
    //     * @return $this
    //     */
    //    public function assignRole(...$roles)
    //    {
    //        $roles = collect($roles)
    //            ->flatten()
    //            ->filter(function ($role) {
    //                return $role instanceof Ruolo;
    //            })
    //            ->all();
    //        $this->ruoli()->saveMany($roles);
    //        return $this;
    //    }
    //
    //
    //    protected function convertPipeToArray(string $pipeString)
    //    {
    //        $pipeString = trim($pipeString);
    //        if (strlen($pipeString) <= 2) {
    //            return $pipeString;
    //        }
    //        $quoteCharacter = substr($pipeString, 0, 1);
    //        $endCharacter = substr($quoteCharacter, -1, 1);
    //        if ($quoteCharacter !== $endCharacter) {
    //            return explode('|', $pipeString);
    //        }
    //        if (!in_array($quoteCharacter, ["'", '"'])) {
    //            return explode('|', $pipeString);
    //        }
    //        return explode('|', trim($pipeString, $quoteCharacter));
    //    }

}
