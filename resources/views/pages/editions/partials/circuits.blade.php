<div class="row mt-3">
    <div class="col-1"></div>

    <div class="col-10">
        <form method="post" action="{{route('editions.circuit.create')}}">
            @csrf
            <input type="hidden" name="edition_id" value="{{$edition->id}}">

            <div class="row">
                <div class="col-4">
                    <select name="circuit_id" id="circuit_id" class="form-control" required>
                        <option value="" disabled selected>Circuit</option>
                        @foreach($circuits as $circuit)
                            <option value="{{$circuit->id}}">{{$circuit->country->name}} - {{$circuit->city}} - {{$circuit->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-1">
                    <select name="round" id="round" class="form-control" required>
                        <option value="" disabled selected>Round</option>
                        @for($i=1;$i<25;$i++)
                            <option value="{{$i}}">{{$i}}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-2">
                    <input type="date" name="date" class="form-control">
                </div>
                <div class="col-5">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="fa-solid fa-floppy-disk"></i> Add
                    </button>
                </div>
            </div>
        </form>
    </div>

    <div class="col-1"></div>
</div>

<div class="row mt-3">
    <div class="col-1"></div>

    <div class="col-10">
        <table class="table table-hover table-striped table-bordered border" id="tabellaCircuits">
            <thead>
                <tr>
                    <th>Round</th>
                    <th>Date</th>
                    <th>Country</th>
                    <th>City</th>
                    <th>Circuit</th>
                    <th>Sprint result</th>
                    <th>Starting grid</th>
                    <th>Race result</th>
                    <th>Video</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($edition->circuits->sortBy('round') as $editionCircuit)
                    <tr>
                        <td class="text-center">{{$editionCircuit->round}}</td>
                        <td>{{$editionCircuit->date?->format('d/m/Y')}}</td>
                        <td>{{$editionCircuit->circuit->country->name}}</td>
                        <td>{{$editionCircuit->circuit->city}}</td>
                        <td>{{$editionCircuit->circuit->name}}</td>
                        <td>
                            @foreach($editionCircuit->sprint->sortBy('position')->take(3) as $sprintCircuit)
                                {{$sprintCircuit->position}}. {{$sprintCircuit->driverTeam->driver->name}} {{$sprintCircuit->driverTeam->driver->number}} - {{$sprintCircuit->time}}
                                <br>
                            @endforeach
                        </td>
                        <td>
                            @foreach($editionCircuit->grid->sortBy('position')->take(3) as $gridCircuit)
                                {{$gridCircuit->position}}. {{$gridCircuit->driverTeam->driver->name}} {{$gridCircuit->driverTeam->driver->number}} - {{$gridCircuit->time}}
                                <br>
                            @endforeach
                        </td>
                        <td>
                            @foreach($editionCircuit->race->sortBy('position')->take(3) as $raceCircuit)
                                {{$raceCircuit->position}}. {{$raceCircuit->driverTeam->driver->name}} {{$raceCircuit->driverTeam->driver->number}} - {{$raceCircuit->time}}
                                <br>
                            @endforeach
                        </td>
                        <td>
                            @foreach($editionCircuit->videos as $i => $video)
                                <a href="{{$video->url}}" target="_target" title="{{$video->title}}">
                                    <i class="fa fa-video text-info"></i>
                                </a>
                                |
                            @endforeach
                        </td>
                        <td>
                            <button class="offcanvasModal" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasBottom" aria-controls="offcanvasBottom" id="{{$editionCircuit->id}}">
                                <i class="fa fa-eye text-info"></i>
                            </button>
                            |
                            <a href="{{route('editions.circuit.edit', [$edition->id, $editionCircuit->id])}}">
                                <i class="fa fa-edit text-primary"></i>
                            </a>
                            |

                            @if($editionCircuit->sprint->count() ||$editionCircuit->grid->count() || $editionCircuit->race->count())
                                <i class="fa-solid fa-trash text-secondary" title="Action not possible"></i>
                            @else
                                <a href="{{route('editions.circuit.delete', [$edition->id, $editionCircuit->id])}}">
                                    <i class="fa-solid fa-trash text-danger"></i>
                                </a>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="col-1"></div>
</div>
