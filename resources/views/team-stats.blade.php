<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('layouts.head.head')
    </head>
    <body class="antialiased bg-light">
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
            $paddingBottom = 52;
            $plotWidth = $chartWidth - $paddingLeft - $paddingRight;
            $plotHeight = $chartHeight - $paddingTop - $paddingBottom;
            $yearCount = $chartYears->count();
            $yTicks = $chartMaxPosition <= 8
                ? range(1, $chartMaxPosition)
                : array_values(array_unique([1, (int) ceil($chartMaxPosition / 2), $chartMaxPosition]));
            $linePatterns = ['', '10 6', '4 4', '12 4 3 4', '2 5', '14 6 2 6'];
            $preparedSeries = collect($chartSeries)->values()->map(function ($series, $seriesIndex) use ($chartYears, $yearCount, $paddingLeft, $paddingTop, $plotWidth, $plotHeight, $chartMaxPosition, $linePatterns) {
                $points = collect($series['placements'])->map(function ($placement) use ($chartYears, $yearCount, $paddingLeft, $paddingTop, $plotWidth, $plotHeight, $chartMaxPosition) {
                    $yearIndex = $chartYears->search(fn ($year) => $year === $placement['year']);

                    if ($yearIndex === false) {
                        return null;
                    }

                    $x = $yearCount > 1
                        ? $paddingLeft + ($plotWidth * $yearIndex / ($yearCount - 1))
                        : $paddingLeft + ($plotWidth / 2);
                    $y = $paddingTop + (($placement['position'] - 1) * $plotHeight / max($chartMaxPosition - 1, 1));

                    return [
                        'x' => round($x, 2),
                        'y' => round($y, 2),
                        'position' => $placement['position'],
                    ];
                })->filter()->values();

                $series['points'] = $points;
                $series['polyline'] = $points->map(fn ($point) => $point['x'].','.$point['y'])->implode(' ');
                $series['strokeDasharray'] = $linePatterns[$seriesIndex % count($linePatterns)];

                return $series;
            });
        @endphp

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-4">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-3 p-md-4 text-gray-900">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
                            <div>
                                <div class="small text-muted">Team comparison</div>
                                <h1 class="h4 mb-0">{{ $team->name }}</h1>
                            </div>
                            <div class="d-flex flex-wrap gap-2">
                                <div class="input-group input-group-sm w-auto">
                                    <span class="bg-white input-group-text text-decoration-none">Team</span>
                                    <select class="form-select" name="changeTeam" id="changeTeam" aria-label="changeTeam">
                                        @foreach($availableTeams as $teamOption)
                                            <option value="{{ $teamOption->id }}" @selected($teamOption->id === $team->id)>{{ $teamOption->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <a href="{{ route('team.single', $team->id) }}" class="btn btn-sm btn-outline-secondary">Back</a>
                            </div>
                        </div>

                        <form method="GET" action="{{ route('team.stats', $team->id) }}" class="row g-2 align-items-end mb-3">
                            <div class="col-12 col-md-8">
                                <label for="compare_team" class="form-label small text-muted mb-1">Add team</label>
                                <select class="form-select" name="compare_team" id="compare_team">
                                    <option value="">Select a team</option>
                                    @foreach($availableTeams as $availableTeam)
                                        @if((string) $availableTeam->id !== (string) $team->id && !$compareIds->contains((string) $availableTeam->id))
                                            <option value="{{ $availableTeam->id }}">{{ $availableTeam->name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>

                            @foreach($compareIds as $compareId)
                                <input type="hidden" name="compare[]" value="{{ $compareId }}">
                            @endforeach

                            <div class="col-12 col-md-4 d-flex gap-2">
                                <button type="submit" class="btn btn-dark">Update</button>
                                <button type="button" id="addCompareTeam" class="btn btn-outline-dark">Add line</button>
                            </div>
                        </form>

                        <div class="d-flex flex-wrap gap-2 mb-3">
                            @foreach($preparedSeries as $series)
                                <div class="border rounded px-2 py-1 bg-white d-flex align-items-center gap-2">
                                    <svg width="28" height="12" viewBox="0 0 28 12" aria-hidden="true">
                                        <line x1="1" y1="6" x2="27" y2="6" stroke="{{ $series['lineColor'] }}" stroke-width="2.5" @if($series['strokeDasharray'] !== '') stroke-dasharray="{{ $series['strokeDasharray'] }}" @endif />
                                    </svg>
                                    <span class="small">{{ $series['teamName'] }}</span>
                                    @if((string) $series['teamId'] !== (string) $team->id)
                                        <a href="{{ route('team.stats', ['team' => $team->id, 'compare' => $compareIds->reject(fn ($id) => $id === (string) $series['teamId'])->values()->all()]) }}" class="text-decoration-none small" aria-label="Remove {{ $series['teamName'] }}">x</a>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <div class="small text-muted mb-2">Placement over the years comparison</div>

                        @if($chartYears->isNotEmpty() && $preparedSeries->contains(fn ($series) => $series['points']->isNotEmpty()))
                            <div class="d-flex justify-content-center">
                                <svg viewBox="0 0 {{ $chartWidth }} {{ $chartHeight }}" role="img" aria-label="Confronto classifiche finali dei team" style="display:block;width:95%;height:auto;margin:0 auto;">
                                    <line x1="{{ $paddingLeft }}" y1="{{ $paddingTop }}" x2="{{ $paddingLeft }}" y2="{{ $paddingTop + $plotHeight }}" stroke="#d7d7d7" stroke-width="1" />
                                    <line x1="{{ $paddingLeft }}" y1="{{ $paddingTop + $plotHeight }}" x2="{{ $paddingLeft + $plotWidth }}" y2="{{ $paddingTop + $plotHeight }}" stroke="#d7d7d7" stroke-width="1" />

                                    @foreach($yTicks as $tick)
                                        @php $tickY = $paddingTop + (($tick - 1) * $plotHeight / max($chartMaxPosition - 1, 1)); @endphp
                                        <line x1="{{ $paddingLeft }}" y1="{{ $tickY }}" x2="{{ $paddingLeft + $plotWidth }}" y2="{{ $tickY }}" stroke="#f1f1f1" stroke-width="1" />
                                        <text x="{{ $paddingLeft - 6 }}" y="{{ $tickY + 4 }}" text-anchor="end" font-size="10" fill="#777">{{ $tick }}</text>
                                    @endforeach

                                    @foreach($preparedSeries as $series)
                                        @if($series['polyline'] !== '')
                                            <polyline fill="none" stroke="{{ $series['lineColor'] }}" stroke-width="2.5" @if($series['strokeDasharray'] !== '') stroke-dasharray="{{ $series['strokeDasharray'] }}" @endif points="{{ $series['polyline'] }}" />
                                            @foreach($series['points'] as $point)
                                                @php $labelY = $point['y'] <= ($paddingTop + 12) ? $point['y'] + 18 : $point['y'] - 10; @endphp
                                                <circle cx="{{ $point['x'] }}" cy="{{ $point['y'] }}" r="4" fill="{{ $series['lineColor'] }}" />
                                                <text x="{{ $point['x'] }}" y="{{ $labelY }}" text-anchor="middle" font-size="10" fill="{{ $series['lineColor'] }}">P{{ $point['position'] }}</text>
                                            @endforeach
                                        @endif
                                    @endforeach

                                    @foreach($chartYears as $index => $year)
                                        @php $yearX = $yearCount > 1 ? $paddingLeft + ($plotWidth * $index / ($yearCount - 1)) : $paddingLeft + ($plotWidth / 2); @endphp
                                        <text x="{{ $yearX }}" y="{{ $paddingTop + $plotHeight + 28 }}" text-anchor="middle" font-size="10" fill="#777">{{ $year }}</text>
                                    @endforeach
                                </svg>
                            </div>
                        @else
                            <div class="text-muted small">Nessuna classifica finale disponibile per i team selezionati.</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <script>
            const teamStatsUrlTemplate = @json(route('team.stats', ['team' => '__TEAM__']));

            document.getElementById('changeTeam')?.addEventListener('change', function (event) {
                const url = new URL(teamStatsUrlTemplate.replace('__TEAM__', event.target.value), window.location.origin);
                const currentUrl = new URL(window.location.href);

                currentUrl.searchParams.forEach((value, key) => url.searchParams.append(key, value));
                window.location.href = url.toString();
            });

            document.getElementById('addCompareTeam')?.addEventListener('click', function () {
                const form = this.closest('form');
                const select = document.getElementById('compare_team');

                if (!form || !select || !select.value) return;

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
