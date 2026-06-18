<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Http\Archive\TranscriptCode;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;

final class TranscriptsSyncCommand extends Command
{
    protected $signature = 'transcripts:sync';

    protected $description = 'Sync docx/mp3 files to recordings table by code extracted from file name or heading';

    public function handle(): int
    {
        $this->buildRecordingCode();
        $this->buildRecordingCodeFromDocx();
        $this->buildRecordingCodeFromAudio();
        $this->syncDocxFiles();
        $this->syncAudioFiles();

        return self::SUCCESS;
    }

    private function buildRecordingCode(): void
    {
        $recordings = DB::connection('archivio_nomadelfia')
            ->table('recordings')
            ->whereNotNull('DATA')
            ->selectRaw("id, DATE_FORMAT(`DATA`, '%y%m%d') as data_ymd, ORE")
            ->get();

        $updates = [];

        foreach ($recordings as $recording) {
            $dateStr = (string) $recording->data_ymd;
            $hourStr = mb_trim($recording->ORE ?? '');
            $rawCode = $dateStr.$hourStr;

            try {
                $code = TranscriptCode::fromString($rawCode);
                $updates[] = [$recording->id, $code->toString()];
            } catch (InvalidArgumentException $e) {
                $this->warn("Skipped recording {$recording->id}: {$e->getMessage()}");
            }
        }

        if (empty($updates)) {
            $this->info('Built recording code. Rows affected: 0');

            return;
        }

        // Batch update using transaction
        $count = DB::connection('archivio_nomadelfia')->transaction(function () use ($updates) {
            $updated = 0;
            foreach ($updates as [$id, $code]) {
                DB::connection('archivio_nomadelfia')
                    ->table('recordings')
                    ->where('id', $id)
                    ->update(['code' => $code]);
                $updated++;
            }

            return $updated;
        });

        $this->info("Built recording code. Rows affected: {$count}");
    }

    private function buildRecordingCodeFromDocx(): void
    {
        $transcripts = DB::connection('archivio_nomadelfia')
            ->table('recording_transcripts')
            ->whereNotNull('heading')
            ->select('id', 'heading', 'code')
            ->get();

        $updates = [];

        foreach ($transcripts as $transcript) {
            $extractedCode = Str::of($transcript->heading)->squish()->before(' ')->toString();

            if (empty($extractedCode)) {
                $this->warn("Could not extract code from heading in transcript {$transcript->id}");

                continue;
            }

            try {
                $code = TranscriptCode::fromString($extractedCode);
                $normalizedCode = $code->toString();

                if ($transcript->code !== $normalizedCode) {
                    $updates[] = [$transcript->id, $normalizedCode];
                }
            } catch (InvalidArgumentException $e) {
                $this->warn("Skipped transcript {$transcript->id}: {$e->getMessage()}");
            }
        }

        if (empty($updates)) {
            $this->info('Built recording code from docx. Rows affected: 0');

            return;
        }

        // Batch update using transaction
        $count = DB::connection('archivio_nomadelfia')->transaction(function () use ($updates) {
            $updated = 0;
            foreach ($updates as [$id, $code]) {
                DB::connection('archivio_nomadelfia')
                    ->table('recording_transcripts')
                    ->where('id', $id)
                    ->update(['code' => $code]);
                $updated++;
            }

            return $updated;
        });

        $this->info("Built recording code from docx. Rows affected: {$count}");
    }

    private function buildRecordingCodeFromAudio(): void
    {
        $audioFiles = DB::connection('archivio_nomadelfia')
            ->table('recording_audio')
            ->whereNotNull('file_name')
            ->select('id', 'file_name', 'code')
            ->get();

        $updates = [];

        foreach ($audioFiles as $audio) {
            $fileNameWithoutExt = (string) preg_replace('/\.mp3$/i', '', (string) $audio->file_name);

            if (empty($fileNameWithoutExt)) {
                $this->warn("Could not extract code from file name in audio {$audio->id}");

                continue;
            }

            try {
                $code = TranscriptCode::fromString($fileNameWithoutExt);
                $normalizedCode = $code->toString();

                if ($audio->code !== $normalizedCode) {
                    $updates[] = [$audio->id, $normalizedCode];
                }
            } catch (InvalidArgumentException $e) {
                $this->warn("Skipped audio {$audio->id}: {$e->getMessage()}");
            }
        }

        if (empty($updates)) {
            $this->info('Built recording code from audio. Rows affected: 0');

            return;
        }

        // Batch update using transaction
        $count = DB::connection('archivio_nomadelfia')->transaction(function () use ($updates) {
            $updated = 0;
            foreach ($updates as [$id, $code]) {
                DB::connection('archivio_nomadelfia')
                    ->table('recording_audio')
                    ->where('id', $id)
                    ->update(['code' => $code]);
                $updated++;
            }

            return $updated;
        });

        $this->info("Built recording code from audio. Rows affected: {$count}");
    }

    private function syncDocxFiles(): void
    {
        $linked = DB::connection('archivio_nomadelfia')->update(<<<'SQL'
            UPDATE recording_transcripts rt
            INNER JOIN recordings r ON r.code = rt.code
            SET rt.recording_id = r.id
            WHERE r.code IS NOT NULL
        SQL);

        $this->info("Linked transcripts to recordings by code match. Rows affected: {$linked}");
    }

    private function syncAudioFiles(): void
    {
        $linked = DB::connection('archivio_nomadelfia')->update(<<<'SQL'
            UPDATE recording_audio ra
            INNER JOIN recordings r ON r.code = ra.code
            SET ra.recording_id = r.id
            WHERE r.code IS NOT NULL
        SQL);

        $this->info("Linked audio files to recordings by code match. Rows affected: {$linked}");
    }
}
