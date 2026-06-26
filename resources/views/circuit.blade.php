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
                            @php
                                $circuitImageUrl = $circuit->getImgCircuitFromGoogle('formula one circuit');
                            @endphp

                            <div class="row g-3 align-items-center">
                                <div class="col-12 col-lg-8">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        @if($circuit->country->flag_icon_url)
                                            <img
                                                src="{{ $circuit->country->flag_icon_url }}"
                                                alt="{{ $circuit->country->name }} flag"
                                                style="width:24px;height:16px;object-fit:cover;border:1px solid #eee;"
                                                loading="lazy"
                                            >
                                        @endif
                                        <span class="text-muted">{{ $circuit->country->name }} | {{ $circuit->city }}</span>
                                    </div>

                                    <div class="h1 mb-3">{{ $circuit->name }}</div>

                                    <a href="https://www.google.com/search?q=wikipedia+{{$circuit->name}}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                        <i class="fa-brands fa-wikipedia-w"></i> Wikipedia
                                    </a>
                                </div>

                                <div class="col-12 col-lg-4 text-lg-end">
                                    @if($circuitImageUrl)
                                        <img
                                            src="{{ $circuitImageUrl }}"
                                            alt="{{ $circuit->name }}"
                                            class="img-fluid rounded border"
                                            style="max-height:220px;object-fit:contain;background:#fff;"
                                            loading="lazy"
                                        >
                                    @else
                                        <div class="text-muted fst-italic">Foto non disponibile</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-4">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-3 text-gray-900">
                            <div class="row g-3">
                                <div class="col-12 col-lg-4">
                                    @include('partials.circuit_driver_standings', [
                                        'title' => 'Pole',
                                        'icon' => 'fa-solid fa-gauge',
                                        'standings' => $poleDrivers,
                                    ])
                                </div>

                                <div class="col-12 col-lg-4">
                                    @include('partials.circuit_driver_standings', [
                                        'title' => 'Race',
                                        'icon' => 'fa-solid fa-flag-checkered',
                                        'standings' => $raceDrivers,
                                    ])
                                </div>

                                <div class="col-12 col-lg-4">
                                    @include('partials.circuit_driver_standings', [
                                        'title' => 'Sprint',
                                        'icon' => 'fa-regular fa-flag',
                                        'standings' => $sprintDrivers,
                                    ])
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @include('layouts.footer.footer')
    </body>
</html>
