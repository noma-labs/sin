<?php

namespace App\Admin\Models;

use Domain\Nomadelfia\Persona\Models\Persona;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\Traits\CausesActivity;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property string $username
 */
class User extends Authenticatable
{
    use CausesActivity;
    use HasRoles;
    use Notifiable;
    use SoftDeletes;

    protected $connection = 'db_auth';

    protected $table = 'utenti';

    protected $primaryKey = 'id';

    protected $fillable = [
        'name', 'email', 'password', 'username', 'persona_id',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];
    protected $casts = ['deleted_at' => 'datetime'];

    public function setPasswordAttribute($password)// mutator that encrypt all the password of the users
    {

        $this->attributes['password'] = bcrypt($password);
    }

    public function persona(): HasOne
    {
        return $this->hasOne(Persona::class, 'id', 'persona_id');
    }
}
