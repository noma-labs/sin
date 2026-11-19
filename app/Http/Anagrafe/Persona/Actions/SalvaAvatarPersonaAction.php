<?php

declare(strict_types=1);

namespace App\Anagrafe\Persona\Actions;

use App\Anagrafe\Persona\Models\Persona;
use GdImage;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

/**
 * Saves a photo as the avatar of a persona: a square JPEG of the face on the media_previews disk, at persone/{id}.jpg.
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
     * @return string the path of the avatar on the media_previews disk
     *
     * @throws RuntimeException when the photo can not be read as an image
     */
    public function execute(Persona $persona, string $percorsoFoto): string
    {
        $contenuto = @file_get_contents($percorsoFoto);
        $foto = $contenuto === false ? false : @imagecreatefromstring($contenuto);
        if (! $foto instanceof GdImage) {
            throw new RuntimeException("La foto {$percorsoFoto} non è un'immagine valida");
        }

        $avatar = $this->ritaglia($this->raddrizza($foto, $percorsoFoto));

        ob_start();
        imagejpeg($avatar, null, self::QUALITA_JPEG);
        $jpeg = (string) ob_get_clean();

        $percorsoAvatar = "persone/{$persona->id}.jpg";
        Storage::disk('media_previews')->put($percorsoAvatar, $jpeg);

        $persona->avatar_path = $percorsoAvatar;
        $persona->save();

        return $percorsoAvatar;
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
     * Crops the largest square and scales it to LATO. On a portrait the square sits at a quarter of the extra height
     * from the top instead of the centre, so the head is not cut; on a landscape it is centred.
     */
    private function ritaglia(GdImage $foto): GdImage
    {
        $larghezza = imagesx($foto);
        $altezza = imagesy($foto);
        $lato = min($larghezza, $altezza);
        $x = intdiv($larghezza - $lato, 2);
        $y = intdiv($altezza - $lato, 4);
        $latoAvatar = min(self::LATO, $lato);

        $avatar = imagecreatetruecolor($latoAvatar, $latoAvatar);
        imagecopyresampled($avatar, $foto, 0, 0, $x, $y, $latoAvatar, $latoAvatar, $lato, $lato);

        return $avatar;
    }
}
