<div class="card text-white bg-primary mb-3">
    <div class="card-body">
        <div class="d-flex justify-content-center align-items-center">
            <h1 class="text-center">{{ $title }}</h1>
        </div>

        @if (isset($subtitle))
            <div class="d-flex justify-content-center align-items-center">
                <h6 class="card-subtitle">{{ $subtitle }}</h6>
            </div>
        @endif
    </div>
</div>
