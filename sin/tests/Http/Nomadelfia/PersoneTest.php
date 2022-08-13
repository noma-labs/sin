<?php

use App\Nomadelfia\Controllers\PersoneController;
use App\Nomadelfia\Models\GruppoFamiliare;
use App\Nomadelfia\Models\Persona;
use Tests\TestCase;

class PersoneTest extends TestCase
{

    /** @test */
    public function can_edit_dati_anagrafici_persona()
    {
        $persona = Persona::factory()->maggiorenne()->maschio()->create();

        $newName = "My-name";
        $newSurname = "My-surnema";
        $newNascita = "2022-12-12";
        $newLuogo = "my-luogo";
        $newSesso = "F";
        $newbiografia = "Sono nato e morto";
        $this->login();
        $this->post(action([PersoneController::class, 'modificaDatiAnagraficiConfirm'], ['idPersona' => $persona->id]),
            [
                'nome' =>  $newName,
                'cognome' => $newSurname,
                'datanascita' => $newNascita,
                'luogonascita' => $newLuogo,
                'sesso' => $newSesso,
                'biografia' => $newbiografia,
            ])
            ->assertRedirect()
            ->assertRedirectContains(route('nomadelfia.persone.dettaglio', ['idPersona' => $persona->id]));

        $p = Persona::findOrFail($persona->id);
        $this->assertEquals($newSurname, $p->cognome);
        $this->assertEquals($newSesso, $p->sesso);
        $this->assertEquals($newLuogo, $p->provincia_nascita);
        $this->assertEquals($newName, $p->nome);
        $this->assertEquals($newNascita, $p->data_nascita);
        $this->assertEquals($newbiografia, $p->biografia);
    }

}