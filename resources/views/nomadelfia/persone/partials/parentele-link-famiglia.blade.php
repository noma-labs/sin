{{-- The link to the page of the famiglia, marked by the casa of the famiglia --}}
<a href="{{ route("anagrafe.families.show", $famiglia->id) }}">
  <svg
    class="profile-icona-famiglia"
    width="14"
    height="14"
    viewBox="0 0 24 24"
    fill="none"
    stroke="currentColor"
    stroke-width="2.5"
    stroke-linecap="round"
    stroke-linejoin="round"
    aria-hidden="true"
  >
    <path d="M3 11l9-7 9 7"></path>
    <path d="M5 10v10h14V10"></path>
  </svg>
  Famiglia {{ $famiglia->nome_famiglia }}
</a>
