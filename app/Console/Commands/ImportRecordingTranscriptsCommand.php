<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Archive\Models\RecordingTranscript;
use Exception;
use Illuminate\Console\Command;
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
                        $headingText = $this->decode($element->getText());
                        [$code, $title] = $this->parseHeading($headingText);

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
                            'code' => $code,
                            'heading' => $headingText,
                            'content' => $contentLines,
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
                RecordingTranscript::updateOrCreate(
                    ['code' => $chunk['code']],
                    [
                        'heading' => $chunk['heading'] ?? null,
                        'content' => implode("\n", $chunk['content']),
                        'file_path' => (string) $file,
                    ],
                );
                $successCount++;
            } catch (Exception $e) {
                $failureCount++;
                $errorMsg = $e instanceof \Illuminate\Database\QueryException
                    ? $e->errorInfo[2] ?? 'Database error'
                    : $e->getMessage();
                $this->error("Error processing code {$chunk['code']}: {$errorMsg}");
            }
        }

        if ($failureCount > 0) {
            $this->warn($file.': '.$failureCount.' transcripts failed.');
        }
        $this->info($file.': '.$successCount.' transcripts imported successfully.');

        return self::SUCCESS;
    }

    /**
     * Decode HTML entities in a string.
     */
    private function decode(string $text): string
    {
        return html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    private function parseHeading(string $heading): array
    {
        $heading = mb_trim($heading);
        if (preg_match('/^(\S+)\s+(.+)$/u', $heading, $matches)) {
            return [$matches[1], mb_trim($matches[2])];
        }

        return [$heading, $heading];
    }
}
