{{-- What moves with an entrata or an uscita when its date is corrected, shown before saving the correction. --}}
<div class="mt-3 small">
  @if ($collegate->isEmpty())
    <p class="text-body-secondary mb-0">{{ $nessuna }}</p>
  @else
    <p class="mb-1">Verranno spostate alla nuova data anche:</p>
    <ul class="list-unstyled mb-0">
      @foreach ($collegate as $collegata)
        <li class="border rounded px-2 py-1 mb-1">
          <span class="text-body-secondary">{{ $collegata->tipo }}</span>
          <span class="fw-semibold">{{ $collegata->nome }}</span>
          <div class="text-body-secondary">
            dal {{ Carbon::parse($collegata->inizio)->translatedFormat("j M Y") }} {{ $collegata->fine ? "al " . Carbon::parse($collegata->fine)->translatedFormat("j M Y") : "a oggi" }}
          </div>
        </li>
      @endforeach
    </ul>
  @endif
</div>
