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
                        $headingText = preg_replace('/\s+/', ' ', trim($this->decode($element->getText())));

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
     * Case 1: heading starts with YYMMDD[HH][LETTER] → YYYYMMDDHH[LETTER]
     * Case 2: heading has no code → YYYY010100
     * Case 3: heading has YYMMDD but no HH → YYYYMMDD00[LETTER]
     */
    private function buildCode(string $heading, string $year): string
    {
        // Split heading and get first word
        $words = explode(' ', trim($heading));
        $firstWord = $words[0] ?? '';

        // Try to extract 6+ digit code from first word with optional HH and optional letter
        if (preg_match('/^(\d{6})(\d{0,2})([A-Z])?/', $firstWord, $matches)) {
            $yymmdd = $matches[1];
            $hh = str_pad($matches[2] ?? '', 2, '0', STR_PAD_RIGHT);
            $letter = $matches[3] ?? '';

            $code = $year . substr($yymmdd, 2) . $hh . $letter;

            return substr($code, 0, 11);
        }

        // No code in heading, use default
        return $year . '010100';
    }

}
