<?php

namespace App\Biblioteca\Models;

use Illuminate\Database\Eloquent\Model;
use App\Biblioteca\Models\Libro as Libro;
use Illuminate\Database\Eloquent\Builder;

use Spatie\Activitylog\Traits\LogsActivity;

class Editore extends Model
{
  use LogsActivity;
  protected $connection = 'db_biblioteca';
  protected $table = 'editore';
  protected $primaryKey = "id";

  protected static $logAttributes = ['editore'];
  // these attributes  don't need to trigger an activity being logged
  protected static $ignoreChangedAttributes = ['tipedi'];
  // logs only attributes that has actually changed after the update
  protected static $logOnlyDirty = true;

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

}
