<?php

namespace App\Http;

final class RecursiveCharacterTextSplitter
{

    public function __construct(
        private int $chunkSize = 10,
        private array $separators = ["\n\n", "\n", " ", ""]
    )
    {
        // Constructor can be used for initialization if needed
    }

    public function splitText(string $text): array
    {
        return $this->split($text, $this->separators);
    }

    private function split(string $text, array $separators = ["\n\n\n","\n\n", "\n", " ", ""]) : array
    {
        $chunks = [];

        $separator = $separators[array_key_last($separators)];
        foreach ($separators as $i => $s) {
            if  ($s === "" || str_contains($text, $s)) {
                $separator = $s;
                unset($separators[$i]);
                break;
            }
        }

        // TODO -1 or 0 ??
        $splits = preg_split("/$separator/", $text, -1, PREG_SPLIT_NO_EMPTY);

        dd($splits);
        $goodSplits = [];

        // dump($text);
        foreach ($splits as $split) {
            if (strlen($split) < $this->chunkSize) {
                $goodSplits[] = $split;
                // dump ("Good split: $split, length: " . strlen($split));
                continue;
            }

            if (count($goodSplits) > 0) {
            //    dump("Found good splits: " . implode(", ", $goodSplits));
               $mergedText = $this->mergeSplits($goodSplits, $separator);
            //    dump("Merged text: $mergedText");
            }
            // Split further
            $newSplits = $this->split($split, $separators);
            $chunks = array_merge($chunks, $newSplits);
        }

        if (count($goodSplits) > 0) {
            // If we have good splits, return them
            $chunks = array_merge($goodSplits, $chunks);
            return $chunks;
        }
        // return array_merge($goodSplits, $chunks);

        return $chunks; // Return the chunks
    }

    public function mergeSplits(array $splits, string $separator): array
    {

        $separatorLen = strlen($separator);

        $documents = []; // when a new document is created, it is added to this list
        $currentDocument = []; // Initialize an empty array to hold the current document (a list of text lines )
        $total = 0;

        foreach ($splits as $split) {
            // dump("Processing split: $split, length: " . strlen($split));
            $lenSplit = strlen($split);
            if ($total + $lenSplit > $this->chunkSize) {
                if ($total > $this->chunkSize) {
                    // dump("Created a chunk of size $total, larger than {$this->chunkSize}.");
                }
                if (count($currentDocument) > 0) {
                    $doc = $this->joinDoc($currentDocument, $separator);
                    if ($doc !== null) {
                        $documents[] = $doc; // Add the current document to the list
                    }
                    //TODO: manage the chunk overlap ?
                }

            }
            // dump("Adding document: $split, to current document");
            // dump("Current document: $currentDocument");
            $currentDocument[] = $split;

            $total += $lenSplit + $separatorLen; // Update the total length, adding the separator length
            // dump("Current document size: $total, chunk size: {$this->chunkSize}");
        }

        $doc = $this->joinDoc($currentDocument, $separator); // Join the last document if it exists
        if ($doc !== null) {
            $documents[] = $doc; // Add the current document to the list
        }
        return $documents; // Return the list of documents
    }

    public function joinDoc(array $lines, string $separator): ?string
    {
        // Joins the lines of the documents with the specified separator
        $text = implode($separator, $lines);
        if (strlen($text) > 0) {
            return $text;
        }
        return null;
    }
}
