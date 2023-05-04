
<x-mail::message>
Ciao,
una nuova persona è uscita da Nomadelfia.

<x-mail::table>
| Persona       | Luogo Nascita         |  Nascita  |  Entrata  | Uscita  | N°Elenco  |
| ------------- |:-------------:| --------:|:-------------:|:-------------:|:-------------:|
| {{ $persona->nome }} {{ $persona->cognome }}     | {{$persona->provincia_nascita}}       | {{$persona->data_nascita}}      |{{$data_entrata->toDateString()}}      | {{$data_uscita->toDateString()}}      |{{$persona->numero_elenco}}   |
</x-mail::table>

Saluti, <br>
{{ config('app.name') }}
</x-mail::message>
