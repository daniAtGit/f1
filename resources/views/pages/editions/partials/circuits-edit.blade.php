<x-app-layout>
    <x-slot name="header">
        <div class="row">
            <div class="col-10">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Edit edition/circuit
                </h2>
            </div>
            <div class="col-2 text-end">
                <a href="{{route('editions.edit',$editionCircuit->edition)}}">
                    <button type="button" class="btn btn-sm btn-outline-secondary">
                        <i class="fa-solid fa-arrow-left"></i> Back
                    </button>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="container-fluid border">
{{--Info--}}
        <form method="post" action="{{route('editions.circuit.update')}}">
            @csrf
            <input type="hidden" name="editionCircuitId" value="{{$editionCircuit->id}}">

            <div class="card my-4">
                <div class="row mt-3">
                    <div class="col-1"></div>

                    <div class="col-10">
                        <div class="row">
                            <div class="col-4">
                                <select name="circuit_id" id="circuit_id" class="form-control" required>
                                    <option value="" disabled selected>Select circuit</option>
                                    @foreach($circuits as $circuit)
                                        <option value="{{$circuit->id}}" @selected($circuit->id == $editionCircuit->circuit_id)>{{$circuit->country->name}} - {{$circuit->city}} - {{$circuit->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-2">
                                <select name="round" id="round" class="form-control" required>
                                    <option value="" disabled selected>Select round</option>
                                    @for($i=1;$i<25;$i++)
                                        <option value="{{$i}}" @selected($i == $editionCircuit->round)>{{$i}}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-2">
                                <input type="date" name="date" class="form-control" value="{{$editionCircuit->date?->format('Y-m-d')}}">
                            </div>
                            <div class="col-3">
                                <button type="submit" class="btn btn-outline-primary">
                                    <i class="fa-solid fa-floppy-disk"></i> Update
                                </button>

                                <button type="button" class="btn btn-outline-info" title="Add video" id="addVideo">
                                    <i class="fa-solid fa-video"></i> Add
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="col-1"></div>
                </div>

                <div class="row mt-3">
                    <div class="col-1"></div>
                    <div class="col-10">
                        <div class="altri"></div>
                    </div>
                    <div class="col-1"></div>
                </div>
            </div>
        </form>

        <div class="card my-4">
            <div class="row mt-2">
                <div class="col-1"></div>

                <div class="col-10">
                    @foreach($editionCircuit->videos as $i => $video)
                        <div class='input-group mb-2'>
                            <span class='input-group-text'><i class='fa fa-link'></i></span>
                            <span class='input-group-text'>{{$video->title}}</span>
                            <a href="{{$video->url}}" target="_target">
                                <button class="btn btn-info">
                                    <i class='fa fa-eye'></i>
                                </button>
                            </a>
                            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalEdit{{$i}}">
                                <i class="fa fa-edit"></i>
                            </button>
                            <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#modalDelete{{$i}}" title="Delete">
                                <i class="fa fa-minus"></i>
                            </button>
                        </div>
                    @endforeach
                </div>

                <div class="col-1"></div>
            </div>
        </div>

        <div class="mb-3">
            <div class="row gx-3">
{{--Grid--}}
                <div class="col-4">
                    <div class="card p-3">
                        <div>
                            <i class="fa-solid fa-grip-vertical"></i> Starting grid
                        </div>
                        <br>

                        <form method="post" action="{{route('editions.circuit.grid.create')}}" class="mb-4">
                            @csrf
                            <input type="hidden" name="editionCircuit_id" value="{{$editionCircuit->id}}">
                            <input type="hidden" name="circuit_id" value="{{$editionCircuit->circuit->id}}">

                            <div class="row">
                                <div class="col-2">
                                    <select name="position" class="form-control" required>
                                        <option value="" disabled selected>Pos.</option>
                                        @for($i=1;$i<=$editionCircuit->edition->driversTeams->count();$i++)
                                            <option value="{{$i}}">{{$i}}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-5">
                                    <select name="driverTeam_id" class="form-control" required>
                                        <option value="" disabled selected>Driver</option>
                                        @foreach($editionCircuit->edition->driversTeams as $driverTeam)
                                            <option value="{{$driverTeam->id}}">{{$driverTeam->driver->name}} - {{$driverTeam->team->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-3">
                                    <input type="text" name="time" class="form-control" placeholder="Time">
                                </div>
                                <div class="col-1">
                                    <button type="submit" class="btn btn-outline-primary">
                                        <i class="fa-solid fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </form>

                        <div class="row mb-1 pb-1" style="border-bottom:1px solid #eee;">
                            <div class="col-1 text-center" title="Position">P.</div>
                            <div class="col-1 text-center" title="Number">N.</div>
                            <div class="col-2">Team</div>
                            <div class="col-4">Driver</div>
                            <div class="col-3">Time</div>
                            <div class="col-1"></div>
                        </div>
                        @foreach($grids as $grid)
                            <div class="row mb-1 pb-1" style="border-bottom:1px solid #eee;">
                                <div class="col-1">
                                    <div style="width:25px;height:25px;line-height:25px;text-align:center;border:1px solid #ccc;">
                                        {{$grid->position}}
                                    </div>
                                </div>
                                <div class="col-1">
                                    <div style="width:25px;height:25px;line-height:25px;text-align:center;border:1px solid #ccc;">
                                        {{$grid->driverTeam->number}}
                                    </div>
                                </div>
                                <div class="col-2">
                                    <badge class="badge" style="background:{{$grid->driverTeam->team->color}};width:100%;text-align:left;overflow:hidden;white-space:nowrap;">
                                        <i class="fa fa-car-side"></i> {{$grid->driverTeam->team->name}}
                                    </badge>
                                </div>
                                <div class="col-5">
                                    <b>{{$grid->driverTeam?->driver?->name}}</b>
                                </div>
                                <div class="col-2">
                                    {{$grid->time}}
                                </div>
                                <div class="col-1">
                                    <form method="post" action="{{route('editions.circuit.grid.delete')}}">
                                        @csrf
                                        <input type="hidden" name="editionCircuit_id" value="{{$editionCircuit->id}}">
                                        <input type="hidden" name="circuit_id" value="{{$editionCircuit->circuit->id}}">
                                        <input type="hidden" name="grid_id" value="{{$grid->id}}">

                                        <button type="submit" class="btn btn-sm btn-outline-danger fa fa-minus" title="Delete"></button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

{{--Race--}}
                <div class="col-4">
                    <div class="card p-3">
                        <div>
                            <i class="fa-solid fa-flag-checkered"></i> Race result
                        </div>
                        <br>

                        <form method="post" action="{{route('editions.circuit.race.create')}}" class="mb-4">
                            @csrf
                            <input type="hidden" name="editionCircuit_id" value="{{$editionCircuit->id}}">
                            <input type="hidden" name="circuit_id" value="{{$editionCircuit->circuit->id}}">

                            <div class="row">
                                <div class="col-2">
                                    <select name="position" class="form-control" required>
                                        <option value="" disabled selected>Pos.</option>
                                        @for($i=1;$i<=$editionCircuit->edition->driversTeams->count();$i++)
                                            <option value="{{$i}}">{{$i}}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-5">
                                    <select name="driverTeam_id" class="form-control" required>
                                        <option value="" disabled selected>Driver</option>
                                        @foreach($editionCircuit->edition->driversTeams as $driverTeam)
                                            <option value="{{$driverTeam->id}}">{{$driverTeam->driver->name}} - {{$driverTeam->team->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-3">
                                    <input type="text" name="time" class="form-control" placeholder="Time">
                                </div>
                                <div class="col-1">
                                    <button type="submit" class="btn btn-outline-primary">
                                        <i class="fa-solid fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </form>

                        <div class="row mb-1 pb-1" style="border-bottom:1px solid #eee;">
                            <div class="col-1 text-center" title="Position">P.</div>
                            <div class="col-1 text-center" title="Number">N.</div>
                            <div class="col-2">Team</div>
                            <div class="col-4">Driver</div>
                            <div class="col-3">Time</div>
                            <div class="col-1"></div>
                        </div>
                        @foreach($races as $race)
                            <div class="row mb-1 pb-1" style="border-bottom:1px solid #eee;">
                                <div class="col-1">
                                    <div style="width:25px;height:25px;line-height:25px;text-align:center;border:1px solid #ccc;">
                                        {{$race->position}}
                                    </div>
                                </div>
                                <div class="col-1">
                                    <div style="width:25px;height:25px;line-height:25px;text-align:center;border:1px solid #ccc;">
                                        {{$race->driverTeam->number}}
                                    </div>
                                </div>
                                <div class="col-2">
                                    <badge class="badge" style="background:{{$race->driverTeam->team->color}};width:100%;text-align:left;overflow:hidden;white-space:nowrap;">
                                        <i class="fa fa-car-side"></i> {{$race->driverTeam->team->name}}
                                    </badge>
                                </div>
                                <div class="col-4">
                                    <b>{{$race->driverTeam?->driver?->name}}</b>
                                </div>
                                <div class="col-3">
                                    {{$race->time}}
                                </div>
                                <div class="col-1">
                                    <form method="post" action="{{route('editions.circuit.race.delete')}}">
                                        @csrf
                                        <input type="hidden" name="editionCircuit_id" value="{{$editionCircuit->id}}">
                                        <input type="hidden" name="circuit_id" value="{{$editionCircuit->circuit->id}}">
                                        <input type="hidden" name="race_id" value="{{$race->id}}">

                                        <button type="submit" class="btn btn-sm btn-outline-danger fa fa-minus" title="Delete"></button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

{{--Sprint--}}

                <div class="col-4">
                    <div class="card p-3">
                        <div>
                            <i class="fa-regular fa-flag"></i> Sprint result
                        </div>
                        <br>

                        <form method="post" action="{{route('editions.circuit.sprint.create')}}" class="mb-4">
                            @csrf
                            <input type="hidden" name="editionCircuit_id" value="{{$editionCircuit->id}}">
                            <input type="hidden" name="circuit_id" value="{{$editionCircuit->circuit->id}}">

                            <div class="row">
                                <div class="col-2">
                                    <select name="position" class="form-control" required>
                                        <option value="" disabled selected>Pos.</option>
                                        @for($i=1;$i<=$editionCircuit->edition->driversTeams->count();$i++)
                                            <option value="{{$i}}">{{$i}}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-5">
                                    <select name="driverTeam_id" class="form-control" required>
                                        <option value="" disabled selected>Driver</option>
                                        @foreach($editionCircuit->edition->driversTeams as $driverTeam)
                                            <option value="{{$driverTeam->id}}">{{$driverTeam->driver->name}} - {{$driverTeam->team->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-3">
                                    <input type="text" name="time" class="form-control" placeholder="Time">
                                </div>
                                <div class="col-1">
                                    <button type="submit" class="btn btn-outline-primary">
                                        <i class="fa-solid fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </form>

                        <div class="row mb-1 pb-1" style="border-bottom:1px solid #eee;">
                            <div class="col-1 text-center" title="Position">P.</div>
                            <div class="col-1 text-center" title="Number">N.</div>
                            <div class="col-2">Team</div>
                            <div class="col-4">Driver</div>
                            <div class="col-3">Time</div>
                            <div class="col-1"></div>
                        </div>
                        @foreach($sprints as $sprint)
                            <div class="row mb-1 pb-1" style="border-bottom:1px solid #eee;">
                                <div class="col-1">
                                    <div style="width:25px;height:25px;line-height:25px;text-align:center;border:1px solid #ccc;">
                                        {{$sprint->position}}
                                    </div>
                                </div>
                                <div class="col-1">
                                    <div style="width:25px;height:25px;line-height:25px;text-align:center;border:1px solid #ccc;">
                                        {{$sprint->driverTeam->number}}
                                    </div>
                                </div>
                                <div class="col-2">
                                    <badge class="badge" style="background:{{$sprint->driverTeam->team->color}};width:100%;text-align:left;overflow:hidden;white-space:nowrap;">
                                        <i class="fa fa-car-side"></i> {{$sprint->driverTeam->team->name}}
                                    </badge>
                                </div>
                                <div class="col-4">
                                    <b>{{$sprint->driverTeam?->driver?->name}}</b>
                                </div>
                                <div class="col-3">
                                    {{$sprint->time}}
                                </div>
                                <div class="col-1">
                                    <form method="post" action="{{route('editions.circuit.sprint.delete')}}">
                                        @csrf
                                        <input type="hidden" name="editionCircuit_id" value="{{$editionCircuit->id}}">
                                        <input type="hidden" name="circuit_id" value="{{$editionCircuit->circuit->id}}">
                                        <input type="hidden" name="sprint_id" value="{{$sprint->id}}">

                                        <button type="submit" class="btn btn-sm btn-outline-danger fa fa-minus" title="Delete"></button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    @section('modal')
        @foreach($editionCircuit->videos as $i => $video)
            <!-- Modal Edit -->
            <div class="modal fade" id="modalEdit{{$i}}" tabindex="-1" aria-labelledby="modalEdit{{$i}}" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="exampleModalLabel">Edit title</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form method="post" action="{{route('editions.circuit.link.title.update')}}">
                            @csrf
                            <input type="hidden" name="video_id" value="{{$video->id}}">

                            <div class="modal-body">
                                <div class='input-group mb-2'>
                                    <span class='input-group-text'><i class='fa fa-link'></i></span>
                                    <span class='input-group-text'>{{$video->title}}</span>
                                </div>
                                <div>
                                    <input type="text" name="title" value="{{$video->title}}" class="form-control">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-sm btn-outline-primary"><i class="fa fa-floppy"></i> Update</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Modal Delete -->
            <div class="modal fade" id="modalDelete{{$i}}" tabindex="-1" aria-labelledby="modalDelete{{$i}}" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="exampleModalLabel">Delete link</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form method="post" action="{{route('editions.circuit.link.delete')}}">
                            @csrf
                            <input type="hidden" name="video_id" value="{{$video->id}}">

                            <div class="modal-body">
                                Are you sure to delete {{$video->title}} link?
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i> Delete</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    @stop

    @section('scripts')
        <script>
            $(document).ready(function() {
                $('#addVideo').click(function() {
                    $('#divAddVideo').show();
                    $("<div class='input-group mb-3'>" +
                        "<span class='input-group-text'><i class='fa fa-video'></i></span>" +
                        "<input type='url' class='form-control' name='altri[]'>" +
                    "</div>").appendTo('.altri');
                });
            });
        </script>
    @stop
</x-app-layout>
