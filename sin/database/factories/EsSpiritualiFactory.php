<?php
namespace Database\Factories;


use App\Nomadelfia\Models\EserciziSpirituali;
use App\Nomadelfia\Models\Persona;
use Illuminate\Database\Eloquent\Factories\Factory;

class EsSpiritualiFactory extends Factory
{

    protected $model = EserciziSpirituali::class;

    public function definition()
    {
        $resp = Persona::factory()->cinquantenne()->maschio()->create();
        return [
            'turno'=>"1-turno",
            'responsabile_id'=>$resp->id,
            'data_inizio'=>$this->faker->date,
            "data_fine"=>$this->faker->date,
            "luogo"=>$this->faker->city,
            'stato'=>'1',
        ];
    }

    public function turno1()
    {
        return $this->state(function (array $attributes) {
            return [
                'turno' => "1-turno",
            ];
        });
    }

    public function turno12()
    {
        return $this->state(function (array $attributes) {
            return [
                'stato' => "0",
            ];
        });
    }

    public function disattivo()
    {
        return $this->state(function (array $attributes) {
            return [
                'stato' => "0",
            ];
        });
    }

    public function attivo()
    {
        return $this->state(function (array $attributes) {
            return [
                'stato' => "1",
            ];
        });
    }


}


