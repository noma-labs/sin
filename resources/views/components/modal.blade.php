@php
    $modalId = "modal-" . \Illuminate\Support\Str::uuid();
@endphp

<button
    type="button"
    class="btn {{ $buttonStyle }}"
    data-bs-toggle="modal"
    data-bs-target="#{{ $modalId }}"
>
    {{ $buttonTitle }}
</button>

<div
    class="modal fade"
    id="{{ $modalId }}"
    tabindex="-1"
    role="dialog"
    aria-labelledby="{{ $modalId }}Title"
    aria-hidden="true"
>
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">
                    {{ $modalTitle }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{ $body }}
            </div>
            <div class="modal-footer">
                @isset($footer)
                    {{ $footer }}
                @endisset
            </div>
        </div>
    </div>
</div>
