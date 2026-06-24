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
                            <div class="mb-2">
                                @php
                                    $circuitImageUrl = $circuit->getImgCircuitFromGoogle('formula one circuit');
                                @endphp
                                @if($circuitImageUrl)
                                    <img
                                        src="{{ $circuitImageUrl }}"
                                        alt="{{ $circuit->name }}"
                                        class="img-fluid"
                                        style="max-height:180px;object-fit:contain;"
                                        loading="lazy"
                                    >
                                @else
                                    <div class="text-muted fst-italic">Foto non disponibile</div>
                                @endif
                            </div>
                            <div class="row">
                                <div class="col-11 d-flex align-items-center gap-2 flex-wrap">
                                    @if($circuit->country->flag_icon_url)
                                        <img
                                            src="{{ $circuit->country->flag_icon_url }}"
                                            alt="{{ $circuit->country->name }} flag"
                                            style="width:20px;height:14px;object-fit:cover;border:1px solid #eee;"
                                            loading="lazy"
                                        >
                                    @endif
                                    <span>{{ $circuit->country->name }} | {{ $circuit->city }} | {{ $circuit->name }}</span>
                                </div>
                                <div class="col-1 text-end">
                                    <a href="https://www.google.com/search?q=wikipedia+{{$circuit->name}}" target="_blank">
                                        <i class="fa-brands fa-wikipedia-w"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-4">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-3 text-gray-900">
                            @forelse($standingDrivers as $standingDriver)
                                <div class="row mb-1 pb-1" style="border-bottom:1px solid #eee;">
                                    <div class="col-12 d-flex align-items-center gap-2">
                                        <div class="col-1">
                                            <div style="width:30px;height:30px;line-height:30px;text-align:center;border:1px solid #ccc;" class="h5">
                                                {{ $standingDriver['position'] }}
                                            </div>
                                        </div>
                                        <div class="col-9 h5">
                                            {{ $standingDriver['driver']->name }}
                                        </div>
                                        <div class="col-2 h5 text-end">
                                            {{ $standingDriver['firstPlaces'] }}
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-muted">Nessun dato disponibile.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @include('layouts.footer.footer')
    </body>
</html>
