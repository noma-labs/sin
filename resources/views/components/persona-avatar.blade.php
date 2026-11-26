<span
  {{ $attributes->class(["persona-avatar", "persona-avatar-{$size}", "persona-avatar-{$sesso}" => $urlFoto === null]) }}
  aria-hidden="true"
>
  @if ($urlFoto)
    <img src="{{ $urlFoto }}" alt="" loading="lazy" />
  @else
    {{ $iniziali }}
  @endif
</span>
