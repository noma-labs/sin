@extends("admin.index")

@section("content")
    @include("partials.header", ["title" => "Amministrazione attività"])
    <div class="row">
        <div class="col-md-12">
            @if (count($activities))
                <table class="table table-striped">
                    <thead>
                        <tr class="table-warning">
                            <th>Data attività</th>
                            <th>Utente</th>
                            <th>Oggetto</th>
                            <th>Nome</th>
                            <th>Operaz.</th>
                            <th>Proprietà</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($activities as $activity)
                            @if ($activity->subject)
                                <tr class="table-primary">
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
                                                href="{{ route("books.show", $activity->subject->id) }}"
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
