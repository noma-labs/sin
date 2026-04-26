@extends("officina.index")

@section("title", "Prenotazioni Calendario")

@section("content")
    <div class="d-flex align-items-center justify-content-between my-3">
        <h5 class="mb-0">{{ $now->isoFormat("dddd D MMMM YYYY") }}</h5>
        <span class="text-muted">{{ $now->format("H:i") }}</span>
    </div>
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-bordered mb-0">
                    <thead>
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

                        @for ($hour = 6; $hour < 22; $hour++)
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
    </script>
@endsection
