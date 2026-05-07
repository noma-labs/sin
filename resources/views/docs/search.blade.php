@extends('layouts.app')

@section('title', 'Search Transcripts')

@section('content')
    <div class="container my-5">
        <div class="row mb-4">
            <div class="col-md-8 mx-auto">
                <h1>Search Transcripts</h1>

                <form method="GET" action="{{ route('docs.search') }}" class="mb-4">
                    <div class="input-group input-group-lg">
                        <input
                            type="text"
                            name="q"
                            class="form-control"
                            placeholder="Enter search term..."
                            value="{{ $term }}"
                            autocomplete="off"
                        >
                        <button class="btn btn-primary" type="submit">Search</button>
                    </div>
                </form>

                @if (!empty($term))
                    <div class="alert alert-info">
                        Found <strong>{{ count($results) }}</strong> result(s) for "<strong>{{ $term }}</strong>"
                    </div>

                    @if (count($results) > 0)
                        <div class="list-group">
                            @foreach ($results as $transcript)
                                <a
                                    href="{{ route('docs.preview', ['id' => $transcript->id]) }}"
                                    class="list-group-item list-group-item-action"
                                >
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h5 class="mb-0">{{ $transcript->title }}</h5>
                                        <span class="badge bg-success">{{ number_format($transcript->relevance, 2) }}</span>
                                    </div>
                                    <span class="badge bg-secondary me-2">{{ $transcript->code }}</span>
                                    <p class="mb-1 text-muted mt-2">
                                        {{ Str::limit($transcript->description, 100) }}
                                    </p>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-warning">
                            No transcripts found matching your search.
                        </div>
                    @endif
                @else
                    <div class="alert alert-secondary">
                        Enter a search term to find transcripts by content.
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
