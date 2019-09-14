<?php
namespace App\Patente\Models;

use App\Nomadelfia\Models\Persona;
use App\Patente\Models\CategoriaPatente;
use App\Patente\Models\CQC;
use App\Patente\Models\Patente;
use App\Traits\SortableTrait;
use Carbon;
use Illuminate\Database\Eloquent\Model;

class Patente extends Model
{
    use SortableTrait;

    protected $connection = 'db_patente';
    protected $table = 'persone_patenti';
    protected $primaryKey = "numero_patente";
    public $increment = false;
    public $keyType = 'string';

    // protected $hidden = ['pivot'];

    public $timestamps = false;
    protected $guarded = [];

    public function persona()
    {
        return $this->belongsTo(Persona::class, 'persona_id', 'id');
    }

    /**
     * Ritorna solo le patenti con la commissione.
     *
     * @author: Davide Neri
     */
    public function scopeConCommissione($query)
    {
        return $query->where('stato', 'commissione');
    }

    /**
     * Ritorna tutte le occorrenze distinte del campo "rilasciata_dal" .
     *
     * @author: Davide Neri
     */
    public function scopeRilasciataDal($query)
    {
        return $query->select('rilasciata_dal')->distinct();
    }

    public function categorie()
    {
        return $this->belongsToMany(CategoriaPatente::class, 'patenti_categorie', 'numero_patente', 'categoria_patente_id');
    }

    public function cqc()
    {
        return $this->belongsToMany(CQC::class, 'patenti_categorie', 'numero_patente', 'categoria_patente_id')
            ->withPivot('data_rilascio', 'data_scadenza');
    }

    public function hasCqcPersone(){
      return $this->cqcPersone() != null;
    }

    
    public function hasCqcMerci(){
      return $this->cqcMerci() != null;
    }

    public function cqcPersone()
    {
        return $this->cqc()->wherePivot('categoria_patente_id', 16)->first();
    }

    public function cqcMerci()
    {
        return $this->cqc()->wherePivot('categoria_patente_id', 17)->first();
    }

    public function categorieAsString()
    {
        return $this->categorie()->get()->implode('categoria', ',');
    }

    /**
     * Ritorna le patenti che scadono entro $days giorni
     * @param int $giorni: numero di giorni entro il quale le patenti scadono.
     * @author Davide Neri
     */
    public function scopeInScadenza($query, int $days)
    {
        $data = Carbon::now()->addDays($days)->toDateString();
        return $query->where('data_scadenza_patente', '<=', $data)
            ->where('data_scadenza_patente', ">=", Carbon::now()->toDateString());

    }

    /**
     * Ritorna le patenti la cui data di scadenza è maggiore di oggi.
     * @author Davide Neri
     */
    public function scopeNonScadute($query)
    {
        $data = Carbon::now()->toDateString();
        return $query->where('data_scadenza_patente', '>', $data);
    }

    /**
     * Ritorna le patenti che sono scadute da un numero di $giorni da oggi.
     * Se $day ==null ritorna tutte le patenti scadute da oggi.
     * @param int $giorni: numero di giorni di scadenza
     * @author Davide Neri
     */
    public function scopeScadute($query, int $days = null)
    {

        if ($days != null) {
            $data = Carbon::now()->subDays($days)->toDateString();
            return $query->where('data_scadenza_patente', '>=', $data)
                ->where('data_scadenza_patente', "<=", Carbon::now()->toDateString());
        } else {
            return $query->where('data_scadenza_patente', "<=", Carbon::now()->toDateString());
        }
    }

    /** Ritorna le patenti a cui è stata assegnata la commissione
     * @author Davide Neri
     */
    public function scopeConCommisione($query)
    {
        return $query->where('stato', '=', 'commissione');
    }

    /** Ritorna le patenti senza la  commissione
     * @author Davide Neri
     */
    public function scopeSenzaCommisione($query)
    {
        return $query->whereNull('stato')
            ->orWhere("stato", "!=", 'commissione');
    }

}
