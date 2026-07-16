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

        @php
            $chartWidth = 960;
            $chartHeight = 320;
            $paddingLeft = 42;
            $paddingRight = 24;
            $paddingTop = 20;
            $paddingBottom = 62;
            $plotWidth = $chartWidth - $paddingLeft - $paddingRight;
            $plotHeight = $chartHeight - $paddingTop - $paddingBottom;
            $roundCount = $chartRounds->count();
            $yTicks = $chartMaxPosition <= 8
                ? range(1, $chartMaxPosition)
                : array_values(array_unique([1, (int) ceil($chartMaxPosition / 2), $chartMaxPosition]));
            $linePatterns = ['', '10 6', '4 4', '12 4 3 4', '2 5', '14 6 2 6'];
            $pointShapes = ['circle', 'square', 'diamond', 'triangle', 'cross', 'circle'];

            $preparedSeries = collect($chartSeries)->values()->map(function ($series, $seriesIndex) use ($chartRounds, $roundCount, $paddingLeft, $paddingTop, $plotWidth, $plotHeight, $chartMaxPosition, $linePatterns, $pointShapes) {
                $points = collect($series['placements'])
                    ->map(function ($placement) use ($chartRounds, $roundCount, $paddingLeft, $paddingTop, $plotWidth, $plotHeight, $chartMaxPosition) {
                        $roundIndex = $chartRounds->search(fn ($round) => $round['round'] === $placement['round']);

                        if ($roundIndex === false) {
                            return null;
                        }

                        $x = $roundCount > 1
                            ? $paddingLeft + ($plotWidth * $roundIndex / ($roundCount - 1))
                            : $paddingLeft + ($plotWidth / 2);
                        $y = $paddingTop + (($placement['position'] - 1) * $plotHeight / max($chartMaxPosition - 1, 1));

                        return [
                            'x' => round($x, 2),
                            'y' => round($y, 2),
                            'round' => $placement['round'],
                            'position' => $placement['position'],
                        ];
                    })
                    ->filter()
                    ->values();

                $series['points'] = $points;
                $series['polyline'] = $points->map(fn ($point) => $point['x'].','.$point['y'])->implode(' ');
                $series['strokeDasharray'] = $linePatterns[$seriesIndex % count($linePatterns)];
                $series['pointShape'] = $pointShapes[$seriesIndex % count($pointShapes)];

                return $series;
            });
        @endphp

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-4">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-3 p-md-4 text-gray-900">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
                            <div>
                                <div class="small text-muted">Driver comparison</div>
                                <h1 class="h4 mb-0">{{ $driver->name }}</h1>
                            </div>
                            <div class="d-flex flex-wrap gap-2">
                                <a href="{{ route('driver.single', ['driver' => $driver->id, 'edition' => $edition?->id]) }}" class="btn btn-sm btn-outline-secondary">Back</a>
                            </div>
                        </div>

                        <form method="GET" action="{{ route('driver.stats', $driver->id) }}" class="row g-2 align-items-end mb-3">
                            <div class="col-12 col-md-3">
                                <label for="edition" class="form-label small text-muted mb-1">Edition</label>
                                <select class="form-select" name="edition" id="edition">
                                    @foreach($editions as $editionOption)
                                        <option value="{{ $editionOption->id }}" @selected($editionOption->id === $edition?->id)>
                                            {{ $editionOption->edition }} - {{ $editionOption->year }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12 col-md-5">
                                <label for="compare_driver" class="form-label small text-muted mb-1">Add driver</label>
                                <select class="form-select" name="compare_driver" id="compare_driver">
                                    <option value="">Select a driver</option>
                                    @foreach($availableDrivers as $availableDriver)
                                        @if((string) $availableDriver->id !== (string) $driver->id && !$compareIds->contains((string) $availableDriver->id))
                                            <option value="{{ $availableDriver->id }}">{{ $availableDriver->name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>

                            @foreach($compareIds as $compareId)
                                <input type="hidden" name="compare[]" value="{{ $compareId }}">
                            @endforeach

                            <div class="col-12 col-md-4 d-flex gap-2">
                                <button type="submit" class="btn btn-dark">Update</button>
                                <button type="button" id="addCompareDriver" class="btn btn-outline-dark">Add line</button>
                            </div>
                        </form>

                        @if($drivers->isNotEmpty())
                            <div class="d-flex flex-wrap gap-2 mb-3">
                                @foreach($preparedSeries as $series)
                                    <div class="border rounded px-2 py-1 bg-white d-flex align-items-center gap-2">
                                        <svg width="28" height="12" viewBox="0 0 28 12" aria-hidden="true">
                                            <line
                                                x1="1"
                                                y1="6"
                                                x2="27"
                                                y2="6"
                                                stroke="{{ $series['lineColor'] }}"
                                                stroke-width="2.5"
                                                @if($series['strokeDasharray'] !== '')
                                                    stroke-dasharray="{{ $series['strokeDasharray'] }}"
                                                @endif
                                            />
                                        </svg>
                                        <span class="small">{{ $series['driverName'] }}</span>
                                        @if((string) $series['driverId'] !== (string) $driver->id)
                                            <a
                                                href="{{ route('driver.stats', ['driver' => $driver->id, 'edition' => $edition?->id, 'compare' => $compareIds->reject(fn ($id) => $id === (string) $series['driverId'])->values()->all()]) }}"
                                                class="text-decoration-none small"
                                                aria-label="Remove {{ $series['driverName'] }}"
                                            >
                                                x
                                            </a>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <div class="small text-muted mb-2">Race placements comparison</div>

                        @if($chartRounds->isNotEmpty() && $preparedSeries->contains(fn ($series) => $series['points']->isNotEmpty()))
                            <div class="d-flex justify-content-center">
                                <svg viewBox="0 0 {{ $chartWidth }} {{ $chartHeight }}" role="img" aria-label="Confronto piazzamenti gara piloti" style="width:100%;max-width:100%;max-height:360px;">
                                    <line x1="{{ $paddingLeft }}" y1="{{ $paddingTop }}" x2="{{ $paddingLeft }}" y2="{{ $paddingTop + $plotHeight }}" stroke="#d7d7d7" stroke-width="1" />
                                    <line x1="{{ $paddingLeft }}" y1="{{ $paddingTop + $plotHeight }}" x2="{{ $paddingLeft + $plotWidth }}" y2="{{ $paddingTop + $plotHeight }}" stroke="#d7d7d7" stroke-width="1" />

                                    @foreach($yTicks as $tick)
                                        @php
                                            $tickY = $paddingTop + (($tick - 1) * $plotHeight / max($chartMaxPosition - 1, 1));
                                        @endphp
                                        <line x1="{{ $paddingLeft }}" y1="{{ $tickY }}" x2="{{ $paddingLeft + $plotWidth }}" y2="{{ $tickY }}" stroke="#f1f1f1" stroke-width="1" />
                                        <text x="{{ $paddingLeft - 6 }}" y="{{ $tickY + 4 }}" text-anchor="end" font-size="10" fill="#777">{{ $tick }}</text>
                                    @endforeach

                                    @foreach($preparedSeries as $series)
                                        @if($series['polyline'] !== '')
                                            <polyline
                                                fill="none"
                                                stroke="{{ $series['lineColor'] }}"
                                                stroke-width="2.5"
                                                @if($series['strokeDasharray'] !== '')
                                                    stroke-dasharray="{{ $series['strokeDasharray'] }}"
                                                @endif
                                                points="{{ $series['polyline'] }}"
                                            />

                                            @foreach($series['points'] as $point)
                                                @php
                                                    $labelY = $point['y'] <= ($paddingTop + 12) ? $point['y'] + 18 : $point['y'] - 10;
                                                @endphp
                                                @if($series['pointShape'] === 'square')
                                                    <rect
                                                        x="{{ $point['x'] - 4 }}"
                                                        y="{{ $point['y'] - 4 }}"
                                                        width="8"
                                                        height="8"
                                                        fill="{{ $series['lineColor'] }}"
                                                    />
                                                @elseif($series['pointShape'] === 'diamond')
                                                    <polygon
                                                        points="{{ $point['x'] }},{{ $point['y'] - 5 }} {{ $point['x'] + 5 }},{{ $point['y'] }} {{ $point['x'] }},{{ $point['y'] + 5 }} {{ $point['x'] - 5 }},{{ $point['y'] }}"
                                                        fill="{{ $series['lineColor'] }}"
                                                    />
                                                @elseif($series['pointShape'] === 'triangle')
                                                    <polygon
                                                        points="{{ $point['x'] }},{{ $point['y'] - 5 }} {{ $point['x'] + 5 }},{{ $point['y'] + 4 }} {{ $point['x'] - 5 }},{{ $point['y'] + 4 }}"
                                                        fill="{{ $series['lineColor'] }}"
                                                    />
                                                @elseif($series['pointShape'] === 'cross')
                                                    <g stroke="{{ $series['lineColor'] }}" stroke-width="2">
                                                        <line x1="{{ $point['x'] - 4 }}" y1="{{ $point['y'] - 4 }}" x2="{{ $point['x'] + 4 }}" y2="{{ $point['y'] + 4 }}" />
                                                        <line x1="{{ $point['x'] - 4 }}" y1="{{ $point['y'] + 4 }}" x2="{{ $point['x'] + 4 }}" y2="{{ $point['y'] - 4 }}" />
                                                    </g>
                                                @else
                                                    <circle cx="{{ $point['x'] }}" cy="{{ $point['y'] }}" r="4" fill="{{ $series['lineColor'] }}" />
                                                @endif
                                                <text x="{{ $point['x'] }}" y="{{ $labelY }}" text-anchor="middle" font-size="10" fill="{{ $series['lineColor'] }}">P{{ $point['position'] }}</text>
                                            @endforeach
                                        @endif
                                    @endforeach

                                    @foreach($chartRounds as $index => $round)
                                        @php
                                            $roundX = $roundCount > 1
                                                ? $paddingLeft + ($plotWidth * $index / ($roundCount - 1))
                                                : $paddingLeft + ($plotWidth / 2);
                                        @endphp
                                        @if($round['flagIconUrl'])
                                            <image
                                                href="{{ $round['flagIconUrl'] }}"
                                                x="{{ $roundX - 8 }}"
                                                y="{{ $paddingTop + $plotHeight + 16 }}"
                                                width="16"
                                                height="12"
                                                preserveAspectRatio="none"
                                            >
                                                <title>{{ $round['circuitName'] }} - {{ $round['countryName'] }}</title>
                                            </image>
                                        @else
                                            <text x="{{ $roundX }}" y="{{ $paddingTop + $plotHeight + 26 }}" text-anchor="middle" font-size="9" fill="#777">R{{ $round['round'] }}</text>
                                        @endif

                                        <text x="{{ $roundX }}" y="{{ $paddingTop + $plotHeight + 42 }}" text-anchor="middle" font-size="9" fill="#777">R{{ $round['round'] }}</text>
                                    @endforeach
                                </svg>
                            </div>
                        @else
                            <div class="text-muted small">Nessun risultato gara disponibile per questa edizione.</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <script>
            document.getElementById('addCompareDriver')?.addEventListener('click', function () {
                const form = this.closest('form');
                const select = document.getElementById('compare_driver');

                if (!form || !select || !select.value) {
                    return;
                }

                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'compare[]';
                input.value = select.value;
                form.appendChild(input);
                form.submit();
            });
        </script>

        @include('layouts.footer.footer')
    </body>
</html>
