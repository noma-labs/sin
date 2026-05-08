@extends("layouts.blank")

@section("title", "Prenotazioni Calendario")

@section("content")
    <div class="d-flex align-items-center justify-content-between my-3">
        <h3 class="fw-bold mb-0">
            {{ $date->isoFormat("dddd D MMMM YYYY") }}
            <span>{{ $now->format("H:i") }}</span>
        </h3>
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
    <div
        class="table-responsive"
        style="height: calc(100vh - 100px); overflow-y: auto"
    >
        <table class="table table-bordered table-striped-columns">
            <thead class="sticky-top">
                <tr class="table-primary">
                    <th
                        scope="col"
                        class="text-nowrap text-white text-uppercase fw-bold fs-5 py-3"
                    >
                        Ora
                    </th>
                    @foreach ($vehicles as $vehicle)
                        <th
                            scope="col"
                            class="text-center text-nowrap text-uppercase fw-bold fs-5 py-3"
                        >
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
                        style="height: 45px"
                        @if ($hour === $currentHour) id="current-hour-row" @endif
                    >
                        <td
                            class="text-muted fw-semibold align-top text-nowrap small"
                            style="position: relative"
                        >
                            {{ str_pad($hour, 2, "0", STR_PAD_LEFT) }}:00
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
                                >
                                    <span
                                        style="
                                            position: absolute;
                                            right: 0;
                                            top: 2px;
                                            font-size: 0.7rem;
                                            color: #dc3545;
                                            line-height: 1;
                                            font-weight: 600;
                                        "
                                    >
                                        {{ $now->format("H:i") }}
                                    </span>
                                </div>
                            @endif
                        </td>
                        @foreach ($vehicles as $vehicle)
                            <td class="align-top" style="position: relative">
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
                                    @php
                                        $info = $multiDayInfo[$pren->id];
                                        // Only show if it starts today or ends today
                                        if (! ($info["startsToday"] || $info["endsToday"])) {
                                            continue;
                                        }
                                    @endphp

                                    @php
                                        $sParts = explode(":", $pren->ora_partenza);
                                        $eParts = explode(":", $pren->ora_arrivo);
                                        $sMin = (int) ($sParts[1] ?? 0);
                                        $sH = (int) $sParts[0];
                                        $eMin = (int) ($eParts[1] ?? 0);
                                        $eH = (int) $eParts[0];

                                        // If multi-day and starts today, extend to 24:00
                                        if ($info["isMultiDay"] && $info["startsToday"] && ! $info["endsToday"]) {
                                            $eH = 24;
                                            $eMin = 0;
                                        }
                                        // If multi-day and doesn't start today, show from 00:00
                                        elseif ($info["isMultiDay"] && ! $info["startsToday"]) {
                                            $sH = 0;
                                            $sMin = 0;
                                            // If ends today, use arrival time; otherwise 24:00
                                            if (! $info["endsToday"]) {
                                                $eH = 24;
                                                $eMin = 0;
                                            }
                                        }

                                        $durationMin = ($eH - $sH) * 60 + ($eMin - $sMin);
                                        $rowHeight = 45;
                                        $boxHeight = max(20, ($durationMin / 60) * $rowHeight);
                                        // Add overflow for bookings continuing past midnight
                                        if ($info["isMultiDay"] && $info["startsToday"] && ! $info["endsToday"]) {
                                            $boxHeight += 6;
                                        }
                                        $boxTop = ($sMin / 60) * $rowHeight;
                                    @endphp

                                    <div
                                        class="text-white rounded p-1"
                                        style="
                                            background-color: {{ $reservationColors[$pren->id] }};
                                            position: absolute;
                                            top: {{ $boxTop }}px;
                                            height: {{ $boxHeight }}px;
                                            left: 2px;
                                            right: 2px;
                                            z-index: 5;
                                            overflow: hidden;
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
    <script>
        const row = document.getElementById('current-hour-row');
        if (row) {
            row.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }

        setTimeout(() => location.reload(), 1000 * 60);
    </script>
@endsection
