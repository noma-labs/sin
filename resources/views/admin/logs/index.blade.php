@extends("admin.index")

@section("archivio")
    @include("partials.header", ["title" => "Amministrazione attività"])
    <div class="row">
        <div class="col-md-12">
            @if (count($activities))
                <table class="table table-striped table-bordered table-sm">
                    <thead class="thead-inverse">
                        <tr>
                            <th style="width: 10%">Data attività</th>
                            <th style="width: 5%">Utente</th>
                            <th style="width: 15%">Oggetto</th>
                            <th style="width: 8%">Nome</th>
                            <th style="width: 6%">Operaz.</th>
                            <th style="width: 50%">Proprietà</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($activities as $activity)
                            @if ($activity->subject)
                                <tr>
                                    <td>
                                        {{ Carbon::now()->diffForHumans($activity["created_at"]) }}
                                    </td>
                                    <td>
                                        @if ($activity->causer)
                                            {{ $activity->causer->username }}
                                        @endif
                                    </td>
                                    <td>
                                        @if (str_contains($activity["subject_type"], "Libro"))
                                            <a
                                                class="text-warning"
                                                href="{{ route("libro.dettaglio", $activity->subject->id) }}"
                                            >
                                                {{ $activity->subject->collocazione }}
                                                -
                                                {{ $activity->subject->titolo }}
                                            </a>
                                        @else
                                            {{ $activity->subject_type }}
                                        @endif
                                    </td>
                                    <td>{{ $activity["log_name"] }}</td>
                                    <td>{{ $activity["description"] }}</td>
                                    <td>{{ $activity["properties"] }}</td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="well">
                    <h4>Non ci sono attività registrate.</h4>
                </div>
            @endif
        </div>
    </div>
@endsection
