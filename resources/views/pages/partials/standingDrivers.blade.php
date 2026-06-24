@foreach($standingDrivers as $i => $standingDriver)
    <div class="row mb-1 pb-1" style="border-bottom:1px solid #eee;">
        <div class="col-12 d-flex align-items-center gap-2">
            <div class="col-1">
                <div style="width:30px;height:30px;line-height:30px;text-align:center;border:1px solid #ccc;" class="h5">
                    @if($i+1 == 1)
                        <i class="fa-solid fa-trophy text-warning"></i>
                    @else
                        {{$i+1}}
                    @endif
                </div>
            </div>
            <div class="col-1">
                <div style="width:30px;height:30px;line-height:30px;text-align:center;border:1px solid #ccc;" class="h5">
                    {{
                        $standingDriver->driver->driverTeams
                            ->firstWhere('team_id', $standingDriver->team_id)
                            ?->number
                    }}
                </div>
            </div>
            <div class="col-5 h5"><a href="{{route('driver.single', $standingDriver->driver)}}">{{$standingDriver->driver->name}}</a></div>
            <div class="col-3 overflow-x-hidden">
                <badge class="badge" style="background:{{$standingDriver->team->color}};">{{$standingDriver->team->name}}</badge>
            </div>
            <div class="col-2 h5">{{$standingDriver->points}}</div>
        </div>
    </div>
@endforeach
