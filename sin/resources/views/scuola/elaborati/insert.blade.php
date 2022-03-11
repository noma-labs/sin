@extends('scuola.index')

@section('archivio')
    @include('partials.header', ['title' => 'Aggiungi elaborato'])
    <form method="POST" action="{{route('libri.inserisci.Confirm')}}">
        @csrf
        <div class="row">
            <div class="col-md-6">
                 <x-media-library-attachment name="avatar"></x-media-library-attachment>
            </div>
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-6">
                    </div>
                    <div class="col-md-6">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <label for="xClassificazione" class="control-label">Classificazione (*)</label>
                        <select class="form-control" name="xClassificazione" type="text" id="xClassificazione">
                            <option disabled selected>---Seleziona la Classificazione---</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="dimensione">Dimensione</label>
                        <input class="form-control" type="text" name="dimensione" value="{{ old('dimensione')}}">
                    </div>
                    <div class="col-md-4">
                        <label for="critica">Critica</label>
                        <select class="form-control" name="critica" type="text">
                            <option disabled selected>---Seleziona la critica---</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <label for="isbn">ISBN</label>
                        <input class="form-control" type="text" autocomplete="off" value="{{ old('isbn')}}" name="isbn">
                    </div>
                    <div class="col-md-4">
                        <label for="data_pubblicazione">Data pubblicazione</label>
                        <input type="text" class="form-control" id="dataPubblicazione"
                               value="{{ old('data_pubblicazione')}}" name="data_pubblicazione">
                    </div>
                    <div class="col-md-4">
                        <label for="categoria">Categoria</label>
                        <select class="form-control" name="categoria" type="text">
                            <option disabled selected>---Seleziona la categoria---</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <label for="xNote" class="control-label">Note </label>
                        <textarea class="form-control" name="xNote" class="text" rows="2">{{ old('xNote')}}</textarea>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <p class="text-right text-danger ">Le informazioni segnate con (*) sono obbligatorie.</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <button class="btn btn-success" name="_addonly" value="true" type="submit">Salva</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
