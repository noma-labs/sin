@extends('nomadelfia.index')

@section('archivio')
	<sin-header title="{{'Modifica Incarico: '.$incarico->nome_azienda }}">
		
	</sin-header>
	<azienda-edit url_azienda_edit="{{ route('api.nomadeflia.azienda.edit', $incarico->id ) }}" url_persona url_mansioni="{{ route('api.nomadeflia.azienda.mansioni') }}" url_stati="{{ route('api.nomadeflia.azienda.stati') }}" url_modifica_lavoratore="{{ route('api.nomadeflia.azienda.modifica.lavoratore') }}" id_azienda="{{$incarico->id}}"></azienda-edit>
@endsection