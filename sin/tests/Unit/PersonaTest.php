<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Nomadelfia\Models\Persona;

class PersonaTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testPersonaEntrata()
    {
        /*$persona = Persona::create(['nominativo'=>"my-test-3", 
                              'sesso'=>"M",
                              'nome'=>"dido",
                              "cognome"=>"neri",
                              "provincia_nascita"=>"GR",
                              'data_nascita'=>"1990-12-12",
                              // TODO: delete this column because the categoria is a many to many relationship in to the persone_categoria table
                              'categoria_id' =>1,
                              'id_arch_pietro'=>0,
                              'id_arch_enrico'=>0
                              ]
                            );
                            */
        $persona = Persona::find(388);
        $res = $persona->entrataNomadelfia("2020-12-12");

        $cat = $persona->categoriaAttuale();
        $this->assertEquals($cat->id, 1);
        $this->assertEquals($cat->data_inizio, "2020-12-12");
    }
}
