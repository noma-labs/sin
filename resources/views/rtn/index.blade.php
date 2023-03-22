@extends('layouts.app')

@section('title', 'Home Rtn')

@section("archivioname","film")

@section('archivio')
  <div class="container">
  	<div class="row">
  		<div class="col">
  			<div class="card">
  				<div class="card-header bg-primary text-white">
  					Archivio film
  				</div>
				<div class="card-body">
				    <div class="list-group-flush">
				    	<a href="{{ route('film.search') }}" class="list-group-item list-group-item-action">Ricerca</a>
						<a href="#" class="list-group-item list-group-item-action">Inserimento</a>
						<a href="#" class="list-group-item list-group-item-action">Ultima trasmissione</a>
						<a href="#" class="list-group-item list-group-item-action">Modifica</a>
						<a href="#" class="list-group-item list-group-item-action disabled">Prestiti</a>
				    </div>
				</div>
			</div>
  		</div>
  		<div class="col">
  			<div class="card">
  				<div class="card-header bg-primary text-white">
  					Commissione TV
  				</div>
			    <div class="card-body">
			    	<div class="list-group-flush">
				    	<a href="#" class="list-group-item list-group-item-action">Ricerca</a>
						<a href="#" class="list-group-item list-group-item-action">Inserimento</a>
						<a href="#" class="list-group-item list-group-item-action">Ultima trasmissione</a>
						<a href="#" class="list-group-item list-group-item-action">Modifica</a>
						<a href="#" class="list-group-item list-group-item-action disabled">Prestiti</a>
				    </div>
			    </div>
			</div>
  		</div>
  	</div>
  	<br>
  	<div class="row">
  		<div class="col">
  			<div class="card">
  				<div class="card-header bg-primary text-white">
  					Archivio Professionale
  				</div>
			    <div class="card-body">
			    	<div class="list-group-flush">
				    	<a href="#" class="list-group-item list-group-item-action">Creazione Programmazione Settimanale</a>
						<a href="#" class="list-group-item list-group-item-action">Consulta Programmazione Settimanale</a>
						<a href="#" class="list-group-item list-group-item-action">Modifica</a>
				    </div>
			    </div>
			</div>
  		</div>
  		<div class="col">
  			<div class="card">
  				<div class="card-header bg-primary text-white">
  					Amministrazione
  				</div>
			    <div class="card-body">
			    	<div class="list-group-flush">
				    	<a href="#" class="list-group-item list-group-item-action">Backup DB</a>
						<a href="#" class="list-group-item list-group-item-action">Manutenzione DB</a>
				    </div>
			    </div>
			</div>
  		</div>
  	</div>
  </div>
@endsection
