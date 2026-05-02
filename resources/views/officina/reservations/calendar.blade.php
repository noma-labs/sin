@extends("officina.index")

@section("title", "Prenotazioni Calendario")

@section("content")
    <div class="d-flex align-items-center justify-content-between my-1">
        <h5 class="mb-0">{{ $date->isoFormat("dddd D MMMM YYYY") }}</h5>
        <form
            method="GET"
            action="{{ route("officina.calendario") }}"
            class="d-flex align-items-center gap-2"
        >
            <input
                type="date"
                name="date"
                class="form-control form-control-sm"
                value="{{ $date->toDateString() }}"
            />
            <button type="submit" class="btn btn-sm btn-outline-secondary">
                Vai
            </button>
            <a
                href="{{ route("officina.calendario") }}"
                class="btn btn-sm btn-outline-primary"
            >
                Oggi
            </a>
        </form>
    </div>
    <div class="card">
        <div class="card-body p-0">
            <div
                class="table-responsive"
                style="height: calc(100vh - 130px); overflow-y: auto"
            >
                <table class="table table-sm table-bordered mb-0">
                    <thead
                        style="
                            position: sticky;
                            top: 0;
                            z-index: 20;
                            background: white;
                        "
                    >
                        <tr class="table-warning">
                            <th scope="col" class="text-nowrap">Ora</th>
                            @foreach ($vehicles as $vehicle)
                                <th scope="col" class="text-center text-nowrap">
                                    {{ $vehicle->nome }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $currentHour = $now->hour;
                            $currentMinute = $now->minute;
                        @endphp

                        @for ($hour = 0; $hour < 24; $hour++)
                            <tr
                                class="border-bottom"
                                @if ($hour === $currentHour) id="current-hour-row" @endif
                            >
                                <td
                                    class="text-muted fw-semibold align-top text-nowrap small"
                                >
                                    {{ str_pad($hour, 2, "0", STR_PAD_LEFT) }}:00
                                </td>
                                @foreach ($vehicles as $vehicle)
                                    <td
                                        class="align-top"
                                        style="
                                            background-color: {{ $loop->even ? "#f8f9fa" : "#ffffff" }};
                                            position: relative;
                                        "
                                    >
                                        @if ($hour === $currentHour)
                                            <div
                                                style="
                                                    position: absolute;
                                                    left: 0;
                                                    right: 0;
                                                    height: 2px;
                                                    background: #dc3545;
                                                    z-index: 10;
                                                    top: {{ round(($currentMinute / 60) * 100) }}%;
                                                "
                                            ></div>
                                        @endif

                                        @foreach ($reservationsByVehicle[$vehicle->id][$hour] ?? [] as $pren)
                                            <div
                                                class="text-white rounded mb-0 p-1"
                                                style="
                                                    background-color: {{ $reservationColors[$pren->id] }};
                                                "
                                            >
                                                <div class="fw-semibold">
                                                    {{ $pren->cliente->nominativo }}
                                                </div>
                                                <div class="opacity-75">
                                                    {{ substr($pren->ora_partenza, 0, 5) }}-{{ substr($pren->ora_arrivo, 0, 5) }}
                                                </div>
                                            </div>
                                        @endforeach
                                    </td>
                                @endforeach
                            </tr>
                        @endfor
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        const row = document.getElementById('current-hour-row');
        if (row) {
            row.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }

        setTimeout(() => location.reload(), 1000 * 60);
    </script>
@endsection
