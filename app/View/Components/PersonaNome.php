<?php

declare(strict_types=1);

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

/**
 * Shows a persona as "Nome Cognome", followed by the nominativo when it differs from the name.
 *
 * The persona can be any object exposing the `nome`, `cognome` and `nominativo` properties
 * (Persona model, Studente model, DTO or a raw query row).
 */
final class PersonaNome extends Component
{
    public string $nomeVisibile;

    public ?string $nominativoVisibile;

    public function __construct(public object $persona)
    {
        $this->nomeVisibile = self::nomeCognome($persona);
        $this->nominativoVisibile = self::nominativoDiverso($persona);
    }

    /**
     * Plain-text version for places where HTML is not allowed (select options, messages, exports).
     */
    public static function testo(object $persona): string
    {
        $nominativo = self::nominativoDiverso($persona);

        return $nominativo === null
            ? self::nomeCognome($persona)
            : self::nomeCognome($persona)." ($nominativo)";
    }

    public static function nomeCognome(object $persona): string
    {
        $nomeCognome = mb_trim(($persona->nome ?? '').' '.($persona->cognome ?? ''));

        return $nomeCognome === '' ? (string) ($persona->nominativo ?? '') : $nomeCognome;
    }

    /**
     * The nominativo, or null when it is empty or just repeats nome and cognome (in any order).
     */
    public static function nominativoDiverso(object $persona): ?string
    {
        $nominativo = mb_trim((string) ($persona->nominativo ?? ''));
        $nome = mb_trim((string) ($persona->nome ?? ''));
        $cognome = mb_trim((string) ($persona->cognome ?? ''));

        if ($nominativo === '' || ($nome === '' && $cognome === '')) {
            return null;
        }

        $nominativoNormalizzato = mb_strtolower($nominativo);
        if (in_array($nominativoNormalizzato, [mb_strtolower("$nome $cognome"), mb_strtolower("$cognome $nome")], true)) {
            return null;
        }

        return $nominativo;
    }

    public function render(): View
    {
        return view('components.persona-nome');
    }
}
