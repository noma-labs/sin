<?php

namespace App\Http;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Prism\Prism\Prism;
use Prism\Prism\Enums\Provider;

final class EmbeddingController
{
    public function index()
    {
      // l'uomo è diverso
      $documentId = "uomo_diverso";
      $chunks = array(
        "Quasi tutti i giorni mi è capitato questo: ho in­ contrato un uomo, o una donna, o una fanciullo, o una fanciulla desolati, abbandonati a se stessi ed immersi in un mare di dolore: soli tra due miliardi e mezzo di fratelli.",
        "Che fare? Che cosa devo fare io?",
        "Ma che c'entro io quando due miliardi e mezzo di fratelli non sanno fare nulla per questi caduti nella desolazione? Hanno gfa fatto per altri: \"ma sono troppi\", si dice. Ed intanto si sentono moto­ ciclette, macchine, aerei che passano; la radio, la stampa annunciano grandi avvenimenti.",
        "Quel disgraziato mi guarda, mi racconta: rac­conta, piange, racconta, continua a guardarmi e pare che pretenda da me la sua soluzione; e forzatamente solo, tra due  miliardi e mezzo di fratelli.",
        "Continua a guardarmi, racconta. Pare che dica: \"Se tu fossi con me non sarei piu solo, saremmo in due\". E la mia prima reazione e sempre stata quella di rispondere a me stesso: \"Gia, e cosl non saremmo piu uno, ma due disgraziati, soli tra due miliardi e mezzo di fratelli\"",
        "E una pillola troppo amara da ingoiare. Poi ho deciso e mi sono fatto un disgraziato come loro: solo con essi, abbandonato con essi, desolato con essi, reietto con essi in mezzo a due miliardi e mezzo di fratelli."
      );

       $response =  Prism::embeddings()
            ->using(Provider::Ollama, 'all-minilm')
            ->fromArray($chunks)
            ->asEmbeddings();


        foreach ($response->embeddings as $index => $embedding) {
            $s = $this->arrayToVectorString($embedding->embedding);
            DB::connection('db_documents')->table('embeddings')->insert([
                'doc_id' => $documentId,
                'chunk_index' => $index,
                'embedding' => DB::raw("Vec_FromText('$s')"),
            ]);

        }

    }

    public function arrayToVectorString(array $embedding): string
    {
        // Converts [0.1, 0.2, ...] to '[0.1,0.2,...]'
        return '[' . implode(',', $embedding) . ']';
    }

    public function query()
    {
        // Your query logic here

        $q = "disgraziati";
        $response = Prism::embeddings()
            ->using(Provider::Ollama, 'all-minilm')
            ->fromInput($q)
            ->asEmbeddings();

        $qEmb = $this->arrayToVectorString($response->embeddings[0]->embedding);

       $similar =  DB::connection('db_documents')
            ->table('embeddings')
            ->selectRaw('chunk_index,  VEC_DISTANCE(embedding, Vec_FromText(?)) as distance', [$qEmb])
            ->orderByRaw('VEC_DISTANCE(embedding, Vec_FromText(?))', [$qEmb])
            ->limit(10)
            ->get();

        dd($similar->pluck('chunk_index'));
//             SELECT id FROM v
//   ORDER BY VEC_DISTANCE(v, x'6ca1d43e9df91b3fe580da3e1c247d3f147cf33e');
        return response()->json($response);

    }
}
