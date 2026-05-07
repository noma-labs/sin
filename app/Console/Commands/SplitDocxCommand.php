<?php

declare(strict_types=1);

namespace App\Console\Commands;

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
    protected $signature = 'docs:split
                                {file : Path to the DOCX file inside storage/app}
                                {--output= : Output directory inside storage/app (default: split/{filename-without-ext})}';

    protected $description = 'Split a DOCX file into one Markdown file per Heading 2 section';

    public function handle(): int
    {
        $file = $this->argument('file');
        $filePath = Storage::disk('local')->path((string) $file);

        if (! file_exists($filePath)) {
            $this->error("File not found: {$filePath}");
            return self::FAILURE;
        }

        $outputDir = $this->option('output')
            ?? 'split/'.pathinfo((string) $file, PATHINFO_FILENAME);

        Storage::disk('local')->makeDirectory($outputDir);
        $outputPath = Storage::disk('local')->path($outputDir);

        $phpWord = IOFactory::load($filePath);

        $chunks = [];
        $currentChunk = null;

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
                        // $this->info('>> Found Titolo2: '.$element->getText());
                        $headingText = $element->getText();
                        [$id, $title] = $this->parseHeading($headingText);

                        // Collect text until TextBreak to get the dscription of the section
                        $descriptionLines = [];
                        $i++;
                        while ($i < count($elements)) {
                            $nextElement = $elements[$i];
                            if ($nextElement instanceof TextBreak) {
                                break;
                            }
                            if ($nextElement instanceof TextRun) {
                                $text = $nextElement->getText();
                                if ($text !== '') {
                                    $descriptionLines[] = $text;
                                    // $this->info('   Collected: '.$text);
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
                                $text = $nextElement->getText();
                                if ($text !== '') {
                                    $contentLines[] = $text;
                                }
                            }
                            $i++;
                        }

                        $chunks[] = [
                            'id' => $id,
                            'title' => $title,
                            'description' => $descriptionLines,
                            'lines' => $contentLines,
                        ];
                        $this->info('DocID='.$id.', Title='.$title.', Description='.implode(', ', $descriptionLines).', Content lines='.count($contentLines));
                       if ($this->confirm('Do you wish to continue?')) {  }
                    }
                }
                $i++;
            }
        }

        if (empty($chunks)) {
            $this->warn('No Heading 2 sections found.');
            return self::FAILURE;
        }

        $this->info(sprintf('Found %d sections. Writing Markdown files to: %s', count($chunks), $outputPath));

        $bar = $this->output->createProgressBar(count($chunks));
        $bar->start();

        foreach ($chunks as $chunk) {
            // Combine description and content lines
            $allLines = array_merge($chunk['description'], $chunk['lines']);
            $markdown = $this->buildMarkdown($chunk['title'], $allLines);
            $filename = $outputPath.DIRECTORY_SEPARATOR.$chunk['id'].'.md';
            file_put_contents($filename, $markdown);
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
    private function parseHeading(string $heading): array
    {
        $heading = trim($heading);
        if (preg_match('/^(\S+)\s+(.+)$/u', $heading, $matches)) {
            return [$matches[1], trim($matches[2])];
        }

        return [$heading, $heading];
    }

    /**
     * @param  string[]  $description
     */
    private function buildMarkdown(string $title, array $description): string
    {
        $body = implode("\n\n", array_map('trim', array_filter($description)));

        return "# {$title}\n\n{$body}\n";
    }

}
