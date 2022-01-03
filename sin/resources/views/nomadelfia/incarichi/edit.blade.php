@extends('nomadelfia.index')

@section('archivio')
	<sin-header title="{{'Modifica Incarico: '.$incarico->nome }}">
		
	</sin-header>
{{--	<azienda-edit url_aggiungi="{{route('api.nomadelfia.incarichi.aggiungi.lavoratore')}}" base_url='/api/nomadelfia/incarichi' url_azienda_edit="{{ route('api.nomadeflia.incarichi.edit', $incarico->id ) }}" url_persona url_mansioni="{{ route('api.nomadeflia.azienda.mansioni') }}" url_stati="{{ route('api.nomadeflia.azienda.stati') }}" url_modifica_lavoratore="{{ route('api.nomadeflia.azienda.modifica.lavoratore') }}" id_azienda="{{$incarico->id}}">--}}

{{--	</azienda-edit>--}}

	<div>
		<div class="row">
			<div class="col-md-8 table-responsive">
				<table class="table table-hover table-bordered">
					<thead class="thead-inverse">
					<th scope="col">Nominativo</th>
					<th scope="col">Data Inizio</th>
					<th scope="col">Operazioni</th>
					</thead>
					<tbody>
						@foreach ($lavoratori as $lavoratore)
					<tr>
							<td>{{$lavoratore->nominativo}} </td>
							<td>{{$lavoratore->pivot->data_inizio}}</td>
							<td></td>
					 </tr>
						@endforeach
					</tbody>
				</table>
				<div class="row">
					<div class="col-sm-2 offset-sm-10">
{{--						<button class="btn btn-success btn-block" role="button" @click="aggiungiLavoratore">Aggiungi</button>--}}
					</div>
				</div>
			</div>

		</div>
		@include('nomadelfia.templates.aggiungiPersonaIncarico',['incarico'=>$incarico])

	</div>
@endsection