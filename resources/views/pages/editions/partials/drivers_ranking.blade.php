<div class="row mt-3">
    <div class="col-1"></div>

    <div class="col-10">
        @if(!$edition->rankingDrivers->count())
            <form method="post" action="{{route('editions.ranking.drivers.create')}}">
                @csrf
                <input type="hidden" name="edition_id" value="{{$edition->id}}">

                <span class="text-sm text-info"><i>-NB: Make sure you have entered all the teams, then create list.-</i></span>
                <br>
                <button type="submit" class="btn btn-sm btn-outline-primary">
                    <i class="fa fa-list-ul"></i> Create
                </button>
            </form>
        @else
            <form method="post" action="{{route('editions.ranking.drivers.add')}}">
                @csrf
                <input type="hidden" name="edition_id_add" value="{{$edition->id}}">

                <select name="team_id_add" id="team_id_add">
                    <option value="" disabled selected>Team</option>
                        @foreach($rankingDriversAdd as $driverAdd)
                            <option value="{{$driverAdd->team->id}}">{{$driverAdd->team->name}}</option>
                        @endforeach
                </select>

                <select name="driver_id_add" id="driver_id_add">
                    <option value="" disabled selected>Driver</option>
                    @foreach($rankingDriversAdd as $driverAdd)
                        <option value="{{$driverAdd->driver->id}}">{{$driverAdd->driver->name}}</option>
                    @endforeach
                </select>

                <button type="submit" class="btn btn-outline-primary">
                    <i class="fa fa-floppy-disk"></i> Add
                </button>
            </form>
        @endif
    </div>

    <div class="col-1"></div>
</div>

<div class="row mt-3">
    <div class="col-1"></div>

    <div class="col-10">
        <table class="table table-hover table-striped table-bordered border" id="tabellaRankingDrivers">
            <thead>
                <th>Pos.</th>
                <th>Pts</th>
                <th>Driver</th>
                <th>Country</th>
                <th>Team</th>
                <th></th>
            </thead>
            <tbody>
                @foreach($rankingDrivers as $p => $driver)
                    <tr>
                        <td>
                            {{$p+1}}
                        </td>
                        <td>{{$driver->points}}</td>
                        <td>{{$driver->driver->name}}</td>
                        <td>{{$driver->driver->country->name}}</td>
                        <td>
                            <div style="display:flex;gap:8px;">
                                <badge class="badge" style="background:{{$driver->team->color}};width:100px;display:flex;align-items:center;justify-content:flex-start;padding-left:8px;">
                                    <i class="fa fa-car-side"></i>
                                </badge>
                                <span>{{$driver->team->name}}</span>
                            </div>
                        </td>
                        <td>
                            <button type="button"
                                    class="btn btn-sm btn-outline-primary"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalRankingDrivers"
                                    data-ranking-driver-id="{{ $driver->id }}"
                                    data-ranking-driver-name="{{ $driver->driver->name }}"
                                    data-ranking-team-name="{{ $driver->team->name }}"
                                    data-ranking-team-color="{{ $driver->team->color }}"
                                    data-ranking-driver-pts="{{ $driver->points }}">
                                <i class="fa fa-edit"></i>
                            </button>
                            |
                            <button type="button"
                                    class="btn btn-sm btn-outline-danger"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalRankingDriversDelete"
                                    data-ranking-driver-id="{{ $driver->id }}"
                                    data-ranking-driver-name="{{ $driver->driver->name }}"
                                    data-ranking-team-name="{{ $driver->team->name }}"
                                    data-ranking-team-color="{{ $driver->team->color }}"
                                    data-ranking-driver-pts="{{ $driver->points }}">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="col-1"></div>
</div>
