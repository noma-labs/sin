<?php

namespace App\Agraria\Models;

use Illuminate\Database\Eloquent\Model;

class Manutenzione extends Model
{
    protected $connection = 'db_agraria';
    protected $table = 'manutenzione';
    protected $guarded = [];
    public $timestamps = false;

    public function programmate(){
        return $this->belongsToMany(ManutenzioneProgrammata::class, 'manutenzione_tipo', 'manutenzione', 'tipo');
    }

    public function mezzo(){
        return $this->hasOne(MezzoAgricolo::class, 'id', 'mezzo_agricolo');
    }

    public function lavoriToString(){
        $res = [];
        if($this->lavori_extra != null && !ctype_space($this->lavori_extra)){
            array_push($res, strtolower($this->lavori_extra));
        }
        $prog = $this->programmate()->get();
        if($prog->isNotEmpty()){
            foreach ($prog as $p) {
                array_push($res, strtolower($p->nome));
            }
        }
        return implode(', ', $res);
    }
}
