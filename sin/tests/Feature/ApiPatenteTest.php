<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ApiPatenteTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $patente = [
            "persona_id"=> 2,
            "numero_patente"=> "prova-dadd",
            "rilasciata_dal"=> "comune",
            "data_rilascio_patente"=> "2000-01-01" ,
            "data_scadenza_patente"=> "2000-01-12",
            "stato"=> 'commissione',
            "note"=> "note prova",
            "categorie" => [  
                       "categoria" => [ 
                          'id' =>6,
                          "categoria"=>"B",
                          "descrizione"=>"ETÀ MINIMA RICHIESTA=> 18 ANNI.",
                          "note"=>"",
                       ],
                       "data_rilascio"=>"2017-11-01",
                       "data_scadenza"=>"2018-10-11"
                    ]
             ];
        $response = $this->withHeaders([
            'Content-Type','application/json'
        ])->json('POST', '/api/patente', $patente);
        dd($response->getContent());
        $response
            ->assertStatus(201);
        //$this->assertTrue(true);
    }
}
