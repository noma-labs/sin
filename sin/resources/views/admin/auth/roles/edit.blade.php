@extends('admin.index')
@section('title', '| Modifica ruolo')

@section('archivio')
@include('partials.header', ['title' => 'Modifica ruolo'])

<form method="POST" action="{{route('roles.update',  $ruolo->id)}}">
  <input type="hidden" name="_method" value="PUT">
  {{ csrf_field() }}

  <div class="row">
    <div class="col-md-6 offset-md-2">
            <p class="font-weight-normal">Ruolo:: <b>{{$ruolo->nome}}</b></p>
            <p class="font-weight-normal">Descrizione: <b>{{$ruolo->descrizione}}</b> </p> 
    </div>
  </div>

 <div class="row">
  <div class="col-md-8 offset-md-2">
    @if (count($ruolo->risorse) === 0)
          <h4>Assegna le risorsa al nuovo ruolo specificando i permessi</strong></h4>
        @foreach ($sistemi_risorse as $sistema)
        <p class="text-secondary">Risorse associate al sistema: <b>{{$sistema->nome}}</b></p>
        <table class='table table-bordered table-hover table-sm'>
              <thead class='thead-inverse'>
                <tr>
                  <th>Risorsa</th>
                  <th>Visualizza</th>
                  <th>Inserisci</th>
                  <th>Elimina</th>
                  <th>Modifica</th>
                  <th>Prenota</th>
                  <th>Esporta</th>
                  <th>Svuota</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($sistema->risorse as $risorsa)
                    <tr> 
                    <td>{{$risorsa->nome}}</td>
                    <td>
                      <input type='hidden' name="{{$risorsa->id}}[visualizza]" value='0'/>
                      <input type='checkbox' name="{{$risorsa->id}}[visualizza]" value='1' />
                    </td>       
                    <td>
                      <input type='hidden' name="{{$risorsa->id}}[inserisci]" value='0'/>
                      <input type='checkbox' name="{{$risorsa->id}}[inserisci]" value='1' />
                    </td>
                    <td>
                      <input type='hidden' name="{{$risorsa->id}}[elimina]" value='0'/>
                      <input type='checkbox' name="{{$risorsa->id}}[elimina]" value='1'/></td>
                    </td>
                    <td>
                      <input type='hidden' name="{{$risorsa->id}}[modifica]" value='0'/>
                      <input type='checkbox' name="{{$risorsa->id}}[modifica]" value='1'/>
                    </td>
                    <td>
                      <input type='hidden' name="{{$risorsa->id}}[prenota]" value='0'/>
                      <input type='checkbox' name="{{$risorsa->id}}[prenota]" value='1'/>
                    </td>
                    <td>
                      <input type='hidden' name="{{$risorsa->id}}[esporta]" value='0' />
                      <input type='checkbox' name="{{$risorsa->id}}[esporta]" value='1'/>
                    </td>
                    <td>
                      <input type='hidden' name="{{$risorsa->id}}[svuota]" value='0'/>
                      <input type='checkbox' name="{{$risorsa->id}}[svuota]" value='1'/>            
                    </td> 
                  </tr>
                @endforeach
              </tbody>
            </table>
          @endforeach
        {{ Form::submit('Aggiungi', array('class' => 'btn btn-primary')) }}
        </div>
        </div>
        {{ Form::close() }}
    @else
     <table class='table table-bordered table-hover table-sm'>
      <thead class='thead-inverse'>
        <tr>
          <th>Sistema</th>
          <th>Risorsa</th>
          <th>visualizza</th>
          <th>Inserisci</th>
          <th>Elimina</th>
          <th>Modifica</th>
          <th>Prenota</th>
          <th>Esporta</th>
          <th>Svuota</th>
        </tr>
      </thead>
      <tbody>
      @foreach ($ruolo->risorse as $risorsa)

            <tr>
            <td>{{$risorsa->sistema->nome}}</td>
            <td>{{$risorsa->nome}}</td>
            <td>
              <input type='hidden' name="{{$risorsa->id}}[visualizza]" value='0'/>
              <input type='checkbox' name="{{$risorsa->id}}[visualizza]" value='1' {{ $risorsa->permessi->visualizza ? 'checked' : '' }}/>
            </td>       
            <td>
              <input type='hidden' name="{{$risorsa->id}}[inserisci]" value='0'/>
              <input type='checkbox' name="{{$risorsa->id}}[inserisci]" value='1' {{ $risorsa->permessi->inserisci ? 'checked' : '' }}/>
            </td>
            <td>
              <input type='hidden' name="{{$risorsa->id}}[elimina]" value='0'/>
              <input type='checkbox' name="{{$risorsa->id}}[elimina]" value='1' {{ $risorsa->permessi->elimina ? 'checked' : '' }}/></td>
            </td>
            <td>
              <input type='hidden' name="{{$risorsa->id}}[modifica]" value='0'/>
              <input type='checkbox' name="{{$risorsa->id}}[modifica]" value='1' {{ $risorsa->permessi->modifica ? 'checked' : '' }}/>
            </td>
            <td>
              <input type='hidden' name="{{$risorsa->id}}[prenota]" value='0'/>
              <input type='checkbox' name="{{$risorsa->id}}[prenota]" value='1' {{ $risorsa->permessi->prenota ? 'checked' : '' }}/>
            </td>
            <td>
              <input type='hidden' name="{{$risorsa->id}}[esporta]" value='0' />
              <input type='checkbox' name="{{$risorsa->id}}[esporta]" value='1' {{ $risorsa->permessi->esporta ? 'checked' : '' }}/>
            </td>
            <td>
              <input type='hidden' name="{{$risorsa->id}}[svuota]" value='0'/>
              <input type='checkbox' name="{{$risorsa->id}}[svuota]" value='1' {{ $risorsa->permessi->svuota ? 'checked' : '' }}/>            
            </td> 
          </tr>
        @endforeach
      </tbody>
    </table>
    <button class="btn btn-primary"   type="submit">Salva</button>
    @endif
   </div>
  </div>
</form>


@endsection
