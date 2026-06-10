<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

final class ArchiveTranscriptSqlSeeder extends Seeder
{
    public function run(): void
    {
        $connection = DB::connection('archivio_nomadelfia');

        $year = random_int(1950, 1980);
        $month = random_int(1, 12);
        $day = random_int(1, 28);
        $date = sprintf('%04d-%02d-%02d', $year, $month, $day);

        $suffix = chr(random_int(65, 90));
        $code = sprintf('%04d%02d%02d0%s', $year, $month, $day, $suffix);
        $yymmdd = sprintf('%02d%02d%02d', $year % 100, $month, $day);
        $audioFileName = sprintf('%s00%s.mp3', $yymmdd, $suffix);
        $audioFilePath = sprintf('%d/%s', $year, $audioFileName);

        $audioDisk = Storage::disk('audio_originals');
        $absoluteAudioPath = $audioDisk->path($audioFilePath);
        $absoluteAudioDir = dirname($absoluteAudioPath);
        $fixturePath = base_path('database/seeders/fixtures/fra_martino.mp3');

        if (! is_dir($absoluteAudioDir) && ! @mkdir($absoluteAudioDir, 0777, true) && ! is_dir($absoluteAudioDir)) {
            throw new \RuntimeException(sprintf('Unable to create audio directory: %s', $absoluteAudioDir));
        }

        if (! is_file($fixturePath)) {
            throw new \RuntimeException(sprintf('Missing seed audio fixture: %s', $fixturePath));
        }

        $audioContents = file_get_contents($fixturePath);

        if ($audioContents === false) {
            throw new \RuntimeException(sprintf('Unable to read seed audio fixture: %s', $fixturePath));
        }

        $audioDisk->put($audioFilePath, $audioContents);

        $audioFileSizeBytes = (int) $audioDisk->size($audioFilePath);

        $connection->insert(
            'INSERT INTO recordings (`code`, `DATA`, `ORE`, `AUTORE`, `ARGOMENTO`, `LOCALITA`, `GENERE`) VALUES (?, ?, ?, ?, ?, ?, ?)',
            [
                $code,
                $date,
                '0'.$suffix,
                'Saltini Don Zeno',
                'Seeder fake argomento',
                'Nomadelfia',
                'Discorso',
            ]
        );

        $recordingIdResult = $connection->select('SELECT LAST_INSERT_ID() AS id');
        $recordingId = (int) ($recordingIdResult[0]->id ?? 0);

        if ($recordingId <= 0) {
            throw new \RuntimeException('Unable to read LAST_INSERT_ID for recordings insert.');
        }

        $connection->insert(
            'INSERT INTO recording_transcripts (`code`, `recording_id`, `heading`, `file_path`, `content`, `created_at`, `updated_at`) VALUES (?, ?, ?, ?, ?, NOW(), NOW())',
            [
                $code,
                $recordingId,
                'Seeder fake heading',
                sprintf('fake/%s.docx', $code),
                sprintf('Seeder fake content for recording %s.', $code),
            ]
        );

        $connection->insert(
            'INSERT INTO recording_audio (`recording_id`, `code`, `file_name`, `file_path`, `file_size_bytes`, `created_at`, `updated_at`) VALUES (?, ?, ?, ?, ?, NOW(), NOW())',
            [
                $recordingId,
                $code,
                $audioFileName,
                $audioFilePath,
                $audioFileSizeBytes,
            ]
        );
    }
}
