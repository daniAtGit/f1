<div class="row mt-3">
    <div class="col-1"></div>

    <div class="col-10">
        @if(!$edition->rankingTeams->count())
            <form method="post" action="{{route('editions.ranking.teams.create')}}">
                @csrf
                <input type="hidden" name="edition_id" value="{{$edition->id}}">

                <span class="text-sm text-info"><i>-NB: Make sure you have entered all the teams, then create list.-</i></span>
                <br>
                <button type="submit" class="btn btn-sm btn-outline-primary">
                    <i class="fa fa-list-ul"></i> Create
                </button>
            </form>
        @endif
    </div>

    <div class="col-1"></div>
</div>

<div class="row mt-3">
    <div class="col-1"></div>

    <div class="col-10">
        <table class="table table-hover table-striped table-bordered border" id="tabellaRankingTeams">
            <thead>
                <th>Pos.</th>
                <th>Pts</th>
                <th>Team</th>
                <th></th>
            </thead>
            <tbody>
                @foreach($rankingTeams as $p => $team)
                    <tr>
                        <td>
                            {{$p+1}}
                        </td>
                        <td>{{$team->points}}</td>
                        <td>
                            <div style="display:flex;gap:8px;">
                                <badge class="badge" style="background:{{$team->team->color}};width:100px;display:flex;align-items:center;justify-content:flex-start;padding-left:8px;">
                                    <i class="fa fa-car-side"></i>
                                </badge>
                                <span>{{$team->team->name}}</span>
                            </div>
                        </td>
                        <td>
                            <button type="button"
                                    class="btn btn-sm btn-outline-primary"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalRankingTeams"
                                    data-ranking-team-id="{{ $team->id }}"
                                    data-ranking-team-name="{{ $team->team->name }}"
                                    data-ranking-team-pts="{{ $team->points }}"
                                    data-ranking-team-color="{{ $team->team->color }}">
                                <i class="fa fa-edit"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="col-1"></div>
</div>
