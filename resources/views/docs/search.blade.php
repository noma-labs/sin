@extends('layouts.app')

@section('title', 'Search Transcripts')

@section('content')
    <div class="container-fluid my-4">
        <h1 class="mb-4">Search Transcripts</h1>

        <div class="row" style="height: calc(100vh - 200px);">
            <!-- Master Panel (Left) -->
            <div class="col-md-5 border-end">
                <form method="GET" action="{{ route('docs.search') }}" class="mb-4">
                    <div class="input-group">
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
                        <div style="max-height: calc(100vh - 300px); overflow-y: auto;">
                            @foreach ($results as $transcript)
                                <a
                                    href="{{ route('docs.search', ['q' => $term, 'selected' => $transcript->id]) }}"
                                    class="card mb-2 border text-decoration-none {{ request('selected') == $transcript->id ? 'border-primary bg-light' : '' }}"
                                    style="cursor: pointer; transition: all 0.2s;"
                                >
                                    <div class="card-body py-2">
                                        <div class="d-flex justify-content-between align-items-start mb-1">
                                            <h6 class="card-title mb-0 text-dark">{{ $transcript->title }}</h6>
                                            <span class="badge bg-success">{{ number_format($transcript->relevance, 2) }}</span>
                                        </div>
                                        <span class="badge bg-secondary">{{ $transcript->code }}</span>
                                        <p class="card-text small text-muted mt-2 mb-0">
                                            {{ Str::limit($transcript->description, 80) }}
                                        </p>
                                    </div>
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

            <!-- Detail Panel (Right) -->
            <div class="col-md-7 ps-4">
                @if (!empty($term) && request('selected'))
                    @php
                        $selected = $results->firstWhere('id', request('selected'));
                    @endphp

                    @if ($selected)
                        <div style="max-height: calc(100vh - 200px); overflow-y: auto;">
                            <div class="mb-3">
                                <span class="badge bg-primary mb-2">{{ $selected->code }}</span>
                            </div>

                            <h2 class="mb-3">{{ $selected->title }}</h2>

                            @if($selected->recorded_at)
                                <div class="text-muted mb-3">
                                    <small>Recorded: <strong>{{ $selected->recorded_at->format('d M Y') }}</strong></small>
                                </div>
                            @endif

                            @if($selected->description)
                                <blockquote class="blockquote mb-4 ps-3 border-start border-4">
                                    <p class="mb-0">{{ $selected->description }}</p>
                                </blockquote>
                            @endif

                            @if($selected->description)
                                <hr class="my-4">
                            @endif

                            @if($selected->content)
                                <div class="content-body" style="line-height: 1.8; color: #333;">
                                    @foreach(explode("\n", $selected->content) as $line)
                                        <p class="mb-2">{{ $line }}</p>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted">No content available.</p>
                            @endif
                        </div>
                    @endif
                @else
                    <div class="text-center text-muted mt-5">
                        <p>Select a transcript to view details</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <style>
        a.card:hover {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        blockquote {
            border-left: 4px solid #0d6efd;
            padding-left: 1rem;
            color: #6c757d;
            font-style: italic;
        }
    </style>
@endsection
