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
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-2">
                    <div class="overflow-hidden">
                        <div class="d-flex justify-content-end">
                            <div class="input-group w-auto">
                                <span class="bg-white input-group-text" id="basic-addon1">Edition</span>
                                <select class="form-select" name="changeEdition" id="changeEdition" aria-label="changeEdition" aria-describedby="basic-addon1">
                                    @foreach($editions as $editionOption)
                                        <option value="{{$editionOption->id}}" @selected($editionOption->id === $edition?->id)>{{$editionOption->edition}} - {{$editionOption->year}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-2">
                    <div class="row g-3">
                        <div class="col-12 col-lg-12">
                            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg h-100">
                                <div class="p-3 text-gray-900">
                                    @forelse($editionCircuits as $circuit)
                                        <div class="mb-4">
                                            <div class="mb-3">
                                                Round <b>{{ $circuit['round'] }}</b>
                                                |
                                                @if($circuit['date'])
                                                    <span style="font-size:11px;">{{ $circuit['date'] }}</span>
                                                @endif
                                                <br>
                                                <span class="d-inline-flex align-items-center gap-2">
                                                    @if($circuit['country']?->flag_icon_url)
                                                        <img
                                                            src="{{ $circuit['country']?->flag_icon_url }}"
                                                            alt="{{ $circuit['countryName'] }} flag"
                                                            style="width:20px;height:14px;object-fit:cover;border:1px solid #eee;"
                                                            loading="lazy"
                                                        >
                                                    @endif
                                                    <span><a href="{{route('circuit.single', $circuit['id'])}}">{{ $circuit['countryName'] }} | {{ $circuit['city'] }} | {{ $circuit['circuitName'] }}</a></span>
                                                </span>
                                            </div>

                                            <div class="row g-3 mb-3">
                                                <div class="col-12 col-lg-3">
                                                    <div class="small fw-bold mb-2"><i class="fa-solid fa-grip-vertical"></i> Grid</div>
                                                    <div class="d-grid gap-2">
                                                        @forelse($circuit['gridResults'] as $result)
                                                            <div class="d-flex align-items-center gap-2">
                                                                <span style="width:30px;height:30px;line-height:30px;padding:0 3px;text-align:center;border:1px solid #ccc;flex:0 0 auto;">{!! $result['position'] == 1 ? '<i class="fa-solid fa-trophy text-warning"></i>' : $result['position'] !!}</span>
                                                                <div class="small">
                                                                    {{ $result['driverName'] }}
{{--                                                                    | <b>{{ $result['number'] }}</b>--}}
{{--                                                                    <br>--}}
{{--                                                                    <span class="badge" style="background:{{ $result['teamColor'] }};">{{ $result['teamName'] }}</span>--}}
                                                                </div>
                                                            </div>
                                                        @empty
                                                            <div class="text-muted small">Nessun dato disponibile.</div>
                                                        @endforelse
                                                    </div>
                                                </div>

                                                <div class="col-12 col-lg-3">
                                                    <div class="small fw-bold mb-2"><i class="fa-solid fa-flag-checkered"></i> Race</div>
                                                    <div class="d-grid gap-2">
                                                        @forelse($circuit['raceResults'] as $result)
                                                            <div class="d-flex align-items-center gap-2">
                                                                <span style="width:30px;height:30px;line-height:30px;padding:0 3px;text-align:center;border:1px solid #ccc;flex:0 0 auto;">{!! $result['position'] == 1 ? '<i class="fa-solid fa-trophy text-warning"></i>' : $result['position'] !!}</span>
                                                                <div class="small">
                                                                    {{ $result['driverName'] }}
{{--                                                                    | <b>{{ $result['number'] }}</b>--}}
{{--                                                                    <br>--}}
{{--                                                                    <span class="badge" style="background:{{ $result['teamColor'] }};">{{ $result['teamName'] }}</span>--}}
                                                                </div>
                                                            </div>
                                                        @empty
                                                            <div class="text-muted small">Nessun dato disponibile.</div>
                                                        @endforelse
                                                    </div>
                                                </div>

                                                <div class="col-12 col-lg-3">
                                                    <div class="small fw-bold mb-2"><i class="fa-regular fa-flag"></i> Sprint</div>
                                                    @if($circuit['sprintResults']->isNotEmpty())
                                                        <div class="d-grid gap-2">
                                                            @foreach($circuit['sprintResults'] as $result)
                                                                <div class="d-flex align-items-center gap-2">
                                                                    <span style="width:30px;height:30px;line-height:30px;padding:0 3px;text-align:center;border:1px solid #ccc;flex:0 0 auto;">{!! $result['position'] == 1 ? '<i class="fa-solid fa-trophy text-warning"></i>' : $result['position'] !!}</span>
                                                                    <div class="small">
                                                                        {{ $result['driverName'] }}
{{--                                                                        | <b>{{ $result['number'] }}</b>--}}
{{--                                                                        <br>--}}
{{--                                                                        <span class="badge" style="background:{{ $result['teamColor'] }};">{{ $result['teamName'] }}</span>--}}
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @else
                                                        <div class="text-muted small">Nessun dato disponibile.</div>
                                                    @endif
                                                </div>

                                                <div class="col-12 col-lg-3">
                                                    <div class="small fw-bold mb-2"></div>
                                                    <div class="d-grid gap-2">
                                                        @forelse($circuit['videos'] as $video)
                                                            <a href="{{ $video['url'] }}" target="_blank" rel="noopener" class="border p-1 small text-decoration-none d-inline-flex align-items-center gap-2">
                                                                <i class="fa fa-video text-info"></i>
                                                                <span>{{ $video['title'] ?: 'Video' }}</span>
                                                            </a>
                                                        @empty
                                                            <div class="text-muted small">Nessun video collegato.</div>
                                                        @endforelse
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
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
            document.getElementById('changeEdition')?.addEventListener('change', function (event) {
                const url = @json(route('edition.single', ['edition' => '__EDITION__']));
                window.location.href = url.replace('__EDITION__', event.target.value);
            });
        </script>

        @include('layouts.footer.footer')
    </body>
</html>
