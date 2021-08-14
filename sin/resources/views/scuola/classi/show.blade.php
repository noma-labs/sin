@extends('nomadelfia.index')

@section('archivio')
@include('partials.header', ['title' => 'Gestione Classe'])

<div class="row justify-content-md-center">
    <div class="col-md-12">
      <div class="card border-dark my-2">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="row">
                        <label class="col-sm-6 font-weight-bold">Classe:</label>
                        <div class="col-sm-6">
                        {{$classe->tipo->nome}}
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-sm-6 font-weight-bold">Responsabile:</label>
                        <div class="col-sm-6">
{{--                            @if ($classe->capogruppoAttuale())--}}
{{--                                <a href="{{}}"> XXXXX </a>--}}
{{--                            @else--}}
                                <span class="text-danger">Responabile non assegnato</span>
{{--                            @endif--}}
                        </div>
                    </div>
                </div> <!--end col dati gruppo -->
                <div class="col-md-8">
                    @include("scuola.templates.aggiungiAlunno",["classe"=>$classe])
                </div> <!--end col -->
            </div>  <!--end row -->           
         </div> <!--end card body -->
       </div>  <!--end card -->

    </div>
</div>

<div class="row justify-content-md-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Alunni <span class="badge badge-primary">{{$alunni->count()}}</span></h4>
                <ul class="list-group list-group-flush">
                    @foreach($alunni->get() as $alunno)
                        <li class="list-group-item"> @year($alunno->data_nascita)
                            @include("nomadelfia.templates.persona", ['persona' => $alunno])
                            (@diffYears($alunno->data_nascita) anni)</li>
                    @endforeach
                </ul>
            </div>

        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Coordinatori</h4>
                <ul class="list-group list-group-flush">

                <ul>
            </div> 
        </div> 
    </div>


 </div>    
@endsection


