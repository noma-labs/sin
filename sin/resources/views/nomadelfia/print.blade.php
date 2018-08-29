<!DOCTYPE html>
<!-- dompPDF got Error "No block-level parent found.  Not good." ù
   look at the issue: https://github.com/dompdf/dompdf/issues/1494
   Solution: no space after html, head. body.
  -->
<html><head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    
    <title>Page Title</title>
    <style>
      /* .page-break {
          page-break-after: always;
      }
      body {
      font-family: sans-serif;
      margin-left: 0.4cm;
      margin-top: 0.9cm;
     } */
    </style>
</head>
<body>
  <h1>Popolazione Nomadelfia</h1>
  <h2>{{Carbon::now(new DateTimeZone("Europe/Rome"))->toDateString() }} </h2>

  <h5>Totale:{{$totale}}</h5>
  <div class="page-break"></div>
  <div class="container">
    <div class="row">
      <div class="col-md-6">
      <p class="font-weight-bold">Uomini maggiorenni {{$maggiorenniUomini->count()}}</p>
        <div class="row">
          @foreach ($maggiorenniUomini->get()->chunk(60) as $chunk)
                <div class="col-md-6">
                  @foreach ($chunk as $uomo)
                        <div class="col-xs-4">{{ $uomo->nominativo }}</div>
                    @endforeach
                </div>
          @endforeach
        </div>
      </div>

      <div class="col-md-6">
        <p class="font-weight-bold">Donne maggiorenni {{$maggiorenniDonne->count()}}</p>
        <div class="row">
          @foreach ($maggiorenniDonne->get()->chunk(60) as $chunk)
                  <div class="col-md-6">
                    @foreach ($chunk as $donna)
                     <div class="col-xs-4">{{ $donna->nominativo }}</div>
                     @endforeach
                  </div>
            @endforeach
          </div>
        </div>
      </div>
    </div>

  <div class="page-break"></div>
    <div class="container">
    <h2> Figli minorenni {{$minorenniCount}}</h2>
    @foreach ($minorenni->chunk(4) as $anni)
      <div class="row">
           @foreach ($anni as $key =>$anno)
              <div class="col-md-3">
              <span class="font-weight-bold">Nati {{$key}}</span>
               @foreach ($anno as $sesso)
                  @foreach($sesso as $persona)
                    <div>{{ $persona->nominativo }}</div>
                  @endforeach
                  @if (!$loop->last)
                    <hr size="4">
                  @endif
                @endforeach
              </div>
            @endforeach
        </div>
      @endforeach
      </div>

  <div class="page-break"></div>
  <div class="container">
    <div class="row">
      <div class="col-md-3">
        <h2>Effettivi Uomini {{App\Nomadelfia\Models\Posizione::perNome("effettivo")->persone()->uomini()->count()}}</h2>
          @foreach(App\Nomadelfia\Models\Posizione::perNome("effettivo")->persone()->uomini()->get() as $uomo)
          <div>{{$uomo->nominativo}}
          </div>
          @endforeach
      </div>
      <div class="col-md-3">
        <h2>Effettivi Donne {{App\Nomadelfia\Models\Posizione::perNome("effettivo")->persone()->donne()->count()}}</h2>
          @foreach(App\Nomadelfia\Models\Posizione::perNome("effettivo")->persone()->donne()->get() as $donna)
          <div>{{$donna->nominativo}}
          </div>
          @endforeach
      </div>
      <div class="col-md-3">
        <h2>Sacerdoti {{App\Nomadelfia\Models\Stato::perNome("sacerdote")->persone()->presente()->count()}}</h2>
          @foreach(App\Nomadelfia\Models\Stato::perNome("sacerdote")->persone()->get() as $sac)
          <div>{{$sac->nominativo}}
          </div>
          @endforeach
      </div>
      <div class="col-md-3">
        <h2>Mamme vocazione {{App\Nomadelfia\Models\Stato::perNome("mammavocazione")->persone()->presente()->count()}}</h2>
          @foreach(App\Nomadelfia\Models\Stato::perNome("mammavocazione")->persone()->get() as $mamma)
          <div>{{$mamma->nominativo}}
          </div>
          @endforeach
      </div>
    </div>
  </div>

  <div class="page-break">
  </div>
  <div class="container">
    <div class="row">
      <div class="col-md-3">
        <h2>Postulanti {{App\Nomadelfia\Models\Posizione::perNome("postulante")->persone()->count()}}</h2>
        <h4>Uomini {{App\Nomadelfia\Models\Posizione::perNome("postulante")->persone()->uomini()->count()}}</h4>
         @foreach(App\Nomadelfia\Models\Posizione::perNome("postulante")->persone()->uomini()->get() as $uomo)
          <div>{{$uomo->nominativo}}
          </div>
          @endforeach
          <h4>Donne {{App\Nomadelfia\Models\Posizione::perNome("postulante")->persone()->donne()->count()}}</h4>
         @foreach(App\Nomadelfia\Models\Posizione::perNome("postulante")->persone()->donne()->get() as $donna)
          <div>{{$donna->nominativo}}
          </div>
          @endforeach
      </div>

      <div class="col-md-3">
      <h2>Ospiti {{App\Nomadelfia\Models\Posizione::perNome("ospite")->persone()->presente()->count()}}</h2>
        <h4>Uomini {{App\Nomadelfia\Models\Posizione::perNome("ospite")->persone()->presente()->uomini()->count()}}</h4>
         @foreach(App\Nomadelfia\Models\Posizione::perNome("ospite")->persone()->presente()->uomini()->get() as $uomo)
          <div>{{$uomo->nominativo}}
          </div>
          @endforeach
          <h4>Donne {{App\Nomadelfia\Models\Posizione::perNome("ospite")->persone()->presente()->donne()->count()}}</h4>
         @foreach(App\Nomadelfia\Models\Posizione::perNome("ospite")->persone()->presente()->donne()->get() as $donna)
          <div>{{$donna->nominativo}}
          </div>
          @endforeach
      </div>

      <div class="col-md-3">
        <h2>Figli 18...21 {{App\Nomadelfia\Models\Posizione::perNome("figlio")->persone()->fraeta(18,21)->presente()->count()}}</h2>
        <h4>Uomini {{App\Nomadelfia\Models\Posizione::perNome("figlio")->persone()->fraeta(18,21)->presente()->uomini()->count()}}</h4>
        @foreach(App\Nomadelfia\Models\Posizione::perNome("figlio")->persone()->fraeta(18,21)->presente()->uomini()->get() as $uomo)
          <div>{{$uomo->nominativo}}
          </div>
          @endforeach
          <h4>Donne {{App\Nomadelfia\Models\Posizione::perNome("figlio")->persone()->fraeta(18,21)->presente()->donne()->count()}}</h4>
        @foreach(App\Nomadelfia\Models\Posizione::perNome("figlio")->persone()->fraeta(18,21)->presente()->donne()->get() as $donna)
          <div>{{$donna->nominativo}}
          </div>
          @endforeach
      </div>

      <div class="col-md-3">
        <h2>Figli>21    {{App\Nomadelfia\Models\Posizione::perNome("figlio")->persone()->DaEta(21)->presente()->count()}}</h2>
        <h4>Uomini {{App\Nomadelfia\Models\Posizione::perNome("figlio")->persone()->DaEta(21)->presente()->uomini()->count()}}</h4>
        @foreach(App\Nomadelfia\Models\Posizione::perNome("figlio")->persone()->DaEta(21)->presente()->uomini()->get() as $uomo)
          <div>{{$uomo->nominativo}}
          </div>
          @endforeach
          <h4>Donne {{App\Nomadelfia\Models\Posizione::perNome("figlio")->persone()->DaEta(21)->presente()->donne()->count()}}</h4>
        @foreach(App\Nomadelfia\Models\Posizione::perNome("figlio")->persone()->DaEta(21)->presente()->donne()->get() as $donna)
          <div>
            {{$donna->nominativo}}
          </div>
          @endforeach
      </div>
    </div>
    </div>
<!-- fine postulanti opiti figli -->

 <div class="container">
    <div class="row">
    <h2>Famiglie</h2>
      <div class="col-md-3">
        
      </div>
    </div>
  </div>
<!-- fine gamiglia -->

<div class="page-break"></div>
<div class="container">
<h2>Gruppi Familiari</h2>
  @foreach ($gruppifamiliari->get()->chunk(4) as $chunk)
      <div class="row">
          @foreach ($chunk as $gruppo)
              <div class="col-md-3"> 
              <p class="font-weight-bold">{{$gruppo->nome}} {{$gruppo->persone()->count()}}</p>
              @if ($gruppo->capogruppiAttuali->isNotEmpty())
              <p class="font-weight-bold"> Capogruppo: {{$gruppo->capogruppiAttuali->first()->nominativo}}</p> 
              @else
              <p class="text-danger">Senza capogruppo</p> 
            @endif
            @foreach($gruppo->famiglie as $famiglia)
                @if ($famiglia->single->isNotEmpty())
                <div class="font-weight-bold"> {{$famiglia->single->first()->nominativo}}</div>
                @else
                <div class="font-weight-bold">@if ($famiglia->capofamiglia->isNotEmpty())  {{$famiglia->capofamiglia()->first()->nominativo}} @endif</div>
                <div class="font-weight-bold">@if ($famiglia->moglie->isNotEmpty())  {{$famiglia->moglie->first()->nominativo}} @endif</div>
                <ul>
                  @foreach($famiglia->figli as $figlio)
                  <li>{{Carbon::parse($figlio->data_nascita_persona)->year}} {{$figlio->nominativo}}</li>
                  @endforeach
                </ul>
                @endif
              @endforeach
              </div>
          @endforeach
      </div>
  @endforeach
</div>
 <!-- fine gruppi familiari -->
 <div class="page-break"></div>
 <div class="container">
  <h2>Scuola</h2>
 </div>
  <!-- fine scuola  -->

  <div class="page-break"></div>
  <div class="container">
    <h2>Aziende</h2>
    @foreach ($aziende->get()->chunk(3) as $chunk)
    <div class="row">
        @foreach ($chunk as $azienda)
            <div class="col-md-4">
            <p class="font-weight-bold"> {{ $azienda->nome_azienda }} {{$azienda->lavoratoriAttuali()->count()}}</p>
           
            @foreach($azienda->lavoratoriAttuali as $persona)
                <div>{{$persona->nominativo}}</div>
            @endforeach
            
            </div>
        @endforeach
    </div>
      @endforeach
  </div>
  <!-- fine azienda  -->
  <div class="page-break"></div>
  <div class="container">
    <p class="font-weight-bold">Popolazione Nomadelfia</p> 
    <p>{{Carbon::now()->toDateString()}}</p>

    <div>Maggiorenni: {{App\Nomadelfia\Models\Persona::maggiorenni()->presente()->count()}}</div>
    <div>Uomini: {{App\Nomadelfia\Models\Persona::maggiorenni()->uomini()->presente()->count()}}</div>
    <div>Donne: {{App\Nomadelfia\Models\Persona::maggiorenni()->donne()->presente()->count()}}</div>
    <hr>
    <div>Effettivi: {{App\Nomadelfia\Models\Posizione::perNome("effettivo")->persone()->count()}}</div>
    <div>Uomini: {{App\Nomadelfia\Models\Posizione::perNome("effettivo")->persone()->uomini()->count()}}</div>
    <div>Donne: {{App\Nomadelfia\Models\Posizione::perNome("effettivo")->persone()->donne()->count() }}</div>
    <hr>
    <div>Postulante: {{App\Nomadelfia\Models\Posizione::perNome("postulante")->persone()->count()}}</div>
    <div>Uomini: {{App\Nomadelfia\Models\Posizione::perNome("postulante")->persone()->uomini()->count()}}</div>
    <div>Donne: {{App\Nomadelfia\Models\Posizione::perNome("postulante")->persone()->donne()->count() }}</div>
    <hr>
    <div>Sacerdoti: {{App\Nomadelfia\Models\Stato::perNome("sacerdote")->persone()->presente()->count()}}</div>
    <div>Mamme di vocazione: {{App\Nomadelfia\Models\Stato::perNome("mammavocazione")->persone()->presente()->count()}}</div>
    <hr>
    <div>Figli Maggiorenni: {{App\Nomadelfia\Models\Posizione::perNome("figlio")->persone()->maggiorenni()->presente()->count()}}</div>
    <div>Uomini: {{App\Nomadelfia\Models\Posizione::perNome("figlio")->persone()->maggiorenni()->uomini()->presente()->count()}}</div>
    <div>Donne: {{App\Nomadelfia\Models\Posizione::perNome("figlio")->persone()->maggiorenni()->donne()->presente()->count()}}</div>
    <hr>
    <div>Figli Minorenni: {{App\Nomadelfia\Models\Posizione::perNome("figlio")->persone()->minorenni()->presente()->count()}}</div>
    <div>Uomini: {{App\Nomadelfia\Models\Posizione::perNome("figlio")->persone()->minorenni()->uomini()->presente()->count()}}</div>
    <div>Donne: {{App\Nomadelfia\Models\Posizione::perNome("figlio")->persone()->minorenni()->donne()->presente()->count()}}</div>
    <div>Accolti: {{App\Nomadelfia\Models\Posizione::perNome("figlio")->persone()->minorenni()->donne()->presente()->count()}}</div>
    <div>Nati da matrimoni: {{App\Nomadelfia\Models\Posizione::perNome("figlio")->persone()->minorenni()->donne()->presente()->count()}}</div>
    <hr>
    <div>Famiglia</div>
  </div>

</body>
</html>

