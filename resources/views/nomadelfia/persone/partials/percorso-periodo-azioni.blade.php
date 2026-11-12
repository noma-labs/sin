{{--
  The corrections of an entrata or an uscita of a period in Nomadelfia in the percorso of the profile: the date, and for
  an uscita also the removal of the last data di uscita (when possible) and the deletion of the whole period.
--}}
<div class="d-flex flex-wrap gap-2 mt-2">
  @if ($uscita)
    @include("nomadelfia.templates.modificaDataUscita", ["persona" => $persona, "data_uscita" => $periodo->data_uscita, "collegate" => $dateCollegate[$periodo->data_entrata]["uscita"]])
    @if ($periodo->data_entrata === $periodoRiapribile?->data_entrata)
      @include("nomadelfia.templates.eliminaDataUscita", ["persona" => $persona, "periodo" => $periodo, "riaperte" => $riaperte])
    @endif

    @include("nomadelfia.templates.eliminaPeriodoPopolazione", ["persona" => $persona, "periodo" => $periodo])
  @else
    @include("nomadelfia.templates.modificaDataEntrata", ["persona" => $persona, "data_entrata" => $periodo->data_entrata, "collegate" => $dateCollegate[$periodo->data_entrata]["entrata"]])
  @endif
</div>
