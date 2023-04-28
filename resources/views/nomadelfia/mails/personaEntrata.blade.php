Ciao,
una nuova persona è entrata nella comunità.

<p>Nome: <strong>{{ $persona->nome }} </strong></p>
<p>Cognome:< strong>{{ $persona->cognome }}</strong></p>
<p>Data Entrata: <strong>{{$data_entrata}}</strong></p>
<p>Data Nascita: <strong>{{$persona->data_nascita}} </strong></p>
<p>Luogo Nascita: <strong>{{$persona->provincia_nascita}} </strong></p>
<p>Gruppo familiare:<strong>{{ $gruppo->nome}}</strong></p>
<p>Numero Elenco : <strong>{{$persona->numero_elenco}} </strong></p>
<p>Famiglia: <strong>{{($famiglia) ? $famiglia->nome_famiglia: ""}} </strong></p>


Saluti,
Staff