<?php

namespace App\Biblioteca\Models;

use Illuminate\Database\Eloquent\Model;
use App\Biblioteca\Models\Libro as Libro;
use Illuminate\Database\Eloquent\Builder;

use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Editore extends Model
{
  use LogsActivity;
  protected $connection = 'db_biblioteca';
  protected $table = 'editore';

  protected $guarded = []; // all the fields are mass assignabe

  public function setEditoreAttribute($value) {
       $this->attributes['editore'] = strtoupper($value);
  }

  public function libri()
   {
       return $this->belongsToMany(Libro::class,'editore_libro','editore_id','libro_id');
   }


   protected static function boot()
   {
       parent::boot();

       static::addGlobalScope('singoli', function (Builder $builder) {
           $builder->where('tipedi', 'S');
       });
   }
   // SELECT * FROM editore WHERE tipedi='S'

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['editore'])
            ->dontLogIfAttributesChangedOnly(['tipedi'])
            ->logOnlyDirty();

    }
}
