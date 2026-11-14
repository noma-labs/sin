<!doctype html>
<html lang="it">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Anno scolastico {{ $anno->as }}</title>
  <link rel="stylesheet" href="{{ asset("css/app.css") }}" />
  <style>
    @page {
      size: A4;
      margin: 0;
    }
  </style>
</head>
<body class="anno-stampa">
  <div class="d-flex flex-wrap justify-content-center gap-2 py-3 d-print-none">
    <a
      class="btn btn-outline-secondary"
      href="{{ route("scuola.anno.show", $anno->id) }}"
    >
      Torna all'anno scolastico
    </a>
    <button type="button" class="btn btn-primary" onclick="window.print()">
      Stampa
    </button>
  </div>

  <main class="stampa-foglio">
    <header class="stampa-header">
      <div class="stampa-intestazione">
        <img
          src="{{ asset("images/logo-noma.png") }}"
          alt="Nomadelfia"
          class="stampa-logo"
        />
        <span class="stampa-occhiello">Scuola familiare</span>
        <h1 class="stampa-titolo">Anno scolastico {{ $anno->as }}</h1>
      </div>
      <span class="stampa-data stampa-muted fst-italic">
        Stampato il {{ now()->format("d/m/Y") }}
      </span>
    </header>

    <div class="stampa-colonne">
      @foreach ($cicli as $ciclo)
        <section class="stampa-ciclo stampa-ciclo-{{ $ciclo->ciclo }}">
          <div class="stampa-ciclo-nome">
            <span>{{ $ciclo->ciclo }}</span>
            <span>{{ $ciclo->alunniCount }}</span>
          </div>
          @foreach ($ciclo->classi as $classe)
            <div class="stampa-classe">
              <div class="stampa-classe-nome">
                <span>{{ $classe->nome }}</span>
                <span>{{ count($classe->alunni) }}</span>
              </div>
              <ol class="stampa-alunni">
                @foreach ($classe->alunni as $alunno)
                  <li>{{ $alunno->nome }} {{ $alunno->cognome }}</li>
                @endforeach
              </ol>
            </div>
          @endforeach
        </section>
      @endforeach
    </div>

    <footer class="stampa-footer">
      @if ($anno->responsabile)
        <span class="stampa-muted">
          Responsabile: {{ $anno->responsabile->nome }} {{ $anno->responsabile->cognome }}
        </span>
      @endif

      <div class="stampa-totale">
        <span>Totale alunni</span>
        <span>{{ $anno->totalStudents }}</span>
      </div>
    </footer>
  </main>
</body>
</html>
