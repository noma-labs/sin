{{-- The bond between two coniugi, or between a persona and the famiglia she heads without coniuge ($casa) --}}
<span class="albero-anello">
  @if (! empty($numero))
    <span class="albero-numero">{{ $numero }}</span>
  @endif

  <span class="albero-anello-segno" aria-hidden="true">
    @if ($casa ?? false)
      <svg
        width="16"
        height="16"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="2.5"
        stroke-linecap="round"
        stroke-linejoin="round"
      >
        <path d="M3 11l9-7 9 7"></path>
        <path d="M5 10v10h14V10"></path>
      </svg>
    @else
      ⚭
    @endif
  </span>
  <span class="visually-hidden">
    {{ $casa ?? false ? "capo della famiglia" : "sposati" }}
  </span>
  @if (! empty($anno))
    <span>{{ ($casa ?? false ? "dal " : "") . $anno }}</span>
  @endif
</span>
