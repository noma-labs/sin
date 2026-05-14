@extends("archiviodocumenti.layout")

@section("content")
    @include("partials.header", ["title" => "Documenti"])

    @php
        $maxCount = $countByDecade->max('count') ?: 1;
    @endphp

    <div class="card border-0 bg-light mb-4">
        <div class="card-body">
            <p class="small text-muted mb-2">Documenti per anno</p>
            <div class="d-flex align-items-flex-end gap-2" style="height: 120px; align-items: flex-end;">
                @foreach ($countByDecade as $item)
                    @php
                        $barHeight = (int) round(($item->count / $maxCount) * 100);
                    @endphp
                    <div class="d-flex flex-column align-items-center" style="flex: 1;">
                        <span class="small text-muted mb-1" style="font-size: 0.7rem;">{{ $item->count }}</span>
                        <div class="bg-primary rounded-top" style="width: 100%; height: {{ $barHeight }}px;"></div>
                        <span class="small text-muted mt-1" style="font-size: 0.7rem;">{{ $item->decade }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
