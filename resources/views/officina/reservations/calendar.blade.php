@extends("officina.index")

@section("title", "Prenotazioni Calendario")

@section("content")
    @php
        $colorMap = [
            'primary' => '#0d6efd',
            'success' => '#198754',
            'danger' => '#dc3545',
            'warning' => '#ffc107',
            'info' => '#0dcaf0',
            'secondary' => '#6c757d',
        ];
    @endphp

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm mb-0" style="font-size: 0.85rem">
                    <thead>
                        <tr class="table-warning">
                            <th scope="col" style="width: 60px;">Ora</th>
                            @foreach ($vehicles as $vehicle)
                                <th scope="col" class="text-center">
                                    <small>{{ $vehicle->nome }}</small>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @php $currentHour = now()->hour; $currentMinute = now()->minute; @endphp
                        @for ($hour = 6; $hour < 22; $hour++)
                            <tr class="border-bottom" @if($hour === $currentHour) id="current-hour-row" @endif>
                                <td class="text-muted fw-semibold align-top">{{ str_pad($hour, 2, '0', STR_PAD_LEFT) }}:00</td>
                                @foreach ($vehicles as $vehicle)
                                    <td class="align-top p-2" style="min-height: 60px; vertical-align: top; position: relative;">
                                        @if ($hour === $currentHour)
                                            {{-- Current time marker line, positioned by JS --}}
                                            <div class="current-time-line" style="position: absolute; left: 0; right: 0; height: 2px; background: #dc3545; z-index: 10; top: {{ round(($currentMinute / 60) * 100) }}%;"></div>
                                        @endif
                                        @foreach ($reservations[$vehicle->id][$hour] ?? [] as $pren)
                                            <div class="text-white p-2 rounded mb-1" style="font-size: 0.75rem; background-color: {{ $colorMap[$clientColors[$pren->cliente_id]] }};">
                                                <div class="fw-semibold mb-1">{{ $pren->cliente->nominativo }}</div>
                                                <div class="mb-1">
                                                    {{ $pren->ora_partenza }} - {{ $pren->ora_arrivo }}
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
        // Scroll the page so the current hour row is near the top on load
        const row = document.getElementById('current-hour-row');
        if (row) {
            row.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    </script>
@endsection
