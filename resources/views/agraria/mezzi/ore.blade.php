@extends("layouts.app")

@section("title", "Mezzo")

@section("content")
    @include("partials.header", ["title" => "Aggiorna Ore"])
    <div class="container">
        <form action="{{ route("mezzi.aggiorna.ore") }}" method="post">
            @csrf
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th width="50%">Nome</th>
                        <th width="25%">Ore Effettuate</th>
                        <th width="25%">Nuove Ore</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($mezzi as $m)
                        <tr>
                            <td>
                                {{-- <input class="form-control" type="text" value="{{$m->nome}}" disabled> --}}
                                {{ $m->nome }}
                            </td>
                            <td>
                                {{ $m->tot_ore }}
                                {{-- <input class="form-control" type="number" value="{{$m->tot_ore}}" disabled> --}}
                            </td>
                            <td>
                                <input
                                    class="form-control"
                                    type="number"
                                    name="{{ "id" . $m->id }}"
                                    value="{{ old("id" . $m->id) }}"
                                    required
                                />
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <button class="btn btn-success btn-block" type="submit">
                Salva
            </button>
        </form>
    </div>
@endsection
