<?php

namespace Domain\Photo\Models;

use Database\Factories\PhotoFactory;
use Domain\Nomadelfia\Persona\Models\Persona;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Photo extends Model
{
    use HasFactory, SoftDeletes;

    public $timestamps = true;

    protected $connection = 'db_foto';

    protected $table = 'photos';

    protected $primaryKey = 'uid';

    protected $guarded = [];

    protected $casts = [
        'taken_at' => 'datetime',
    ];

    protected static function newFactory(): PhotoFactory
    {
        return PhotoFactory::new();
    }

    public function persone(): BelongsToMany
    {
        return $this->belongsToMany(Persona::class, 'db_foto.foto_persone', 'photo_id', 'persona_id');
    }
}
