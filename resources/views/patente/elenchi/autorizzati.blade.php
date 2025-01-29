@foreach ($patentiAutorizzati->chunk(50) as $chunk)
    <div class="page">
        <table class="table table-striped">
            <thead>
                <tr class="table-warning">
                    <th>#</th>
                    <th>Nome Cognome</th>
                    <th>Patente</th>
                    <th>Data Nascita</th>
                </tr>
            </thead>
            @foreach ($chunk as $patente)
                <tr class="table-primary" hoverable>
                    <td>
                        @if ($loop->parent)
                            {{ $loop->parent->index * 50 + $loop->iteration }}
                        @endif
                    </td>
                    <td>
                        @isset($patente->persona->nome)
                            {{ $patente->persona->nome }}
                        @endisset

                        @isset($patente->persona->cognome)
                            {{ $patente->persona->cognome }}
                        @endisset
                    </td>
                    <td>{{ $patente->categorieAsString() }}</td>
                    <td>
                        @isset($patente->persona->data_nascita)
                            {{ $patente->persona->data_nascita }}
                        @endisset
                    </td>
                </tr>
            @endforeach
        </table>
    </div>
@endforeach
