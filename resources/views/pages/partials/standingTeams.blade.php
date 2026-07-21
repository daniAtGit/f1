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
            <div class="col-9 h4">
                <a href="{{route('team.single', $standingTeam->team)}}">
                    <badge class="badge" style="background:{{$standingTeam->team->color}};">{{$standingTeam->team->name}}</badge>
                </a>
            </div>
            <div class="col-1 text-end">
                <div class="h5 mb-0">{{$standingTeam->points}}</div>
                @php($pointsDifference = $standingTeams->first()->points - $standingTeam->points)
                @if($s > 0 && $pointsDifference <= 200)
                    <div class="small" style="margin-top:-4px;">-{{$pointsDifference}}</div>
                @endif
            </div>
            <div class="col-1"></div>
        </div>
    </div>
@endforeach
