<?php

declare(strict_types=1);

namespace Tests\Scuola\Unit;

use App\Scuola\Models\Elaborato;

it('finds the ciclo of the classi of an elaborato', function (?string $classi, ?string $ciclo): void {
    $elaborato = Elaborato::factory()->make(['classi' => $classi]);

    expect($elaborato->ciclo())->toBe($ciclo);
})->with([
    'prescuola' => ['prescuola', 'prescuola'],
    'one classe of the elementari' => ['3 elementare', 'elementari'],
    'classi of the same ciclo' => ['1 media,2 media', 'medie'],
    'the whole ciclo' => ['superiori', 'superiori'],
    'classi of different cicli' => ['5 elementare,1 media', 'tutti'],
    'tutti i cicli' => ['tutti i cicli', 'tutti'],
    'personale' => ['personale', null],
    'no classi' => [null, null],
]);
