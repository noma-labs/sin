<?php
namespace App\Admin\Models;

use Illuminate\Database\Eloquent\Model;


class Sistema extends Model
{
    protected $connection = 'db_auth';
    protected $table = 'sistemi';
    protected $primaryKey = "id";

     public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nome', 'descrizione'
    ];

    public function risorse(){
        return $this->hasMany(Risorsa::class,'sistema_id');
    }

}
