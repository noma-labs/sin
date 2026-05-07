@extends('layouts.app')

@section('title', $transcript->title)

@section('content')
    <div class="container-lg my-4">
        <div class="bg-white rounded shadow-sm p-5">
            <div class="mb-5">
                <h1 class="text-primary mb-4 pb-3 border-bottom border-2">{{ $transcript->title }}</h1>

                <div class="mb-4 d-flex gap-2 flex-wrap">
                    <span class="badge bg-primary fs-6">{{ $transcript->code }}</span>
                    @if($transcript->recorded_at)
                        <span class="badge bg-secondary fs-6">{{ $transcript->recorded_at->format('d M Y') }}</span>
                    @endif
                </div>

                @if($transcript->description)
                    <div class="bg-light border-start border-5 border-primary ps-4 py-3 mb-5">
                        <p class="mb-0 text-dark fw-500 lh-lg">
                            <em>{{ $transcript->description }}</em>
                        </p>
                    </div>
                @endif

                <hr class="my-5">

                @if($transcript->content)
                    <div class="text-dark lh-lg" style="font-size: 1.05rem;">
                        {!! nl2br(e($transcript->content)) !!}
                    </div>
                @else
                    <p class="text-muted">No content available.</p>
                @endif
            </div>
        </div>
    </div>
@endsection
