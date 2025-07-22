<?php

namespace App\Http;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Prism\Prism\Prism;
use Prism\Prism\Enums\Provider;

final class EmbeddingController
{
    public function index()
    {
        $documentId = "uomo-diverso.txt";
        $filePath = Storage::disk('media_originals')->path("documents/uomo-è-diverso.txt");
        $content = File::get($filePath);

        $splitter = new RecursiveCharacterTextSplitter();
        $lines  = $splitter->splitText($content);

        dd($lines);

        $sentences = preg_split('/\R+/', $content, 0, PREG_SPLIT_NO_EMPTY); // split on \n\n, \n, \r

        $response = Prism::embeddings()
            ->using(Provider::Ollama, 'all-minilm')
            ->fromArray($sentences)
            ->asEmbeddings();

        foreach ($response->embeddings as $index => $embedding) {
            $s = $this->arrayToVectorString($embedding->embedding);
            DB::connection('db_documents')->table('embeddings')->insert([
                'doc_id' => $documentId,
                'chunk_index' => $index,
                'embedding' => DB::raw("Vec_FromText('$s')"),
                'content' => $sentences[$index]
            ]);
        }
    }

    public function arrayToVectorString(array $embedding): string
    {
        // Converts [0.1, 0.2, ...] to '[0.1,0.2,...]'
        return '[' . implode(',', $embedding) . ']';
    }

    public function query(Request $request)
    {
        $q = $request->string('q');

        $response = Prism::embeddings()
            ->using(Provider::Ollama, 'all-minilm')
            ->fromInput($q)
            ->asEmbeddings();

        $qEmb = $this->arrayToVectorString($response->embeddings[0]->embedding);

       $similar =  DB::connection('db_documents')
            ->table('embeddings')
            ->selectRaw("VEC_DISTANCE_EUCLIDEAN(embedding, Vec_FromText('".$qEmb."')) as distance, content")
            ->orderBy('distance')
            ->limit(10)
            ->get();

        return response()->json($similar);
    }
}
