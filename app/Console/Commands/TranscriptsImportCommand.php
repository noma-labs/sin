<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Archive\Models\RecordingTranscript;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\Element\TextRun;
use PhpOffice\PhpWord\IOFactory;

final class TranscriptsImportCommand extends Command
{
    protected $signature = 'transcripts:import-docx
                                {file? : DOCX filename from transcripts_originals disk (omit to process all)}';

    protected $description = 'Import audio transcripts from DOCX files in transcripts_originals disk into the database';

    public function handle(): int
    {
        DB::connection('archivio_nomadelfia')->table('recording_transcripts')->truncate();

        $file = $this->argument('file');

        if ($file === null) {
            $files = Storage::disk('transcripts_originals')->files();
            $docxFiles = array_filter($files, fn (string $f) => mb_strtolower(pathinfo($f, PATHINFO_EXTENSION)) === 'docx');

            if (empty($docxFiles)) {
                $this->warn('No DOCX files found in transcripts_originals.');

                return self::FAILURE;
            }

            $exitCode = self::SUCCESS;
            foreach ($docxFiles as $docxFile) {
                if ($this->processFile($docxFile) === self::FAILURE) {
                    $exitCode = self::FAILURE;
                }
            }

            return $exitCode;
        }

        return $this->processFile((string) $file);
    }

    private function processFile(string $file): int
    {
        $filePath = Storage::disk('transcripts_originals')->path($file);

        if (! file_exists($filePath)) {
            $this->error("File not found in transcripts_originals: {$filePath}");

            return self::FAILURE;
        }

        $yearFromFile = $this->extractYearFromFilename($file);

        $phpWord = IOFactory::load($filePath);

        /** @var array[] $docs */
        $docs = [];

        foreach ($phpWord->getSections() as $section) {
            $elements = $section->getElements();
            $i = 0;
            while ($i < count($elements)) {
                $element = $elements[$i];
                if ($element instanceof TextRun) {
                    $par = $element->getParagraphStyle();
                    $styleName = $par->getStyleName();
                    if ($styleName === 'Titolo2') {
                        $headingText = preg_replace('/\s+/', ' ', mb_trim($this->decode($element->getText())));

                        // Collect all content lines until next Titolo2 or end
                        $contentLines = [];
                        $i++;
                        while ($i < count($elements)) {
                            $nextElement = $elements[$i];
                            // Stop if we hit another Titolo2 heading
                            if ($nextElement instanceof TextRun) {
                                $nextPar = $nextElement->getParagraphStyle();
                                if ($nextPar->getStyleName() === 'Titolo2') {
                                    $i--; // Back up so the outer loop processes this Titolo2
                                    break;
                                }
                                $text = $this->decode($nextElement->getText());
                                if ($text !== '') {
                                    $contentLines[] = $text;
                                }
                            }
                            $i++;
                        }

                        $docs[] = [
                            'heading' => $headingText,
                            'content' => $contentLines,
                            'code' => $this->buildCode($headingText, $yearFromFile),
                        ];
                    }
                }
                $i++;
            }
        }

        if (empty($docs)) {
            $this->warn($file.': no transcripts found.');

            return self::FAILURE;
        }

        $successCount = 0;
        $failureCount = 0;

        foreach ($docs as $chunk) {
            try {
                RecordingTranscript::insert(
                    [
                        'code' => $chunk['code'],
                        'heading' => $chunk['heading'] ?? null,
                        'content' => implode("\n", $chunk['content']),
                        'file_path' => (string) $file,
                    ]
                );
                $successCount++;
            } catch (Exception $e) {
                $failureCount++;
                $errorMsg = $e instanceof \Illuminate\Database\QueryException
                    ? $e->errorInfo[2] ?? 'Database error'
                    : $e->getMessage();
                $this->error("Error processing heading {$chunk['heading']}: {$errorMsg}");
            }
        }

        if ($failureCount > 0) {
            $this->warn($file.': '.$failureCount.' transcripts failed.');
        }
        $this->info($file.': '.$successCount.' transcripts imported successfully.');

        return self::SUCCESS;
    }

    private function decode(string $text): string
    {
        return html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /**
     * Extract year from filename (e.g., '1949registrazioni.docx' → '1949')
     */
    private function extractYearFromFilename(string $filename): string
    {
        if (preg_match('/(\d{4})/', $filename, $matches)) {
            return $matches[1];
        }

        return '0000';
    }

    /**
     * Build code from heading and year: YYYYMMDDHH[A|B|C]
     * If first word starts with a number, use entire word as code (skip YY prefix, prepend YYYY)
     * Otherwise default to YYYY010100
     */
    private function buildCode(string $heading, string $year): string
    {
        // Get first word from heading
        $words = explode(' ', mb_trim($heading));
        $firstWord = $words[0] ?? '';

        // If first word starts with a number, use it as the code
        if ($firstWord && is_numeric($firstWord[0])) {
            // Extract from position 2 onwards (skip YY, keep MMDD[HH][A|B|C]...)
            $code = mb_substr($firstWord, 2);

            // If the remaining part is exactly 4 digits (MMDD only, no hour/letter), append '00'
            if (preg_match('/^\d{4}$/', $code)) {
                $code .= '00';
            }

            return mb_substr($year.$code, 0, 11);
        }

        // Default if no numeric code found
        return $year.'010100';
    }
}
