<?php

declare(strict_types=1);

namespace App\View\Components;

use App\Anagrafe\Persona\Models\Persona;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use InvalidArgumentException;

/**
 * Shows the avatar of a persona: their photo when they have one, otherwise the initials on the color of the sesso.
 *
 * The persona can be any object exposing `id`, `nome`, `cognome`, `sesso` and `avatar_path` (Persona model, DTO or
 * a raw query row); without `avatar_path` the initials are shown. The avatar is decorative: the name is always
 * written next to it.
 */
final class PersonaAvatar extends Component
{
    /**
     * The sizes: xs 24px (autocomplete), sm 34px (cards, ricorrenze), md 56px (albero), lg 96px (header of the profile).
     */
    public const MISURE = ['xs', 'sm', 'md', 'lg'];

    public ?string $urlFoto;

    public string $iniziali;

    /**
     * m, f, or n when the sesso is missing: the color of the initials.
     */
    public string $sesso;

    public function __construct(public object $persona, public string $size = 'sm')
    {
        if (! in_array($size, self::MISURE, true)) {
            throw new InvalidArgumentException("The size of the avatar must be one of xs, sm, md, lg: `{$size}` given");
        }

        $this->urlFoto = self::urlFoto($persona);
        $this->iniziali = self::inizialiDi($persona);
        $this->sesso = match ($persona->sesso ?? null) {
            'M' => 'm',
            'F' => 'f',
            default => 'n',
        };
    }

    public function render(): View
    {
        return view('components.persona-avatar');
    }

    /**
     * The first letters of nome and cognome, or of the nominativo when both are missing.
     */
    private static function inizialiDi(object $persona): string
    {
        $iniziali = mb_substr(mb_trim((string) ($persona->nome ?? '')), 0, 1).mb_substr(mb_trim((string) ($persona->cognome ?? '')), 0, 1);
        if ($iniziali === '') {
            $iniziali = mb_substr(mb_trim((string) ($persona->nominativo ?? '')), 0, 1);
        }

        return mb_strtoupper($iniziali);
    }

    private static function urlFoto(object $persona): ?string
    {
        if ($persona instanceof Persona) {
            return $persona->avatarUrl();
        }

        return empty($persona->avatar_path) || empty($persona->id) ? null : route('nomadelfia.person.avatar', ['id' => $persona->id]);
    }
}
