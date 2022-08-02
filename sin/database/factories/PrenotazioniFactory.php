<?php
namespace Database\Factories;

use App\Officina\Models\Prenotazioni;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class PrenotazioniFactory extends Factory
{

    protected $model = Prenotazioni::class;

    public function definition()
    {
        return [
//            'cliente_id' => $cliente->id,
//            'veicolo_id' => $veicolo->id,
//            'meccanico_id' => $meccanico->id,
//            'data_partenza' => $this->faker->date('yy-mm-dd'),
//            'ora_partenza' =>  $ora_partenza,
//            'data_arrivo' => $this->faker->date('yy-mm-dd'),
//            'uso_id' => $uso->ofus_iden,
//            'ora_arrivo' => $ora_arrivo,
            'note' => $this->faker->text,
            'destinazione' => $this->faker->title
        ];
    }

//    public function today()
//    {
//        return $this->state(function (array $attributes) {
//            return [
//                'data_partenza' => Carbon::now()->toDateString(),
//                'data_arrivo' => Carbon::now()->toDateString(),
//                'ora_partenza' =>  "08:00",
//                'ora_arrivo' => "11:00",
//               ];
//        });
//    }

    public function partitaIeri(string $ora_partenza="08:00")
    {
        return $this->state(function (array $attributes) use ($ora_partenza){
            return [
                'data_partenza' => Carbon::now()->subDay()->toDateString(),
                'ora_partenza' =>  $ora_partenza,
            ];
        });
    }

    public function ritornaDomani(string $ora_arrivo="12:00")
    {
        return $this->state(function (array $attributes) use ($ora_arrivo){
            return [
                'data_arrivo' => Carbon::now()->addDay()->toDateString(),
                'ora_arrivo' => $ora_arrivo,
            ];
        });
    }
}

