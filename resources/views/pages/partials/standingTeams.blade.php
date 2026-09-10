@foreach($standingTeams as $s => $standingTeam)
    <div class="row mb-1 pb-1" style="border-bottom:1px solid #eee;">
        <div class="col-12">
            <div class="row align-items-center gx-2">
            <div class="col-auto">
                <div style="width:30px;height:30px;line-height:30px;text-align:center;border:1px solid #ccc;" class="h5 mb-0">
                    @if($s+1 == 1)
                        <i class="fa-solid fa-trophy text-warning"></i>
                    @else
                        {{$s+1}}
                    @endif
                </div>
            </div>
            <div class="col-auto">
                @if($standingTeam->team->country?->flag_icon_url)
                    <span style="width:30px;height:30px;padding:3px;border:1px solid #ccc;display:inline-flex;align-items:center;justify-content:center;">
                        <img src="{{ $standingTeam->team->country->flag_icon_url }}" alt="{{ $standingTeam->team->country->name }}" title="{{ $standingTeam->team->country->name }}" width="20" height="14" style="object-fit:cover;">
                    </span>
                @endif
            </div>
            <div class="col h4 mb-0">
                <a href="{{route('team.single', $standingTeam->team)}}">
                    <badge class="badge" style="background:{{$standingTeam->team->color}};max-width:100%;white-space:normal;text-align:center;">{{$standingTeam->team->name}}</badge>
                </a>
            </div>
            <div class="col-auto text-end">
                <div class="h5 mb-0">{{$standingTeam->points}}</div>
                @php($pointsDifference = (float) $standingTeams->first()->points - (float) $standingTeam->points)
                @if($s > 0 && $pointsDifference <= 200)
                    <div class="small" style="margin-top:-4px;">-{{$pointsDifference}}</div>
                @endif
            </div>
            </div>
        </div>
    </div>
@endforeach
