@extends('nomadelfia.index')

@section('archivio')
	<sin-header title="{{'Modifica azienda: '.$azienda->nome_azienda }}">
		
	</sin-header>
	<azienda-edit url_azienda_edit="{{ route('api.nomadeflia.azienda.edit', $azienda->id ) }}" url_persona url_mansioni="{{ route('api.nomadeflia.azienda.mansioni') }}" url_stati="{{ route('api.nomadeflia.azienda.stati') }}" url_modifica_lavoratore="{{ route('api.nomadeflia.azienda.modifica.lavoratore') }}" id_azienda="{{$azienda->id}}"></azienda-edit>
@endsection