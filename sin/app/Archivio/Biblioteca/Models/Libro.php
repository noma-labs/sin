<?php
namespace App\Biblioteca\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\SortableTrait;
use App\Traits\Enums;

// Extrnal library to associate media files a model
//use Spatie\MediaLibrary\HasMedia;
//use Spatie\MediaLibrary\InteractsWithMedia;

use Spatie\Activitylog\Traits\LogsActivity;

use App\Biblioteca\Models\Autore as Autore;
use App\Biblioteca\Models\Editore as Editore;
use App\Biblioteca\Models\Prestito as Prestito;
use App\Biblioteca\Models\Classificazione as Classificazione;

class Libro extends Model #mplements HasMedia
{
//  use HasMediaTrait;
  use SortableTrait;
  use SoftDeletes;
  use Enums;
  use LogsActivity;

   // log the changed attributes in the list for all these events
  protected static $logAttributes = ['titolo', 'collocazione', 'classificazione_id',  'critica','categoria', 'dimensione', 'data_pubblicazione','isbn', 'note'];
  // these attributes  don't need to trigger an activity being logged
  protected static $ignoreChangedAttributes = ['deleted_note','tobe_printed'];
  // logs only attributes that has actually changed after the update
  protected static $logOnlyDirty = true;

  protected $connection = 'db_biblioteca';
  protected $table = 'libro';
  protected $primaryKey = "id";
  protected $dates = ['deleted_at'];

  protected $enumCategoria = [  
    'piccoli',
    'elementari',
    'medie',
    'superiori',
    'adulti'
   ];

   protected $enumCritica = [ 
     1,2,3,4,5
    ];

  protected $guarded = []; // all the fields are mass assignabe

  public function getLogNameToUse(string $eventName = ''): string
  {
     return 'biblioteca';
  }

  public function setTitoloAttribute($value) {
       $this->attributes['titolo'] = strtoupper($value);
  }

  public function setNoteAttribute($value) {
       $this->attributes['note'] = strtoupper($value);
  }

  // public function registerMediaConversions(Media $media = null)
  //   {
  //       $this->addMediaConversion('thumb')
  //             ->width(368)
  //             ->height(232)
  //             ->sharpen(10);
  //   }

  public function classificazione(){
    return $this->belongsTo(Classificazione::class, "classificazione_id");
  }

  public function editori(){
    return $this->belongsToMany(Editore::class,'editore_libro','libro_id','editore_id');
  }

  public function autori(){
    return $this->belongsToMany(Autore::class,'autore_libro','libro_id','autore_id');
  }

  public function prestiti(){
    return $this->hasMany(Prestito::class, "libro_id");
  }

  public function inPrestito()
  {
      $prestiti = $this->prestiti()->where('in_prestito', 1)->get();
      return count($prestiti) > 0;
  }

  public function scopeTobePrinted($query)
  {
    return $query->where('tobe_printed', 1)->orderBy("collocazione");
  }

  public function scopeEditori($query)
  {
    return $query->select("editore")->groupBy("editori"); //->orderBY("EDITORE");
  }

  public function scopeAutori($query)
  {
    return $query->select("autore")->groupBy("autore"); //->orderBY("AUTORE");
  }

}
