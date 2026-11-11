{{-- The famiglia of the persona in the parentele, with her posizione in it --}}
<div class="text-body-secondary small mb-2">
  @include("nomadelfia.persone.partials.parentele-link-famiglia")
  &middot; {{ Illuminate\Support\Str::ucfirst(mb_strtolower($posizioneFamiglia)) }}
  @if (! $nelNucleo)
    &middot; non più nel nucleo
  @endif
</div>
