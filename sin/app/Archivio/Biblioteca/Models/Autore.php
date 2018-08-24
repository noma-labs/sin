<?php

namespace App\Biblioteca\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Biblioteca\Models\Libro as Libro;
use Spatie\Activitylog\Traits\LogsActivity;

class Autore extends Model
{
  use LogsActivity;

  protected $connection = 'db_biblioteca';
  protected $table = 'autore';
  protected $primaryKey = "id";


  // log the changed attributes in the list for all these events
  protected static $logAttributes = ['autore'];
  // these attributes  don't need to trigger an activity being logged
  protected static $ignoreChangedAttributes = ['tip_aut'];
  // logs only attributes that has actually changed after the update
  protected static $logOnlyDirty = true;

  
  protected $guarded = []; // all the fields are mass assignabe

  protected static function boot()
   {
       parent::boot();

       static::addGlobalScope('tipaut', function (Builder $builder) {
           $builder->where('tipaut', 'S');
       });
   }


 public function setAutoreAttribute($value) {
      $this->attributes['autore'] = strtoupper($value);
 }


 public function libri()
  {
      return $this->belongsToMany(Libro::class,'autore_libro','autore_id','libro_id');
  }
}
