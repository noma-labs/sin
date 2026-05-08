<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\ArchivioDocumenti\Models\AudioTranscript;
use App\DocumentChunk;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\Element\Paragraph;
use PhpOffice\PhpWord\Element\Text;
use PhpOffice\PhpWord\Element\TextBreak;
use PhpOffice\PhpWord\Element\TextRun;
use PhpOffice\PhpWord\Element\Title;
use PhpOffice\PhpWord\IOFactory;

final class ImportAudioTranscriptsCommand extends Command
{
    protected $signature = 'docs:import-transcripts
                                {file? : DOCX filename from transcripts_originals disk (omit to process all)}';

    protected $description = 'Import audio transcripts from DOCX files in transcripts_originals disk into the database';

    public function handle(): int
    {
        $file = $this->argument('file');

        if ($file === null) {
            $files = Storage::disk('transcripts_originals')->files();
            $docxFiles = array_filter($files, fn (string $f) => strtolower(pathinfo($f, PATHINFO_EXTENSION)) === 'docx');

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

        $baseFilename = pathinfo($file, PATHINFO_FILENAME);

        // Extract year from filename (first 4 characters if numeric)
        $year = null;
        if (preg_match('/^(\d{4})/', $baseFilename, $matches)) {
            $year = $matches[1];
        }

        $outputSubdir = $year ?? $baseFilename;

        $phpWord = IOFactory::load($filePath);

        /** @var DocumentChunk[] $docs */
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
                        [$id, $title] = $this->parseHeading($headingText);

                        $descriptionLines = [];
                        $i++;
                        while ($i < count($elements)) {
                            $nextElement = $elements[$i];
                            if ($nextElement instanceof TextBreak) {
                                break;
                            }
                            if ($nextElement instanceof TextRun) {
                                $text = $this->decode($nextElement->getText());
                                if ($text !== '') {
                                    $descriptionLines[] = $text;
                                }
                            }
                            $i++;
                        }

                        // Collect all remaining content lines until next Titolo2 or end
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

                        $description = implode(' ', $descriptionLines);

                        $docs[] = new DocumentChunk(
                            id: $id,
                            title: $title,
                            description: $description,
                            content: $contentLines,
                        );
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
                AudioTranscript::updateOrCreate(
                    ['code' => $chunk->id],
                    [
                        'title' => $chunk->title,
                        'description' => $chunk->description,
                        'content' => implode("\n", $chunk->content),
                        'file_path' => (string) $file,
                        'recorded_date' => $this->extractRecordedAt($chunk->id),
                    ],
                );
                $successCount++;
            } catch (\Exception $e) {
                $failureCount++;
                $errorMsg = $e instanceof \Illuminate\Database\QueryException
                    ? $e->errorInfo[2] ?? 'Database error'
                    : $e->getMessage();
                $this->error("Error processing code {$chunk->id} ({$chunk->title}): {$errorMsg}");
            }
        }


        if ($failureCount > 0) {
            $this->warn($file.': '.$failureCount.' transcripts failed.');
        }
        $this->info($file.': '.$successCount.' transcripts imported successfully.');
        return self::SUCCESS;
    }

    /**
     * Parse a heading like "4912100A\t\tLA VITE E I TRALCI" into [id, title].
     *
     * @return array{0: string, 1: string}
     */
    private function decode(string $text): string
    {
        return html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    private function parseHeading(string $heading): array
    {
        $heading = trim($heading);
        if (preg_match('/^(\S+)\s+(.+)$/u', $heading, $matches)) {
            return [$matches[1], trim($matches[2])];
        }

        return [$heading, $heading];
    }

    /**
     * Extract recorded_date date from code format: YYMMDD or YYMMDD[A-Z]
     * Example: 50060211 or 50060211A → 1950-06-02
     */
    private function extractRecordedAt(string $code): ?string
    {
        // Extract first 6 digits from code
        if (!preg_match('/^(\d{2})(\d{2})(\d{2})/', $code, $matches)) {
            return null;
        }

        $year = (int) $matches[1];
        $month = (int) $matches[2];
        $day = (int) $matches[3];

        // Validate month and day
        if ($month < 1 || $month > 12 || $day < 1 || $day > 31) {
            return null;
        }

        // Convert 2-digit year to 4-digit (assume 1900-1999 for historical records)
        $fullYear = 1900 + $year;

        try {
            $date = \Carbon\Carbon::create($fullYear, $month, $day);
            return $date->toDateTimeString();
        } catch (\Exception) {
            return null;
        }
    }
}
