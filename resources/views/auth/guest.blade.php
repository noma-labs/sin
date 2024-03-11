@extends('layouts.app')

@section('archivio')

<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
          <div class="panel-group">
            <div class="panel panel-default">
              <div class="panel-heading">Utente Ospite </div>
              <div class="panel-body">
                <p>Operazione non consentita.</p>
                <p>Per procedere devi effettuare il Login</p>
                  <div class="row">
                    <div class="col-md-4 col-md-offset-4">
                      <button  class="btn btn-primary" onclick="event.preventDefault(); window.history.back();"> <a>Continua come ospite</a>
                    </div>
                  <!-- Effettua il Login
                      <a href="{{  route('login') }}" class="btn btn-primary">Login</a>
                    </div> -->

                    </div>
                  </div>

              </div>
            </div>

          </div>
        </div>
    </div>
</div>
@endsection
