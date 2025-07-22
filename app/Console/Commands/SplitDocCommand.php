<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\File;
use App\Http\RecursiveCharacterTextSplitter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Console\Command;

class SplitDocCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'doc:split';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $content = File::get(Storage::disk('media_originals')->path("documents/uomo-diverso.txt"));

        $splitter = new RecursiveCharacterTextSplitter();
        $lines  = $splitter->splitText($content);

        dd($lines);

    }
}
