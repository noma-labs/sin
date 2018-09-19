@extends('rtn.film.navbar')

@section('title', 'Film')

@section('archivio')
	<sin-header title="Ricerca"></sin-header>
	<form>
	  <div class="form-row">
	  	<div class="col">
	  		<label for="serie">Serie</label>
	  		<select id="serie" class="form-control">
	  			<option value="" selected>Nessuno</option>
			    @foreach($serie as $s)
			    <option value="{{$s->serie_tv}}">{{$s->serie_tv}}</option>
			    @endforeach
			</select>
	  	</div>
	  	<div class="col">
	  		<div class="form-group">
			    <label for="desc">Descrizione</label>
			    <input type="text" class="form-control" id="desc" placeholder="Descrizione...">
			</div>
	  	</div>
	  	<div class="col">
	  		<label for="numero">Numero</label>
	  		<div class="input-group">
				<input type="number" class="form-control" placeholder="Numero...">
			</div>
	  	</div>
	  	<div class="col">
	  		
		</div>
	  </div>
	  <br>
	  <button type="submit" class="btn btn-primary">Submit</button>
	</form>
@endsection