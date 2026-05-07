<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\ArchivioDocumenti\Models\AudioTranscript;
use App\DocumentChunk;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\Element\Paragraph;
use PhpOffice\PhpWord\Element\Text;
use PhpOffice\PhpWord\Element\TextBreak;
use PhpOffice\PhpWord\Element\TextRun;
use PhpOffice\PhpWord\Element\Title;
use PhpOffice\PhpWord\IOFactory;

final class SplitDocxCommand extends Command
{
    protected $signature = 'docs:docx-to-markdown
                                {file : DOCX filename from transcripts_originals disk}';

    protected $description = 'Split a DOCX file into one Markdown file per Heading 2 section';

    public function handle(): int
    {
        $file = $this->argument('file');
        $filePath = Storage::disk('transcripts_originals')->path((string) $file);

        if (! file_exists($filePath)) {
            $this->error("File not found in transcripts_originals: {$filePath}");
            return self::FAILURE;
        }

        $baseFilename = pathinfo((string) $file, PATHINFO_FILENAME);

        // Extract year from filename (first 4 characters if numeric)
        $year = null;
        if (preg_match('/^(\d{4})/', $baseFilename, $matches)) {
            $year = $matches[1];
        }

        $outputSubdir = $year ?? $baseFilename;
        Storage::disk('transcripts_previews')->makeDirectory($outputSubdir);
        $outputPath = Storage::disk('transcripts_previews')->path($outputSubdir);

        $phpWord = IOFactory::load($filePath);

        /** @var DocumentChunk[] $docs */
        $docs = [];

        foreach ($phpWord->getSections() as $section) {
            $this->info('Processing '.$filePath.' found '.count($section->getElements()).' elements...');
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
                        $this->info('DocID='.$id.', Title='.$title.', Lines='.count($contentLines));
                    }
                }
                $i++;
            }
        }

        if (empty($docs)) {
            $this->warn('No Heading 2 sections found.');
            return self::FAILURE;
        }

        $this->info(sprintf('Found %d sections. Writing Markdown files to: %s', count($docs), $outputPath));

        $bar = $this->output->createProgressBar(count($docs));
        $bar->start();

        foreach ($docs as $chunk) {
            $markdown = $this->buildMarkdown($chunk->id, $chunk->title, $chunk->description, $chunk->content);
            $mdFilename = $outputPath.DIRECTORY_SEPARATOR.$chunk->id.'.md';
            file_put_contents($mdFilename, $markdown);

            $relativePath = $outputSubdir.DIRECTORY_SEPARATOR.$chunk->id.'.md';
            AudioTranscript::updateOrCreate(
                ['code' => $chunk->id],
                [
                    'title' => $chunk->title,
                    'description' => $chunk->description,
                    'content' => implode("\n", $chunk->content),
                    'file_path' => $relativePath,
                ],
            );

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Done.');

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
     * @param  string[]  $content
     */
    private function buildMarkdown(string $id, string $title, string $description, array $content): string
    {
        $body = implode("\n\n", array_map('trim', array_filter($content)));

        return "# {$id} {$title}\n\n {$description}\n\n \n\n{$body}\n";
    }

}
