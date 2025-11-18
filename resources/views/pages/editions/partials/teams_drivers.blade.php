<div class="row mt-3">
    <div class="col-1"></div>

    <div class="col-10">
        <form method="post" action="{{route('editions.driver.team.create')}}">
            @csrf
            <input type="hidden" name="edition_id" value="{{$edition->id}}">

            <div class="row">
                <div class="col-3">
                    <select name="team_id" id="team_id" class="form-control" required>
                        <option value="" disabled selected>Teams</option>
                        @foreach($teams as $team)
                            <option value="{{$team->id}}">{{$team->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-3">
                    <select name="driver_id" id="driver_id" class="form-control" required>
                        <option value="" disabled selected>Drivers</option>
                        @foreach($drivers as $driver)
                            <option value="{{$driver->id}}">{{$driver->name}}</option>
                        @endforeach
                    </select>
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
                <th>Driver</th>
                <th></th>
            </thead>
            <tbody>
                @foreach($edition->driversTeams as $driverTeam)
                    <tr>
                        <td>
                            <badge class="badge" style="background:{{$driverTeam->team->color}};width:100%;text-align:left;"><i class="fa fa-car-side"></i> {{$driverTeam->team->name}}</badge>
                        </td>
                        <td>{{$driverTeam->driver->name}}</td>
                        <td>
{{--                            @dump($driverTeam->grid)--}}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="col-1"></div>
</div>
