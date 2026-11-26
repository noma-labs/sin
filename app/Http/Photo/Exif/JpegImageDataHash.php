<?php

declare(strict_types=1);

namespace App\Photo\Exif;

use HashContext;
use RuntimeException;

/**
 * The MD5 that exiftool returns as File:ImageDataHash for a JPEG, computed in PHP.
 *
 * It reproduces the marker loop of Image::ExifTool::ProcessJPEG() (exiftool 13.x) byte for
 * byte, because the hash is the photos.sha dedup key of the photos already imported:
 * - only the image data is hashed, from the first start of scan (FF DA) to the end of image
 *   (FF D9), and none of the metadata segments nor the trailer after FF D9;
 * - the data after a FF DA, FF 00 or restart (FF D0-D7) marker is hashed together with the
 *   marker, unless the next marker is a segment with a length (like the FF C4 between the
 *   scans of a progressive JPEG): exiftool then skips that last piece of data;
 * - at the end of the file without FF D9, the last piece of data is not hashed.
 *
 * mb_strlen() and mb_substr() use the '8bit' encoding, so they count the bytes of the JPEG.
 */
final class JpegImageDataHash
{
    /** Markers without a length word (exiftool %markerLenBytes with 0). */
    private const MARKERS_WITHOUT_LENGTH = [
        0x00, 0x01, 0xD0, 0xD1, 0xD2, 0xD3, 0xD4, 0xD5, 0xD6, 0xD7, 0xD8, 0xD9, 0xDA,
        0x30, 0x31, 0x32, 0x33, 0x34, 0x35, 0x36, 0x37, 0x38, 0x39, 0x3A, 0x3B, 0x3C, 0x3D, 0x3E, 0x3F,
        0x4F, 0x92, 0x93,
    ];

    /** J2C extension markers with a 4-byte length word. */
    private const MARKERS_WITH_LONG_LENGTH = [0x74, 0x75, 0x77];

    /**
     * Inside the image data, the first 0xFF run followed by a marker that ends the hashed
     * bytes: anything but a stuffed byte (00), a restart (D0-D7) or a start of scan (DA).
     */
    private const END_OF_HASHED_DATA = '/\xFF+[^\x00\xD0-\xD7\xDA\xFF]/';

    private const END_OF_IMAGE = 0xD9;

    private const START_OF_DATA = 0x93;

    private readonly HashContext $context;

    private bool $hashedAnyByte = false;

    private function __construct(private readonly string $jpeg)
    {
        $this->context = hash_init('md5');
    }

    /**
     * The hash, or null when exiftool returns none: the file isn't a JPEG or has no image data.
     */
    public static function ofFile(string $path): ?string
    {
        $jpeg = @file_get_contents($path);

        return $jpeg === false ? null : (new self($jpeg))->hash();
    }

    private function hash(): ?string
    {
        // exiftool recognises a JPEG by FF D8 FF and reads the markers after FF D8
        if (! str_starts_with($this->jpeg, "\xFF\xD8\xFF")) {
            return null;
        }

        $length = mb_strlen($this->jpeg, '8bit');
        $position = 2;
        $nextMarker = null;

        while (true) {
            $marker = $nextMarker;
            $nextMarker = null;

            if ($marker === self::END_OF_IMAGE || $marker === self::START_OF_DATA) {
                break;
            }

            if ($this->isImageData($marker)) {
                $end = $this->hashImageData($position);
                if ($end === null) {
                    break;
                }
                [$position, $nextMarker] = $end;
                if (in_array($nextMarker, self::MARKERS_WITHOUT_LENGTH, true)) {
                    continue;
                }
            } else {
                $markerByte = $position + strcspn($this->jpeg, "\xFF", $position);
                if ($markerByte >= $length) {
                    break;
                }
                $position = $markerByte + strspn($this->jpeg, "\xFF", $markerByte);
                if ($position >= $length) {
                    break;
                }
                $nextMarker = ord($this->jpeg[$position++]);
                if (in_array($nextMarker, self::MARKERS_WITHOUT_LENGTH, true)) {
                    continue;
                }
            }

            $position = $this->skipSegment($position, $nextMarker);
            if ($position === null) {
                break;
            }
        }

        return $this->hashedAnyByte ? hash_final($this->context) : null;
    }

    private function isImageData(?int $marker): bool
    {
        return $marker === 0x00 || $marker === 0xDA || ($marker >= 0xD0 && $marker <= 0xD7);
    }

    /**
     * Hash the image data that starts with the marker just before $position, and return the
     * position after the marker that ends it with that marker, or null at the end of the file.
     *
     * @return array{int, int}|null
     */
    private function hashImageData(int $position): ?array
    {
        $start = $position - 2;

        $found = preg_match(self::END_OF_HASHED_DATA, $this->jpeg, $match, PREG_OFFSET_CAPTURE, $position);
        if ($found === false) {
            throw new RuntimeException('Cannot search the JPEG image data: '.preg_last_error_msg());
        }
        if ($found === 0) {
            // at the end of the file, the data after the last marker is not hashed
            $end = mb_strlen($this->jpeg, '8bit');
            while ($end > $position && $this->jpeg[$end - 1] === "\xFF") {
                $end--;
            }
            $this->hashRange($start, $this->lastMarkerBefore($end, $start, $position));

            return null;
        }

        $run = $match[0][1];
        $lastFillByte = $run + mb_strlen($match[0][0], '8bit') - 2;
        $nextMarker = ord($this->jpeg[$lastFillByte + 1]);

        // before a segment with a length, the data after the last marker is not hashed
        $this->hashRange($start, in_array($nextMarker, self::MARKERS_WITHOUT_LENGTH, true)
            ? $lastFillByte
            : $this->lastMarkerBefore($run, $start, $position));

        return [$lastFillByte + 2, $nextMarker];
    }

    /**
     * The 0xFF of the last marker between $position and $end, otherwise $start.
     */
    private function lastMarkerBefore(int $end, int $start, int $position): int
    {
        for ($byte = $end - 1; $byte >= $position; $byte--) {
            if ($this->jpeg[$byte] === "\xFF") {
                return $byte;
            }
        }

        return $start;
    }

    private function hashRange(int $from, int $to): void
    {
        if ($to > $from) {
            hash_update($this->context, mb_substr($this->jpeg, $from, $to - $from, '8bit'));
            $this->hashedAnyByte = true;
        }
    }

    /**
     * The position after the segment of $marker that starts at $position, or null when the
     * file ends before (exiftool stops reading there).
     */
    private function skipSegment(int $position, int $marker): ?int
    {
        $lengthBytes = in_array($marker, self::MARKERS_WITH_LONG_LENGTH, true) ? 4 : 2;
        if ($position + $lengthBytes > mb_strlen($this->jpeg, '8bit')) {
            return null;
        }
        $segmentLength = (int) (unpack($lengthBytes === 4 ? 'N' : 'n', $this->jpeg, $position) ?: [1 => 0])[1];
        if ($segmentLength < $lengthBytes) {
            return null;
        }
        $end = $position + $segmentLength;

        return $end <= mb_strlen($this->jpeg, '8bit') ? $end : null;
    }
}
