@foreach($standingTeams as $s => $standingTeam)
    <div class="row mb-1 pb-1" style="border-bottom:1px solid #eee;">
        <div class="col-12 d-flex align-items-center gap-2">
            <div class="col-1">
                <div style="width:30px;height:30px;line-height:30px;text-align:center;border:1px solid #ccc;" class="h5">
                    @if($s+1 == 1)
                        <i class="fa-solid fa-trophy text-warning"></i>
                    @else
                        {{$s+1}}
                    @endif
                </div>
            </div>
            <div class="col-10 h5"><a href="{{route('team.single', $standingTeam->team)}}">{{$standingTeam->team->name}}</a></div>
            <div class="col-1 h5">{{$standingTeam->points}}</div>
        </div>
    </div>
@endforeach
