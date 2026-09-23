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
        @elseif($rankingTeamsAdd->isNotEmpty())
            <form method="post" action="{{route('editions.ranking.teams.add')}}">
                @csrf
                <input type="hidden" name="edition_id_add" value="{{$edition->id}}">

                <select name="team_id_add" id="ranking_team_id_add" required>
                    <option value="" disabled selected>Team</option>
                    @foreach($rankingTeamsAdd as $teamAdd)
                        <option value="{{$teamAdd->team->id}}">{{$teamAdd->team->name}}</option>
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
        <table class="table table-hover table-striped table-bordered border" id="tabellaRankingTeams">
            <thead>
                <th class="text-center">Pos.</th>
                <th class="text-center">Pts</th>
                <th>Team</th>
                <th style="width:100px;"></th>
            </thead>
            <tbody>
                @foreach($rankingTeams->sortByDesc(('points')) as $team)
                    <tr>
                        <td class="text-center">
                            {{ $loop->iteration }}
                        </td>
                        <td class="text-center">{{$team->points}}</td>
                        <td>
                            <div style="display:flex;gap:8px;">
                                <badge class="badge" style="background:{{$team->team->color}};width:100px;display:flex;align-items:center;justify-content:flex-start;padding-left:8px;">
                                    <i class="fa fa-car-side"></i>
                                </badge>
                                <span>{{$team->team->name}}</span>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex gap-2">
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

                                <form method="post" action="{{route('editions.ranking.team.delete')}}" onsubmit="return confirm('Remove {{ addslashes($team->team->name) }} from the team ranking?');">
                                    @csrf
                                    <input type="hidden" name="ranking_team_id" value="{{$team->id}}">
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Remove from ranking">
                                        <i class="fa fa-trash"></i>
                                    </button>
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
