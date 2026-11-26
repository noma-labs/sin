<?php

declare(strict_types=1);

use App\Anagrafe\Persona\Models\Persona;
use App\Photo\Actions\CodaAvatarAction;
use App\Photo\Models\Photo;
use Illuminate\Support\Facades\DB;

/**
 * Tags $persona in $quante new photos: by persona_id, or only by their FOTO name when $soloNome.
 */
function taggaInFoto(Persona $persona, int $quante, string $nome = 'NOME FOTO', bool $soloNome = false): void
{
    foreach (range(1, $quante) as $_) {
        DB::connection('db_foto')->table('photos_people')->insert([
            'photo_id' => Photo::factory()->create()->id,
            'persona_id' => $soloNome ? null : $persona->id,
            'persona_nome' => $nome,
        ]);
    }
}

it('queues the persone without an avatar who are in the photos, the ones with more photos first', function (): void {
    $poche = Persona::factory()->create();
    taggaInFoto($poche, 1);
    $molte = Persona::factory()->create();
    taggaInFoto($molte, 3);
    $conAvatar = Persona::factory()->create(['avatar_path' => 'persone/avatar.jpg']);
    taggaInFoto($conAvatar, 2);
    $senzaFoto = Persona::factory()->create();

    $coda = resolve(CodaAvatarAction::class);

    expect(array_values(array_intersect($coda->idsDaFare(), [$poche->id, $molte->id, $conAvatar->id, $senzaFoto->id])))->toBe([$molte->id, $poche->id])
        ->and($coda->numeroFotoPerPersona())->toMatchArray([$molte->id => 3, $poche->id => 1]);
});

it('keeps the counts of the photos in the cache until they expire', function (): void {
    $persona = Persona::factory()->create();
    taggaInFoto($persona, 1);
    $coda = resolve(CodaAvatarAction::class);
    $coda->numeroFotoPerPersona();

    taggaInFoto($persona, 1);

    expect($coda->numeroFotoPerPersona()[$persona->id])->toBe(1);
    $this->travel(CodaAvatarAction::CACHE_SCADUTA + 1)->seconds();
    expect($coda->numeroFotoPerPersona()[$persona->id])->toBe(2);
});

it('counts the photos where the persona is tagged only by their FOTO name', function (): void {
    $idAlfa = fake()->unique()->numberBetween(100000, 999999);
    $nomeNelleFoto = 'PERSONA '.$idAlfa;
    DB::connection('db_nomadelfia')->table('alfa_enrico_15_feb_23')->insert(['id' => $idAlfa, 'FOTO' => $nomeNelleFoto]);
    $persona = Persona::factory()->withIdEnrico($idAlfa)->create();
    taggaInFoto($persona, 1, $nomeNelleFoto);
    taggaInFoto($persona, 2, $nomeNelleFoto, soloNome: true);

    expect(resolve(CodaAvatarAction::class)->numeroFotoPerPersona()[$persona->id])->toBe(3);
});

it('lists the persone of the photos with an avatar, the last saved first, the ones without a date last', function (): void {
    $vecchia = Persona::factory()->create(['avatar_path' => 'persone/a.jpg', 'avatar_aggiornato_il' => '2026-11-20 09:00:00']);
    $recente = Persona::factory()->create(['avatar_path' => 'persone/b.jpg', 'avatar_aggiornato_il' => '2026-11-26 09:00:00']);
    $senzaData = Persona::factory()->create(['avatar_path' => 'persone/c.jpg', 'avatar_aggiornato_il' => null]);
    $senzaAvatar = Persona::factory()->create();
    foreach ([$vecchia, $recente, $senzaData, $senzaAvatar] as $persona) {
        taggaInFoto($persona, 1);
    }
    $fuoriDalleFoto = Persona::factory()->create(['avatar_path' => 'persone/d.jpg', 'avatar_aggiornato_il' => '2026-11-26 10:00:00']);

    $fatte = resolve(CodaAvatarAction::class)->fatte()
        ->whereIn('persone.id', [$vecchia->id, $recente->id, $senzaData->id, $senzaAvatar->id, $fuoriDalleFoto->id])
        ->get()
        ->modelKeys();

    expect($fatte)->toBe([$recente->id, $vecchia->id, $senzaData->id]);
});

it('gives the next persona of the queue, also for the one that just got an avatar, none after the last one', function (): void {
    $prima = Persona::factory()->create();
    taggaInFoto($prima, 1);
    $ultima = Persona::factory()->create();
    taggaInFoto($ultima, 1);
    $fuoriDalleFoto = Persona::factory()->create();

    $coda = resolve(CodaAvatarAction::class);

    expect($coda->prossima($prima))->toBe($ultima->id);
    $prima->update(['avatar_path' => 'persone/prima.jpg']);
    expect($coda->prossima($prima))->toBe($ultima->id)
        ->and($coda->prossima($ultima))->toBeNull()
        ->and($coda->prossima($fuoriDalleFoto))->toBeNull();
});
