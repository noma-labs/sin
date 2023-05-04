Ciao,
una nuova persona è entrata nella comunità.

<span class="badge badge-primary badge-pill"> akdsjf</span>
<table>
    <thead>
    <tr>
        <th>Persona</th>
        <th>Luogo Nascita</th>
        <th>Nascita<br></th>
        <th>Entrata</th>
        <th>Gruppo</th>
        <th>Famiglia</th>
        <th>N Cartella</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td>{{ $persona->nome }} {{ $persona->cognome }}</td>
        <td>{{$persona->provincia_nascita}}</td>
        <td>{{$persona->data_nascita}}</td>
        <td>{{$data_entrata}}</td>
        <td>{{ $gruppo->nome}}</td>
        <td>{{($famiglia) ? $famiglia->nome_famiglia: ""}}</td>
        <td>{{$persona->numero_elenco}}</td>
    </tr>
    </tbody>
</table>

Saluti,
Staff
