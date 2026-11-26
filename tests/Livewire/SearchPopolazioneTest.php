<?php

declare(strict_types=1);

namespace Tests\Livewire;

use App\Anagrafe\Persona\Models\Persona;
use App\Livewire\SearchPopolazione;
use App\Nomadelfia\GruppoFamiliare\Models\GruppoFamiliare;
use App\Nomadelfia\PopolazioneNomadelfia\Actions\EntrataMaggiorenneSingleAction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;

it('shows the avatar of the persone present, with the address that changes with the persona', function (): void {
    $persona = Persona::factory()->maggiorenne()->maschio()->create(['nominativo' => 'presentissimo', 'avatar_path' => 'persone/presente.jpg']);
    app(EntrataMaggiorenneSingleAction::class)->execute($persona, Carbon::parse('2000-01-01'), GruppoFamiliare::all()->random());
    // a persona updated before their row of the popolazione, which has its own updated_at
    DB::connection('db_nomadelfia')->table('persone')->where('id', $persona->id)->update(['updated_at' => '2020-01-01 10:00:00']);

    Livewire::test(SearchPopolazione::class)
        ->call('search', 'presentissimo')
        ->assertSee('<img src="'.e($persona->refresh()->avatarUrl()).'"', false);
});
