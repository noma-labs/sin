{{-- \resources\views\permissions\index.blade.php --}}
@extends('admin.index')
@section('title', '| Risorse')

@section('archivio')

@include('partials.header', ['title' => 'Gestione risorse'])
    <!-- <a href="{{ route('risorse.create') }}" class="btn btn-success my-2 float-right">Aggiungi risorsa</a> -->
    @foreach ($sistemi as $sistema)
    
    @if($sistema->risorse->count()>0)
    <div class="table-responsive  col-md-8 offset-md-2">
        <h5><b>  {{$sistema->nome}}</b></h5>
        <table class="table table-bordered table-striped">
            <thead class="thead-inverse">
                <tr>
                    <th>Risorsa</th>
                    <th>Operazioni</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($sistema->risorse as $risorsa)
                <tr>
                    <td>{{ $risorsa->nome }}</td>
                    <td>
                    <a href="{{ route('risorse.edit', $risorsa->id) }}" class="btn btn-info pull-left" style="margin-right: 3px;">Modifica</a>

                    {!! Form::open(['method' => 'DELETE', 'route' => ['risorse.destroy', $risorsa->id] ]) !!}
                    {!! Form::submit('Cancella', ['class' => 'btn btn-danger']) !!}
                    {!! Form::close() !!}

                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
    @endforeach
@endsection
