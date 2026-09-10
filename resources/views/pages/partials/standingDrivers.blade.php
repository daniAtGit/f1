@foreach($standingDrivers as $i => $standingDriver)
    <div class="row mb-1 pb-1" style="border-bottom:1px solid #eee;">
        <div class="col-12">
            <div class="row align-items-center gx-2">
            <div class="col-auto">
                <div style="width:30px;height:30px;line-height:30px;text-align:center;border:1px solid #ccc;" class="h5 mb-0">
                    @if($i+1 == 1)
                        <i class="fa-solid fa-trophy text-warning"></i>
                    @else
                        {{$i+1}}
                    @endif
                </div>
            </div>
            <div class="col-auto">
                <div style="width:30px;height:30px;line-height:30px;text-align:center;border:1px solid #ccc;" class="h5 mb-0">
                    {{
                        $standingDriver->driver->driverTeams
                            ->firstWhere('team_id', $standingDriver->team_id)
                            ?->number
                    }}
                </div>
            </div>
            <div class="col-auto">
                @if($standingDriver->driver->country?->flag_icon_url)
                    <span style="width:30px;height:30px;padding:3px;border:1px solid #ccc;display:inline-flex;align-items:center;justify-content:center;">
                        <img src="{{ $standingDriver->driver->country->flag_icon_url }}" alt="{{ $standingDriver->driver->country->name }}" title="{{ $standingDriver->driver->country->name }}" width="20" height="14" style="object-fit:cover;">
                    </span>
                @endif
            </div>
            <div class="col">
                <div class="h5 mb-1"><a href="{{route('driver.single', $standingDriver->driver)}}"><x-driver-name :driver="$standingDriver->driver" :show-flag="false" /></a></div>
                <badge class="badge" style="background:{{$standingDriver->team->color}};max-width:100%;white-space:normal;text-align:center;">{{$standingDriver->team->name}}</badge>
            </div>
            <div class="col-auto text-end">
                <div class="h5">{{$standingDriver->points}}</div>
                @php($pointsDifference = (float) $standingDrivers->first()->points - (float) $standingDriver->points)
                @if($i > 0 && $pointsDifference <= 200)
                    <div class="small">-{{$pointsDifference}}</div>
                @endif
            </div>
            </div>
        </div>
    </div>
@endforeach
