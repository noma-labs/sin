
<x-mail::message>
## Aggiornamento Anagrafe {{Carbon::now()->toDateString()}}

La seguente persona è uscita da nomadelfia:
<x-mail::panel>
<p>Nome: <strong>{{ $persona->nome }} </strong></p>
<p>Cognome: <strong>{{ $persona->cognome }}</strong></p>
<p>Luogo Nascita: <strong>{{$persona->provincia_nascita}} </strong></p>
<p>Data Nascita: <strong>{{$persona->data_nascita}} </strong></p>

<p>Data Entrata: <strong>{{$data_entrata->toDateString()}}</strong></p>
<p>Data Uscita: <strong>{{$data_uscita->toDateString()}}</strong></p>
<p>Numero di Elenco: <strong>{{$persona->numero_elenco}}</strong></p>
</x-mail::panel>


<x-mail::button :url="route('nomadelfia.persone.dettaglio',['idPersona'=>$persona->id])" color="success">
Vedi persona
</x-mail::button>


Saluti, <br>
{{ config('app.name') }}
</x-mail::message>
