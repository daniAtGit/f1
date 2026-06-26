<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-3 text-gray-900">
                    <div class="row">
                        <div class="col-4">
                            <div class="small">PREV</div>
                            @if($prevRace)
                                <div class="small mt-1">
                                    @if($prevRace->date)
                                        <p class="small">{{$prevRace->date->format('d/m/Y')}}</p>
                                    @endif
                                    <b>Round {{$prevRace->round}}</b>
                                    <br>
                                    <span class="small d-inline-flex flex-column flex-sm-row align-items-center gap-1 text-center">
                                        @if($prevRace->circuit?->country?->flag_icon_url)
                                            <img
                                                src="{{$prevRace->circuit->country->flag_icon_url}}"
                                                alt="{{$prevRace->circuit?->country?->name}} flag"
                                                style="width:20px;height:14px;object-fit:cover;border:1px solid #eee;"
                                                loading="lazy"
                                            >
                                        @endif
                                        <span><a href="{{route('circuit.single', $prevRace->circuit)}}">{{$prevRace->circuit->name}}</a></span>
                                    </span>
                                    <br>
                                    @foreach($prevRace->videos as $video)
                                        <a href="{{$video->url}}" target="_target" title="{{$video->title}}">
                                            <i class="fa fa-youtube text-danger"></i>
                                        </a>
                                        |
                                    @endforeach
                                </div>
                            @else
                                <div class="small mt-1 text-muted">Nessuna gara disponibile</div>
                            @endif
                        </div>

                        <div class="col-4 text-center" style="border:2px solid #ddd;padding:12px;box-shadow:0 10px 14px -12px rgba(0,0,0,.55);">
                            <div class="small">CURRENT</div>
                            @if($currentRace)
                                <div class="small mt-1">
                                    @if($currentRace->date)
                                        {{$currentRace->date->format('d/m/Y')}}
                                    @endif
                                    <p style="font-size:22px;"><b>Round {{$currentRace->round}}</b></p>
                                    <span class="d-inline-flex flex-column flex-sm-row align-items-center gap-1 text-center">
                                        @if($currentRace->circuit?->country?->flag_icon_url)
                                            <img
                                                src="{{$currentRace->circuit->country->flag_icon_url}}"
                                                alt="{{$currentRace->circuit?->country?->name}} flag"
                                                style="width:20px;height:14px;object-fit:cover;border:1px solid #eee;"
                                                loading="lazy"
                                            >
                                        @endif
                                        <span><a href="{{route('circuit.single', $currentRace->circuit)}}">{{$currentRace->circuit->name}}</a></span>
                                    </span>
                                    <br>
                                    @foreach($currentRace->videos as $video)
                                        <a href="{{$video->url}}" target="_target" title="{{$video->title}}">
                                            <i class="fa fa-youtube text-danger"></i>
                                        </a>
                                        |
                                    @endforeach
                                </div>
                            @else
                                <div class="small mt-1 text-muted">Nessuna gara disponibile</div>
                            @endif
                        </div>

                        <div class="col-4 text-end">
                            <div class="small">NEXT</div>
                            @if($nextRace)
                                <div class="small mt-1">
                                    @if($nextRace->date)
                                        <p class="small">{{$nextRace->date->format('d/m/Y')}}</p>
                                    @endif
                                    <b>Round {{$nextRace->round}}</b>
                                    <br>
                                    <span class="small d-inline-flex flex-column flex-sm-row align-items-center gap-1 text-center">
                                        @if($nextRace->circuit?->country?->flag_icon_url)
                                            <img
                                                src="{{$nextRace->circuit->country->flag_icon_url}}"
                                                alt="{{$nextRace->circuit?->country?->name}} flag"
                                                style="width:20px;height:14px;object-fit:cover;border:1px solid #eee;"
                                                loading="lazy"
                                            >
                                        @endif
                                        <span><a href="{{route('circuit.single', $nextRace->circuit)}}">{{$nextRace->circuit->name}}</a></span>
                                    </span>
                                </div>
                            @else
                                <div class="small mt-1 text-muted">Nessuna gara disponibile</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-2">
            <div class="overflow-hidden">
                <div class="d-flex justify-content-end">
                    <div class="input-group w-auto">
{{--                        <span class="bg-white input-group-text" id="basic-addon1">Edition</span>--}}
                        <a class="bg-white input-group-text text-decoration-none" id="basic-addon1" href="{{ route('edition.single', ['edition' => $edition?->id]) }}">Edition</a>
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
                <div class="col-12 col-lg-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg h-100">
                        <div class="p-3 text-gray-900">
                            DRIVER

                            <div class="row mt-3">
                                <div class="col-12">
                                    <div class="row mb-1 my-3">
                                        <div class="col-12 d-flex align-items-center gap-2">
                                            <div class="col-1" title="Position">Pos</div>
                                            <div class="col-1" title="Position">N.</div>
                                            <div class="col-5">Driver</div>
                                            <div class="col-4">Team</div>
                                            <div class="col-1">Pts</div>
                                        </div>
                                    </div>
                                    <div class="my-3">
                                        @include('pages.partials.standingDrivers')
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg h-100">
                        <div class="p-3 text-gray-900">
                            TEAM

                            <div class="row mt-3">
                                <div class="col-12">
                                    <div class="row mb-1 my-3">
                                        <div class="col-12 d-flex align-items-center gap-2">
                                            <div class="col-1" title="Position">Pos</div>
                                            <div class="col-10">Team</div>
                                            <div class="col-1">Pts</div>
                                        </div>
                                    </div>
                                    <div class="my-3">
                                        @include('pages.partials.standingTeams')
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('changeEdition')?.addEventListener('change', function (event) {
            const url = @json(route('dashboard', ['edition' => '__EDITION__']));
            window.location.href = url.replace('__EDITION__', event.target.value);
        });
    </script>
</x-app-layout>
