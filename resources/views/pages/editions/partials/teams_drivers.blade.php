<div class="row mt-3">
    <div class="col-1"></div>

    <div class="col-10">
        <form method="post" action="{{route('editions.driver.team.create')}}">
            @csrf
            <input type="hidden" name="edition_id" value="{{$edition->id}}">

            <div class="row">
                <div class="col-2">
                    <select name="team_id" id="team_id" class="form-control" required>
                        <option value="" disabled selected>Team</option>
                        @foreach($teams as $team)
                            <option value="{{$team->id}}">{{$team->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-3">
                    <select name="car_id" id="car_id" class="form-control">
                        <option value="" disabled selected>Car</option>

                    </select>
                </div>
                <div class="col-3">
                    <select name="driver_id" id="driver_id" class="form-control" required>
                        <option value="" disabled selected>Driver</option>
                        @foreach($drivers as $driver)
                            <option value="{{$driver->id}}">{{$driver->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-1">
                    <input type="number" name="number" class="form-control" placeholder="Number" min="1">
                </div>

                <div class="col-3">
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
        <table class="table table-hover table-striped table-bordered border" id="tabellaTeamsDrivers">
            <thead>
                <th>Team</th>
                <th class="text-nowrap" style="width:1%;">Car</th>
                <th title="Number">N.</th>
                <th>Driver</th>
                <th></th>
            </thead>
            <tbody>
                @foreach($edition->driversTeams as $driverTeam)
                    <tr>
                        <td>
                            <badge class="badge" style="background:{{$driverTeam->team->color}};width:100%;text-align:left;"><i class="fa fa-car-side"></i> {{$driverTeam->team->name}}</badge>
                        </td>
                        <td class="text-nowrap" style="width:1%;">
                            <div>{{$driverTeam->car?->name}}</div>
                            @if($driverTeam->car?->edition)
                                <div class="text-muted" style="font-size:0.65rem;line-height:1;">{{$driverTeam->car->edition->year}}</div>
                            @endif
                        </td>
                        <td>
                            <div style="width:25px;height:25px;line-height:25px;text-align:center;border:1px solid #ccc;">
                                {{$driverTeam->number}}
                            </div>
                        </td>
                        <td><x-driver-name :driver="$driverTeam->driver" /></td>
                        <td>
                            <div class="d-flex align-items-center justify-content-center gap-2">
                                <button type="button"
                                        class="btn btn-sm btn-outline-primary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalDriverTeamEdit"
                                        data-driver-team-id="{{$driverTeam->id}}"
                                        data-team-id="{{$driverTeam->team_id}}"
                                        data-team-name="{{$driverTeam->team->name}}"
                                        data-team-color="{{$driverTeam->team->color}}"
                                        data-car-id="{{$driverTeam->car_id}}"
                                        data-driver-name="{{$driverTeam->driver->name}}"
                                        data-number="{{$driverTeam->number}}">
                                    <i class="fa-solid fa-edit"></i>
                                </button>

                                <form method="post" action="{{route('editions.driver.team.delete')}}">
                                    @csrf
                                    <input type="hidden" name="edition_id" value="{{$edition->id}}">
                                    <input type="hidden" name="driver_team_id" value="{{$driverTeam->id}}">

                                    @if($driverTeam->gridCircuits->count() || $driverTeam->sprintCircuits->count() || $driverTeam->raceCircuits->count())
                                        <i class="fa-solid fa-trash text-secondary"></i>
                                    @else
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    @endif
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="col-1"></div>
</div>

<div class="modal fade" id="modalDriverTeamEdit" tabindex="-1" aria-labelledby="modalDriverTeamEditLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" action="{{route('editions.driver.team.update')}}">
                @csrf
                <input type="hidden" name="edition_id" value="{{$edition->id}}">
                <input type="hidden" name="driver_team_id" id="edit_driver_team_id">

                <div class="modal-header">
                    <h5 class="modal-title" id="modalDriverTeamEditLabel">Edit team/driver</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Team</label>
                        <div>
                            <span id="edit_team_badge" class="badge d-block p-2 text-start">
                                <i class="fa fa-car-side"></i>
                                <span id="edit_team_name"></span>
                            </span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="edit_driver_name" class="form-label">Driver</label>
                        <input type="text" id="edit_driver_name" class="form-control" readonly>
                    </div>

                    <div class="mb-3">
                        <label for="edit_car_id" class="form-label">Car</label>
                        <select name="car_id" id="edit_car_id" class="form-control">
                            <option value="">Car</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="edit_number" class="form-label">Number</label>
                        <input type="number" name="number" id="edit_number" class="form-control" min="1">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="fa-solid fa-floppy-disk"></i> Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
