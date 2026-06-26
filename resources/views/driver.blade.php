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
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-4">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-3 text-gray-900">
                            <div class="row">
                                <div class="col-9">
                                    @php
                                        $driverImageUrl = $driver->getImgDriverFromGoogle('racing driver');
                                    @endphp
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
                                        <i class="fa-solid fa-trophy" style="margin-left:2px;margin-right:1px;"></i>
                                        Point
                                    </p>
                                    <span style="font-size:30px">{{ $editionPoints }}</span>
                                </div>
                                <div class="col-1"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-4">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-3 text-gray-900">
                            <div class="row">
                                <div class="col-3">
                                    <div style="width:50px;height:50px;line-height:50px;text-align:center;border:1px solid #ccc;font-size:40px;font-style:italic;padding-left:3px;padding-right:5px;">
                                        {{ $driverNumber }}
                                    </div>
                                </div>
                                <div class="col-3">
                                    <p style="font-style:italic;color:#c1c1c1;font-size:10px;">
                                        <i class="fa-solid fa-grip-vertical" style="margin-left:2px;margin-right:1px;"></i>
                                        Pole
                                    </p>
                                    <span style="font-size:30px">{{ $poleCount }}</span>
                                </div>
                                <div class="col-3">
                                    <p style="font-style:italic;color:#c1c1c1;font-size:10px;">
                                        <i class="fa-solid fa-flag-checkered"></i>
                                        Race
                                    </p>
                                    <span style="font-size:30px">{{ $raceCount }}</span>
                                </div>
                                <div class="col-3">
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
                                                        - <a href="{{route('circuit.single', $circuit['circuitId'])}}">{{ $circuit['countryName'] }} {{ $circuit['city'] }} {{ $circuit['circuitName'] }}</a>
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
            document.getElementById('changeEdition')?.addEventListener('change', function (event) {
                const url = new URL(window.location.href);
                url.searchParams.set('edition', event.target.value);
                window.location.href = url.toString();
            });
        </script>

        @include('layouts.footer.footer')
    </body>
</html>
