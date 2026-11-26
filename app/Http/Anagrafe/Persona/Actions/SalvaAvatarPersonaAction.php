<?php

declare(strict_types=1);

namespace App\Anagrafe\Persona\Actions;

use App\Anagrafe\Persona\Models\Persona;
use App\Photo\DataTransferObjects\RiquadroVolto;
use GdImage;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

/**
 * Saves a photo as the avatar of a persona: a square JPEG of the face on the media_previews disk, at persone/{id}.jpg,
 * with the time it was saved (avatar_aggiornato_il).
 */
final class SalvaAvatarPersonaAction
{
    /**
     * The side of the avatar in pixels; a smaller photo is not upscaled and keeps its short side.
     */
    public const LATO = 256;

    public const QUALITA_JPEG = 85;

    /**
     * @param  string  $percorsoFoto  the local path of the original photo
     * @param  RiquadroVolto|null  $riquadro  the square to cut (around a face or drawn on the photo), null to cut the largest square
     * @return string the path of the avatar on the media_previews disk
     *
     * @throws RuntimeException when the photo can not be read as an image
     */
    public function execute(Persona $persona, string $percorsoFoto, ?RiquadroVolto $riquadro = null): string
    {
        $jpeg = $this->jpeg($percorsoFoto, self::LATO, $riquadro);

        $percorsoAvatar = "persone/{$persona->id}.jpg";
        Storage::disk('media_previews')->put($percorsoAvatar, $jpeg);

        $persona->avatar_path = $percorsoAvatar;
        $persona->avatar_aggiornato_il = Date::now();
        $persona->save();

        return $percorsoAvatar;
    }

    /**
     * The photo rotated by its EXIF orientation and cut in a square, as a JPEG at most $latoMassimo wide: the square of
     * $riquadro, or the largest square of the photo.
     *
     * @throws RuntimeException when the photo can not be read as an image
     */
    public function jpeg(string $percorsoFoto, int $latoMassimo, ?RiquadroVolto $riquadro = null): string
    {
        // a photo of a camera or a phone (12 MP and more) takes more than 100 MB once decoded, twice when rotated
        ini_set('memory_limit', '1024M');

        $contenuto = @file_get_contents($percorsoFoto);
        $foto = $contenuto === false ? false : @imagecreatefromstring($contenuto);
        if (! $foto instanceof GdImage) {
            throw new RuntimeException("La foto {$percorsoFoto} non è un'immagine valida");
        }

        $avatar = $this->ritaglia($this->raddrizza($foto, $percorsoFoto), $latoMassimo, $riquadro);

        ob_start();
        imagejpeg($avatar, null, self::QUALITA_JPEG);

        return (string) ob_get_clean();
    }

    /**
     * Rotates the photo as the camera recorded it in the EXIF Orientation; left as it is without the exif extension.
     */
    private function raddrizza(GdImage $foto, string $percorsoFoto): GdImage
    {
        if (! function_exists('exif_read_data')) {
            return $foto;
        }

        $exif = @exif_read_data($percorsoFoto);
        $angolo = match (is_array($exif) ? ($exif['Orientation'] ?? 1) : 1) {
            3 => 180,
            6 => -90,
            8 => 90,
            default => 0,
        };
        if ($angolo === 0) {
            return $foto;
        }

        $ruotata = imagerotate($foto, $angolo, 0);

        return $ruotata instanceof GdImage ? $ruotata : $foto;
    }

    /**
     * Crops a square and scales it to $latoMassimo: the square of $riquadro, otherwise the largest square. On a portrait
     * the largest square sits at a quarter of the extra height from the top instead of the centre, so the head is not
     * cut; on a landscape it is centred.
     */
    private function ritaglia(GdImage $foto, int $latoMassimo, ?RiquadroVolto $riquadro): GdImage
    {
        $larghezza = imagesx($foto);
        $altezza = imagesy($foto);
        if (! $riquadro instanceof RiquadroVolto) {
            $lato = min($larghezza, $altezza);
            $x = intdiv($larghezza - $lato, 2);
            $y = intdiv($altezza - $lato, 4);
        } else {
            ['x' => $x, 'y' => $y, 'lato' => $lato] = $riquadro->inPixel($larghezza, $altezza);
        }
        $latoAvatar = max(1, min($latoMassimo, $lato));

        $avatar = imagecreatetruecolor($latoAvatar, $latoAvatar);
        imagecopyresampled($avatar, $foto, 0, 0, $x, $y, $latoAvatar, $latoAvatar, $lato, $lato);

        return $avatar;
    }
}
