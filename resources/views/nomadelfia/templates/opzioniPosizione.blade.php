@php
  $posizioni = in_array($famiglia->tipo, App\Anagrafe\Famiglia\Models\TipoFamiglia::senzaConiuge(), true)
      ? ["CAPO FAMIGLIA" => $famiglia->tipo->nome()]
      : ["CAPO FAMIGLIA" => "Marito", "MOGLIE" => "Moglie"];
  $posizioni += ["FIGLIO NATO" => "Figlio nato", "FIGLIO ACCOLTO" => "Figlio accolto"];
  if (isset($selezionata) && ! array_key_exists($selezionata, $posizioni)) {
      $posizioni[$selezionata] = Illuminate\Support\Str::ucfirst(mb_strtolower($selezionata));
  }
@endphp

@foreach ($posizioni as $posizione => $etichetta)
  <option
    value="{{ $posizione }}"
    @selected($posizione === ($selezionata ?? null))
  >
    {{ $etichetta }}
  </option>
@endforeach
