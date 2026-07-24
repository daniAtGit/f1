<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('layouts.head.head')
    </head>
    <body class="antialiased bg-light">
        @if (Route::has('login'))
            <div class="position-absolute top-0 end-0 p-3 p-md-4 d-flex gap-2">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-dark">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-sm btn-dark">Login</a>
                @endauth
            </div>
        @endif
        <div class="position-absolute top-0 start-50 translate-middle-x p-3 p-md-4" style="z-index:20;">
            <a href="{{ route('welcome') }}">
                <x-application-logo class="h-10 w-auto fill-current text-dark" />
            </a>
        </div>

        <br>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-4">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="d-flex justify-content-end mb-2">
                        <div class="input-group w-auto">
                            <span class="bg-white input-group-text text-decoration-none">Driver</span>
                            <select class="form-select" name="changeDriver" id="changeDriver" aria-label="changeDriver">
                                @foreach($drivers as $driverOption)
                                    <option value="{{ $driverOption->id }}" @selected($driverOption->id === $driver->id)>{{ $driverOption->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-4">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-3 text-gray-900">
                            <div class="row">
                                <div class="col-8">
                                    @if($driverImageUrl)
                                        <img
                                            src="{{ $driverImageUrl }}"
                                            alt="{{ $driver->name }}"
                                            class="img-fluid"
                                            style="max-height:180px;object-fit:contain;"
                                            loading="lazy"
                                        >
                                    @else
                                        <div class="text-muted fst-italic">Foto non disponibile</div>
                                    @endif
                                </div>
                                <div class="col-1">
                                    <p style="font-style:italic;color:#c1c1c1;font-size:10px;">
                                        <i class="fa-solid fa-bars-staggered" style="margin-left:2px;margin-right:1px;"></i>
                                        Pos
                                    </p>
                                    <span style="font-size:30px">{{ $editionPosition }}</span>
                                </div>
                                <div class="col-1">
                                    <p style="font-style:italic;color:#c1c1c1;font-size:10px;">
                                        <i class="fa-solid fa-cubes" style="margin-left:2px;margin-right:1px;"></i>
                                        Point
                                    </p>
                                    <span style="font-size:30px">{{ $editionPoints }}</span>
                                </div>
                                <div class="col-1">
                                    <p style="font-style:italic;color:#c1c1c1;font-size:10px;">
                                        <i class="fa-solid fa-car-side" style="margin-left:2px;margin-right:1px;"></i>
                                        num
                                    </p>
                                    <div style="width:50px;height:50px;line-height:50px;text-align:center;border:1px solid #ccc;font-size:40px;font-style:italic;padding-left:3px;padding-right:5px;">
                                        {{ $driverNumber }}
                                    </div>
                                </div>
                                <div class="col-1"></div>
                            </div>
                        </div>
                    </div>
                </div>

                @php
                    $history = collect($driverStandingsHistory ?? []);
                    $chartWidth = 360;
                    $chartHeight = 150;
                    $paddingLeft = 20;
                    $paddingRight = 12;
                    $paddingTop = 12;
                    $paddingBottom = 24;
                    $plotWidth = $chartWidth - $paddingLeft - $paddingRight;
                    $plotHeight = $chartHeight - $paddingTop - $paddingBottom;
                    $maxPosition = max((int) $history->max('position'), 1);
                    $countHistory = $history->count();
                    $yTicks = $maxPosition <= 6
                        ? range(1, $maxPosition)
                        : array_values(array_unique([1, (int) ceil($maxPosition / 2), $maxPosition]));

                    $chartPoints = $history->values()->map(function ($item, $index) use ($countHistory, $paddingLeft, $paddingTop, $plotWidth, $plotHeight, $maxPosition) {
                        $x = $countHistory > 1
                            ? $paddingLeft + ($plotWidth * $index / ($countHistory - 1))
                            : $paddingLeft + ($plotWidth / 2);
                        $y = $paddingTop + (($item['position'] - 1) * $plotHeight / max($maxPosition - 1, 1));

                        return [
                            'x' => round($x, 2),
                            'y' => round($y, 2),
                            'year' => $item['year'],
                            'position' => $item['position'],
                            'points' => $item['points'],
                        ];
                    });
                @endphp

                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-4">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-3 text-gray-900">
                            <p class="mb-2" style="font-style:italic;color:#c1c1c1;font-size:10px;">
                                <i class="fa fa-chart-line"></i> Placement over the Years
                            </p>

                            <div class="d-flex justify-content-center">
                            @if($chartPoints->isNotEmpty())
                                <svg viewBox="0 0 {{ $chartWidth }} {{ $chartHeight }}" role="img" aria-label="Storico classifica finale del pilota" style="width:80%;max-width:80%;max-height:170px;">
                                    <line x1="{{ $paddingLeft }}" y1="{{ $paddingTop }}" x2="{{ $paddingLeft }}" y2="{{ $paddingTop + $plotHeight }}" stroke="#d7d7d7" stroke-width="1" />
                                    <line x1="{{ $paddingLeft }}" y1="{{ $paddingTop + $plotHeight }}" x2="{{ $paddingLeft + $plotWidth }}" y2="{{ $paddingTop + $plotHeight }}" stroke="#d7d7d7" stroke-width="1" />

                                    @foreach($yTicks as $tick)
                                        @php
                                            $tickY = $paddingTop + (($tick - 1) * $plotHeight / max($maxPosition - 1, 1));
                                        @endphp
                                        <line x1="{{ $paddingLeft }}" y1="{{ $tickY }}" x2="{{ $paddingLeft + $plotWidth }}" y2="{{ $tickY }}" stroke="#f1f1f1" stroke-width="1" />
                                        <text x="{{ $paddingLeft - 4 }}" y="{{ $tickY + 4 }}" text-anchor="end" font-size="9" fill="#777">{{ $tick }}</text>
                                    @endforeach

                                    <polyline
                                        fill="none"
                                        stroke="#0d6efd"
                                        stroke-width="2.5"
                                        points="{{ $chartPoints->map(fn ($point) => $point['x'].','.$point['y'])->implode(' ') }}"
                                    />

                                    @foreach($chartPoints as $point)
                                        <circle cx="{{ $point['x'] }}" cy="{{ $point['y'] }}" r="4" fill="#0d6efd" />
                                        @php
                                            $isTopLabel = $point['y'] <= ($paddingTop + 12);
                                            $labelY = $isTopLabel ? $point['y'] + 20 : $point['y'] - 10;
                                        @endphp
                                        <text
                                            x="{{ $point['x'] }}"
                                            y="{{ $labelY }}"
                                            text-anchor="middle"
                                            font-size="10"
                                            fill="#0f172a"
                                        >P{{ $point['position'] }}</text>
                                        <text x="{{ $point['x'] }}" y="{{ $paddingTop + $plotHeight + 15 }}" text-anchor="middle" font-size="10" fill="#777">{{ $point['year'] }}</text>
                                    @endforeach
                                </svg>
                            @else
                                <div class="text-muted small">Nessuna classifica finale disponibile.</div>
                            @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-4">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-3 text-gray-900">
                            <div class="row">
                                <div class="col-4">
{{--                                    <div style="width:50px;height:50px;line-height:50px;text-align:center;border:1px solid #ccc;font-size:40px;font-style:italic;padding-left:3px;padding-right:5px;">--}}
{{--                                        {{ $driverNumber }}--}}
{{--                                    </div>--}}
                                </div>
                                <div class="col-2">
                                    <p style="font-style:italic;color:#c1c1c1;font-size:10px;">
                                        <i class="fa-solid fa-trophy" style="margin-left:2px;margin-right:1px;"></i>
                                        Titles
                                    </p>
                                    <span style="font-size:30px">{{ $championshipCount }}</span>
                                </div>
                                <div class="col-2">
                                    <p style="font-style:italic;color:#c1c1c1;font-size:10px;">
                                        <i class="fa-solid fa-grip-vertical" style="margin-left:2px;margin-right:1px;"></i>
                                        Pole
                                    </p>
                                    <span style="font-size:30px">{{ $poleCount }}</span>
                                </div>
                                <div class="col-2">
                                    <p style="font-style:italic;color:#c1c1c1;font-size:10px;">
                                        <i class="fa-solid fa-flag-checkered"></i>
                                        Race
                                    </p>
                                    <span style="font-size:30px">{{ $raceCount }}</span>
                                </div>
                                <div class="col-2">
                                    <p style="font-style:italic;color:#c1c1c1;font-size:10px;">
                                        <i class="fa-regular fa-flag"></i>
                                        Sprint
                                    </p>
                                    <span style="font-size:30px">{{ $sprintCount }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-2">
                <div class="overflow-hidden">
                    <div class="d-flex justify-content-end">
                        <div class="input-group w-auto">
                                <span class="bg-white input-group-text text-decoration-none">Edition</span>
                                <select class="form-select" name="changeEdition" id="changeEdition" aria-label="changeEdition" aria-describedby="editionLink">
                                    @foreach($editions as $editionOption)
                                        <option value="{{$editionOption->id}}" @selected($editionOption->id === $edition?->id)>{{$editionOption->edition}} - {{$editionOption->year}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                @php
                    $editionRaceChart = collect($editionRacePlacements ?? []);
                    $raceChartWidth = 720;
                    $raceChartHeight = 210;
                    $racePaddingLeft = 24;
                    $racePaddingRight = 16;
                    $racePaddingTop = 16;
                    $racePaddingBottom = 48;
                    $racePlotWidth = $raceChartWidth - $racePaddingLeft - $racePaddingRight;
                    $racePlotHeight = $raceChartHeight - $racePaddingTop - $racePaddingBottom;
                    $raceMaxPosition = max((int) $editionRaceChart->max('position'), 1);
                    $raceCount = $editionRaceChart->count();
                    $raceYTicks = $raceMaxPosition <= 8
                        ? range(1, $raceMaxPosition)
                        : array_values(array_unique([1, (int) ceil($raceMaxPosition / 2), $raceMaxPosition]));

                    $raceChartPoints = $editionRaceChart->values()->map(function ($item, $index) use ($raceCount, $racePaddingLeft, $racePaddingTop, $racePlotWidth, $racePlotHeight, $raceMaxPosition) {
                        $x = $raceCount > 1
                            ? $racePaddingLeft + ($racePlotWidth * $index / ($raceCount - 1))
                            : $racePaddingLeft + ($racePlotWidth / 2);
                        $y = $racePaddingTop + (($item['position'] - 1) * $racePlotHeight / max($raceMaxPosition - 1, 1));

                        return [
                            'x' => round($x, 2),
                            'y' => round($y, 2),
                            'position' => $item['position'],
                            'flagIconUrl' => $item['flagIconUrl'],
                            'circuitName' => $item['circuitName'],
                            'countryName' => $item['countryName'],
                            'round' => $item['round'],
                        ];
                    });
                @endphp

                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-2">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-3 text-gray-900">
                            <p class="mb-2" style="font-style:italic;color:#c1c1c1;font-size:10px;">
                                <i class="fa fa-chart-line"></i> Race placements
                            </p>

                            @if($raceChartPoints->isNotEmpty())
                                <div class="d-flex justify-content-center">
                                    <svg viewBox="0 0 {{ $raceChartWidth }} {{ $raceChartHeight }}" role="img" aria-label="Piazzamenti gara del pilota nell'edizione selezionata" style="width:80%;max-width:80%;max-height:230px;">
                                        <line x1="{{ $racePaddingLeft }}" y1="{{ $racePaddingTop }}" x2="{{ $racePaddingLeft }}" y2="{{ $racePaddingTop + $racePlotHeight }}" stroke="#d7d7d7" stroke-width="1" />
                                        <line x1="{{ $racePaddingLeft }}" y1="{{ $racePaddingTop + $racePlotHeight }}" x2="{{ $racePaddingLeft + $racePlotWidth }}" y2="{{ $racePaddingTop + $racePlotHeight }}" stroke="#d7d7d7" stroke-width="1" />

                                        @foreach($raceYTicks as $tick)
                                            @php
                                                $tickY = $racePaddingTop + (($tick - 1) * $racePlotHeight / max($raceMaxPosition - 1, 1));
                                            @endphp
                                            <line x1="{{ $racePaddingLeft }}" y1="{{ $tickY }}" x2="{{ $racePaddingLeft + $racePlotWidth }}" y2="{{ $tickY }}" stroke="#f1f1f1" stroke-width="1" />
                                            <text x="{{ $racePaddingLeft - 4 }}" y="{{ $tickY + 4 }}" text-anchor="end" font-size="9" fill="#777">{{ $tick }}</text>
                                        @endforeach

                                        <polyline
                                            fill="none"
                                            stroke="{{ $editionRaceLineColor }}"
                                            stroke-width="2.5"
                                            points="{{ $raceChartPoints->map(fn ($point) => $point['x'].','.$point['y'])->implode(' ') }}"
                                        />

                                        @foreach($raceChartPoints as $point)
                                            @php
                                                $raceLabelY = $point['y'] <= ($racePaddingTop + 12) ? $point['y'] + 18 : $point['y'] - 10;
                                            @endphp
                                            <circle cx="{{ $point['x'] }}" cy="{{ $point['y'] }}" r="4" fill="{{ $editionRaceLineColor }}" />
                                            <text x="{{ $point['x'] }}" y="{{ $raceLabelY }}" text-anchor="middle" font-size="10" fill="#0f172a">P{{ $point['position'] }}</text>

                                            @if($point['flagIconUrl'])
                                                <image
                                                    href="{{ $point['flagIconUrl'] }}"
                                                    x="{{ $point['x'] - 8 }}"
                                                    y="{{ $racePaddingTop + $racePlotHeight + 12 }}"
                                                    width="16"
                                                    height="12"
                                                    preserveAspectRatio="none"
                                                >
                                                    <title>{{ $point['circuitName'] }} - {{ $point['countryName'] }}</title>
                                                </image>
                                            @else
                                                <text x="{{ $point['x'] }}" y="{{ $racePaddingTop + $racePlotHeight + 22 }}" text-anchor="middle" font-size="9" fill="#777">R{{ $point['round'] }}</text>
                                            @endif
                                        @endforeach
                                    </svg>
                                </div>
                            @else
                                <div class="text-muted small">Nessun risultato gara disponibile per questa edizione.</div>
                            @endif

                            <div class="d-flex justify-content-end mt-3">
                                <a
                                    href="{{ route('driver.stats', ['driver' => $driver->id, 'edition' => $edition?->id]) }}"
                                    class="btn btn-sm btn-outline-dark"
                                >
                                    Compare
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-2">
                    <div class="row g-3">
                        <div class="col-12 col-lg-12">
                            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg h-100">
                                <div class="p-3 text-gray-900">
                                    @forelse($resultsByYear as $year => $circuits)
                                        <div class="mb-4">
                                            <div class="fw-bold mb-2">
                                                {{ $year }}
                                                @if($editionPosition !== null)
                                                    <span class="text-muted fw-normal ms-2">Pos. {{ $editionPosition }}</span>
                                                @endif
                                                <span class="text-muted fw-normal ms-2">Pts. {{ $editionPoints }}</span>
                                            </div>

                                            @foreach($circuits as $circuit)
                                                <div class="border-bottom pb-2 mb-2">
                                                    <div class="small text-muted">
                                                        Round {{ $circuit['round'] }}
                                                        @if($circuit['countryFlagIconUrl'])
                                                            <img
                                                                src="{{ $circuit['countryFlagIconUrl'] }}"
                                                                alt="{{ $circuit['countryName'] }} flag"
                                                                title="{{ $circuit['countryName'] }}"
                                                                style="display:inline-block;width:16px;height:12px;object-fit:cover;margin:0 4px 2px 6px;"
                                                            >
                                                        @endif
                                                        <a href="{{route('circuit.single', $circuit['circuitId'])}}">{{ $circuit['countryName'] }} {{ $circuit['city'] }} {{ $circuit['circuitName'] }}</a>
                                                    </div>

                                                    @foreach($circuit['sessions'] as $session)
                                                        <div class="ms-2">
                                                            @if($session['type'] == 'grid')
                                                                <i class="fa-solid fa-grip-vertical" style="margin-left:2px;margin-right:1px;" title="{{strtoupper($session['type'])}}"></i>
                                                            @elseif($session['type'] == 'race')
                                                                <i class="fa-solid fa-flag-checkered" title="{{strtoupper($session['type'])}}"></i>
                                                            @elseif($session['type'] == 'sprint')
                                                                <i class="fa-regular fa-flag" title="{{strtoupper($session['type'])}}"></i>
                                                            @endif
                                                            |
                                                            @foreach($session['results'] as $result)
                                                                <span style="width:30px;height:30px;line-height:30px;padding:0 3px;text-align:center;border:1px solid #ccc;">{{ $result['number'] }}</span> Pos. <b>{{ $result['position'] }}</b> <a href="{{route('team.single', $result['teamId'])}}"><badge class="badge" style="background:{{$result['teamColor']}};">{{ $result['teamName'] }}</badge></a>
                                                                <br>
                                                            @endforeach
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endforeach
                                        </div>
                                    @empty
                                        <div class="text-muted">Nessun risultato disponibile.</div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            const driverUrlTemplate = @json(route('driver.single', ['driver' => '__DRIVER__']));

            document.getElementById('changeDriver')?.addEventListener('change', function (event) {
                const url = new URL(driverUrlTemplate.replace('__DRIVER__', event.target.value), window.location.origin);
                const edition = new URL(window.location.href).searchParams.get('edition');

                if (edition) {
                    url.searchParams.set('edition', edition);
                }

                window.location.href = url.toString();
            });

            document.getElementById('changeEdition')?.addEventListener('change', function (event) {
                const url = new URL(window.location.href);
                url.searchParams.set('edition', event.target.value);
                window.location.href = url.toString();
            });
        </script>

        @include('layouts.footer.footer')
    </body>
</html>
