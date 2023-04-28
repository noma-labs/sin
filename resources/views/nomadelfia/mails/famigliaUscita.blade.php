Ciao,
la famiglia {{$famiglia->nome_famiglia}} è uscita da Nomadelfia.

I seguenti componenti della famiglia sono usciti:

@foreach ($componenti as $persona)
<p>Nome: <strong>{{ $persona->nome }} </strong></p>
<p>Cognome: <strong>{{ $persona->cognome }}</strong></p>
<p>Luogo Nascita: <strong>{{$persona->provincia_nascita}} </strong></p>
<p>Data Nascita: <strong>{{$persona->data_nascita}} </strong></p>

<p>Data Entrata: <strong>{{$persona->getDataEntrataNomadelfia() ?: ''}}</strong></p>
<p>Data Uscita: <strong>{{$data_uscita}}</strong></p>
<p>Numero di Elenco: <strong>{{$persona->numero_elenco}}</strong></p>
<br>
@endforeach


Saluti,
Staff