<?php

declare(strict_types=1);

namespace App\Http\Archive;

use InvalidArgumentException;
use Stringable;

final readonly class TranscriptCode implements Stringable
{
    public int $year;

    public int $month;

    public int $day;

    public string $hour;

    public function __construct(
        int $year,
        int $month,
        int $day,
        ?string $hour,
    ) {
        if ($year < 1949 || $year > 2040) {
            // for me of the future, if you are reading this in 2040 or later, please update the year validation to allow for years beyond 2040.
            // we are not able to distinguish from 49 to be 1949 or 2049, so we are limiting the range to 1949-2040 for now.
            throw new InvalidArgumentException("Invalid year segment: '{$year}'. Expected a value between 1949 and 2040.");
        }

        if ($month < 1 || $month > 12) {
            throw new InvalidArgumentException("Invalid month segment: '{$month}'. Expected a value between 1 and 12.");
        }

        if ($day < 1 || $day > 31) {
            throw new InvalidArgumentException("Invalid day segment: '{$day}'. Expected a value between 1 and 31.");
        }

        $this->year = $year;
        $this->month = $month;
        $this->day = $day;

        $normalizedHour = $hour;

        if ($normalizedHour === null || $normalizedHour === '' || $normalizedHour === '??') {
            $normalizedHour = '0A';
        }

        if (preg_match('/^[A-Za-z]$/', $normalizedHour) === 1) {
            $normalizedHour = '0'.mb_strtoupper($normalizedHour);
        }

        if (preg_match('/^00([A-Za-z])$/', $normalizedHour, $matches) === 1) {
            $normalizedHour = '0'.mb_strtoupper($matches[1]);
        }

        if (! preg_match('/^(?:\d{1,2}[A-Za-z]?)$/', $normalizedHour)) {
            throw new InvalidArgumentException("Invalid hour segment: '{$normalizedHour}'. Expected empty string or 1-2 digits with optional Letter suffix.");
        }

        $this->hour = $normalizedHour;
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    public static function fromString(string $code): self
    {
        $trimmedCode = mb_trim($code);

        if (mb_strlen($trimmedCode) < 6) {
            throw new InvalidArgumentException("Cannot parse TranscriptCode from '{$trimmedCode}'.");
        }

        $twoDigitYear = mb_substr($trimmedCode, 0, 2);
        $firstDigit = $twoDigitYear[0];
        $fourDigitYear = (int) (in_array($firstDigit, ['0', '1', '2', '3'], true) ? '20'.$twoDigitYear : '19'.$twoDigitYear);

        return new self(
            year: $fourDigitYear,
            month: (int) mb_substr($trimmedCode, 2, 2),
            day: (int) mb_substr($trimmedCode, 4, 2),
            hour: mb_substr($trimmedCode, 6),
        );
    }

    public function toString(): string
    {
        $lastTwoDigitsOfYear = $this->year % 100;

        return mb_str_pad((string) $lastTwoDigitsOfYear, 2, '0', STR_PAD_LEFT)
            .mb_str_pad((string) $this->month, 2, '0', STR_PAD_LEFT)
            .mb_str_pad((string) $this->day, 2, '0', STR_PAD_LEFT)
            .$this->hour;
    }
}
