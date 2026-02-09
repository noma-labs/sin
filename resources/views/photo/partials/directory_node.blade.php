@php($hasChildren = isset($node["children"]) && count($node["children"]) > 0)
@php($count = isset($node["total"]) ? (int) $node["total"] : (isset($node["photos"]) ? count($node["photos"]) : 0))
@php($parentAccordionId = $parentAccordionId ?? "photosGroupedAccordion")
@if ($prefix === "")
    @php($sortedChildren = collect($node["children"])->sortBy(function ($child) {return is_string($child["label"] ?? null) ? $child["label"] : "";})->all())
    @foreach ($sortedChildren as $childLabel => $child)
        @include(
            "photo.partials.directory_node",
            [
                "node" => $child,
                "prefix" => (string) ($child["label"] ?? (string) $childLabel),
                "currentView" => $currentView,
            ]
        )
    @endforeach
@else
    @php($id = "dir-" . md5($prefix))
    <div class="accordion-item">
        <h2 class="accordion-header" id="heading-{{ $id }}">
            <button
                class="accordion-button collapsed"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#{{ $id }}"
                aria-expanded="false"
                aria-controls="{{ $id }}"
            >
                @php($segments = array_values(array_filter(explode("/", (string) $prefix), function ($s) {return $s !== "";}),))
                @php($breadcrumb = count($segments) ? implode(" / ", $segments) : $node["label"] ?? "")
                {{ $breadcrumb }}
                <span class="badge text-bg-secondary ms-2">{{ $count }}</span>
            </button>
        </h2>
        <div
            id="{{ $id }}"
            class="accordion-collapse collapse"
            aria-labelledby="heading-{{ $id }}"
            data-bs-parent="#{{ $parentAccordionId }}"
        >
            <div class="accordion-body">
                @if ($hasChildren)
                    <div class="accordion" id="accordion-{{ $id }}">
                        @php($sortedChildren = collect($node["children"])->sortBy(function ($child) {return is_string($child["label"] ?? null) ? $child["label"] : "";})->all())
                        @foreach ($sortedChildren as $childLabel => $child)
                            @include(
                                "photo.partials.directory_node",
                                [
                                    "node" => $child,
                                    "prefix" =>
                                        $prefix . "/" . (string) ($child["label"] ?? (string) $childLabel),
                                    "currentView" => $currentView,
                                    "parentAccordionId" => "accordion-" . $id,
                                ]
                            )
                        @endforeach
                    </div>
                @endif

                @if ($count > 0)
                    @if ($currentView === "grid")
                        <div class="d-flex flex-wrap">
                            @foreach ($node["photos"] as $photo)
                                <a
                                    href="{{ route("photos.show", $photo->id) }}"
                                >
                                    <figure
                                        class="figure m-1"
                                        style="width: 25rem"
                                    >
                                        <div class="position-relative">
                                            <img
                                                src="{{ route("photos.preview", $photo->id) }}"
                                                class="figure-img img-fluid rounded"
                                                alt="{{ $photo->description }}"
                                            />
                                            <div
                                                class="position-absolute bottom-0 start-0 w-100 p-2 bg-dark bg-opacity-50 text-white"
                                            >
                                                <span class="small">
                                                    {{ $photo->file_name ?? "" }}
                                                </span>
                                                <span class="small">
                                                    {{ $photo->taken_at ? $photo->taken_at->format("d/m/Y") : "N/A" }}
                                                </span>

                                                @if ($photo->strip)
                                                    <span
                                                        class="badge text-bg-success"
                                                    >
                                                        {{ $photo->strip->datnum }}
                                                    </span>
                                                @else
                                                    <span
                                                        class="badge text-bg-danger"
                                                    >
                                                        Senza Striscia
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </figure>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <ul class="list-group">
                            @foreach ($node["photos"] as $photo)
                                <li
                                    class="list-group-item d-flex align-items-center"
                                >
                                    <img
                                        src="{{ route("photos.preview", $photo->id) }}"
                                        alt="{{ $photo->description }}"
                                        class="me-3 rounded"
                                        style="width: 80px; height: auto"
                                    />
                                    <div class="flex-grow-1">
                                        <div class="fw-semibold">
                                            {{ $photo->file_name ?? "—" }}
                                        </div>
                                        <div class="text-muted small">
                                            {{ $photo->taken_at ? $photo->taken_at->format("d/m/Y") : "N/A" }}
                                        </div>
                                        @if ($photo->strip)
                                            <div class="mt-1">
                                                <span
                                                    class="badge text-bg-success"
                                                >
                                                    {{ $photo->strip->datnum }}
                                                </span>
                                            </div>
                                        @endif

                                        @if ($photo->subjects)
                                            <div class="small">
                                                {{ $photo->subjects }}
                                            </div>
                                        @endif
                                    </div>
                                    <a
                                        href="{{ route("photos.show", $photo->id) }}"
                                        class="btn btn-sm btn-outline-primary"
                                    >
                                        Apri
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                @endif
            </div>
        </div>
    </div>
@endif
