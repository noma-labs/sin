<?php

declare(strict_types=1);

namespace App\Admin\Models;

use App\Nomadelfia\Persona\Models\Persona;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\Traits\CausesActivity;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property string $username
 */
final class User extends Authenticatable
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

    public function persona(): HasOne
    {
        return $this->hasOne(Persona::class, 'id', 'persona_id');
    }

    protected function password(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(set: fn ($password): array => ['password' => bcrypt($password)]);
    }

    protected function casts(): array
    {
        return ['deleted_at' => 'datetime'];
    }
}
