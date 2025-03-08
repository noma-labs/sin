<?php

declare(strict_types=1);

namespace App\Officina\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $marca_id
 * @property string $nome
 */
final class Modelli extends Model
{
    public $timestamps = false;

    protected $connection = 'db_officina';

    protected $table = 'modello';

    protected $primaryKey = 'id';

    protected $guarded = [];

    public function marca()
    {
        // return $this->hasOne(Marche::class, 'id', 'marca_id');
        return $this->belongsTo(Marche::class, 'marca_id');
    }

    protected function nome(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(set: function ($value): array {
            return ['nome' => mb_strtoupper($value)];
        });
    }
}
