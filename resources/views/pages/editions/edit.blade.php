<x-app-layout>
    <x-slot name="header">
        <div class="row">
            <div class="col-10">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Edit edition
                </h2>
            </div>
            <div class="col-2 text-end">
                <a href="{{route('editions.index')}}">
                    <button type="button" class="btn btn-sm btn-outline-secondary">
                        <i class="fa-solid fa-arrow-left"></i> Back
                    </button>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="container-fluid border">
        <div class="card my-4">
            <div class="row mt-3">
                <div class="col-1"></div>

                <div class="col-10">
                    <form method="post" action="{{route('editions.update', $edition)}}">
                        @csrf
                        @method('PATCH')

                        <div class="mb-3">
                            <label for="edition" class="form-label">Edition<span class="text-danger">*</span></label>
                            <input type="number" name="edition" class="form-control" value="{{$edition->edition}}" required>
                        </div>

                        <div class="mb-3">
                            <label for="year" class="form-label">Year<span class="text-danger">*</span></label>
                            <input type="number" name="year" class="form-control" value="{{$edition->year}}" required>
                        </div>

                        <div class="mb-3">
                            <label for="wikipedia" class="form-label">Wikipedia</label>
                            <input type="text" name="wikipedia" class="form-control" value="{{$edition->wikipedia}}">
                        </div>

                        <div class="mb-3 text-end">
                            <button type="submit" class="btn btn-sm btn-outline-primary">
                                <i class="fa-solid fa-floppy-disk"></i> Update
                            </button>
                        </div>
                    </form>
                </div>

                <div class="col-1"></div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="row p-3">
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#circuits" type="button" role="tab" aria-controls="circuits" aria-selected="false" title="Circuits">
                            <i class="fa fa-ring"></i>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#teams_drivers" type="button" role="tab" aria-controls="teams_drivers" aria-selected="true" title="Teams/Drivers">
                            <i class="fa fa-users-between-lines"></i>/<i class="fa fa-gamepad"></i>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#drivers_ranking" type="button" role="tab" aria-controls="drivers_ranking" aria-selected="true" title="Drivers ranking">
                            <i class="fa fa-list-ul"></i> Drivers
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#teams_ranking" type="button" role="tab" aria-controls="teams_ranking" aria-selected="true" title="Teams ranking">
                            <i class="fa fa-list-ul"></i> Teams
                        </button>
                    </li>
                </ul>

                <div class="tab-content mt-3" id="myTabContent">
                    <div class="tab-pane fade show active" id="circuits" role="tabpanel" aria-labelledby="circuits">
                        @include('pages.editions.partials.circuits')
                    </div>
                    <div class="tab-pane fade" id="teams_drivers" role="tabpanel" aria-labelledby="teams_drivers">
                        @include('pages.editions.partials.teams_drivers')
                    </div>
                    <div class="tab-pane fade" id="drivers_ranking" role="tabpanel" aria-labelledby="drivers_ranking">
                        @include('pages.editions.partials.drivers_ranking')
                    </div>
                    <div class="tab-pane fade" id="teams_ranking" role="tabpanel" aria-labelledby="teams_ranking">
                        @include('pages.editions.partials.teams_ranking')
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- offcanvas di grid/sprint/race richiamato da circuits.blade.php -->
    <div class="offcanvas offcanvas-bottom" tabindex="-1" id="offcanvasBottom" aria-labelledby="offcanvasBottomLabel">
        <div class="offcanvas-header">
            <span class="offcanvas-title h3" id="offcanvasTitle"></span>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body small">
            <ul class="nav nav-tabs" id="info" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#grid" type="button" role="tab" aria-controls="grid" aria-selected="false" title="Starting Grid">
                        <i class="fa-solid fa-grip-vertical"></i>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#race" type="button" role="tab" aria-controls="result" aria-selected="false" title="Race Result">
                        <i class="fa-solid fa-flag-checkered"></i>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#sprint" type="button" role="tab" aria-controls="sprint" aria-selected="false" title="Sprint Result">
                        <i class="fa-regular fa-flag"></i>
                    </button>
                </li>
            </ul>

            <div class="tab-content mt-3" id="infoContent">
                <div class="tab-pane fade show active" id="grid" role="tabpanel" aria-labelledby="grid">
                    <span class="h3">Starting grid</span>
                    <div class="row mb-1 my-3">
                        <div class="col-2 d-flex align-items-center gap-2">
                            <div style="width:30px;text-align:center;" title="Position">P</div>
                            <div style="width:30px;text-align:center;" title="Number">N</div>
                            <span>Team</span>
                        </div>
                        <div class="col-7">Driver</div>
                        <div class="col-2 text-end">Time</div>
                    </div>
                    <div class="my-3" id="gridList"></div>
                </div>
                <div class="tab-pane fade" id="race" role="tabpanel" aria-labelledby="result">
                    <span class="h3">Race result</span>
                    <div class="row mb-1 my-3">
                        <div class="col-2 d-flex align-items-center gap-2">
                            <div style="width:30px;text-align:center;" title="Position">P</div>
                            <div style="width:30px;text-align:center;" title="Number">N</div>
                            <span>Team</span>
                        </div>
                        <div class="col-7">Driver</div>
                        <div class="col-2 text-end">Time</div>
                    </div>
                    <div class="my-3" id="raceList"></div>
                </div>
                <div class="tab-pane fade" id="sprint" role="tabpanel" aria-labelledby="sprint">
                    <span class="h3">Sprint result</span>
                    <div class="row mb-1 my-3">
                        <div class="col-2 d-flex align-items-center gap-2">
                            <div style="width:30px;text-align:center;" title="Position">P</div>
                            <div style="width:30px;text-align:center;" title="Number">N</div>
                            <span>Team</span>
                        </div>
                        <div class="col-7">Driver</div>
                        <div class="col-2 text-end">Time</div>
                    </div>
                    <div class="my-3" id="sprintsList"></div>
                </div>
            </div>
        </div>
    </div>

    @section('modal')
        <!-- Modal ranking teams -->
        <div class="modal fade" id="modalRankingTeams" tabindex="-1" aria-labelledby="modalRankingTeams" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Update ranking Team</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{route('editions.ranking.team.update')}}" method="post">
                        @csrf
                        <input type="hidden" name="ranking_team_id" id="rankingTeamId" value="">

                        <div class="modal-body">
                            <div>
                                <badge class="badge" id="teamTitle" style="width:200px;text-align:left;">Team</badge>
                                <input type="text" name="team" id="rankingTeam" value="" class="form-control" readonly>
                            </div>
                            <br>

                            <div>
                                <label for="point">Pts</label>
                                <input type="number" name="pts" id="rankingTeamPts" value="" class="form-control">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-sm btn-outline-primary"><i class="fa fa-floppy-disk"></i> Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modalRankingDrivers" tabindex="-1" aria-labelledby="modalRankingDrivers" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Update ranking driver</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{route('editions.ranking.driver.update')}}" method="post">
                        @csrf
                        <input type="hidden" name="ranking_driver_id" id="rankingDriverId" value="">

                        <div class="modal-body">
                            <div>
                                <label for="rankingDriver">Driver</label>
                                <input type="text" name="driver" id="rankingDriver" value="" class="form-control" readonly>
                            </div>
                            <br>

                            <div>
                                <badge class="badge" id="driverTeamTitleDelete" style="width:200px;text-align:left;">Team</badge>
                                <input type="text" name="team" id="rankingDriverTeam" value="" class="form-control" readonly>
                            </div>
                            <br>

                            <div>
                                <label for="rankingDriverPts">Pts</label>
                                <input type="number" name="pts" id="rankingDriverPts" value="" class="form-control">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-sm btn-outline-primary"><i class="fa fa-floppy-disk"></i> Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modalRankingDriversDelete" tabindex="-1" aria-labelledby="modalRankingDriversDelete" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Delete ranking driver</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{route('editions.ranking.driver.delete')}}" method="post">
                        @csrf
                        <input type="hidden" name="ranking_driver_id" id="rankingDriverIdDelete" value="">

                        <div class="modal-body">
                            <div>
                                <label for="rankingDriver">Driver</label>
                                <input type="text" name="driver" id="rankingDriverDelete" value="" class="form-control" readonly>
                            </div>
                            <br>

                            <div>
                                <badge class="badge" id="driverTeamTitle" style="width:200px;text-align:left;">Team</badge>
                                <input type="text" name="team" id="rankingDriverTeamDelete" value="" class="form-control" readonly>
                            </div>
                            <br>

                            <div>
                                <label for="rankingDriverPts">Pts</label>
                                <input type="number" name="pts" id="rankingDriverPtsDelete" value="" class="form-control">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fa fa-trash"></i> Delete</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @stop

    @section('style')
        <style>
            #offcanvasBottom {
                height: 90vh !important; /* metà schermo */
                border-top-left-radius: 1rem;
                border-top-right-radius: 1rem;
            }
        </style>
    @stop

    @section('scripts')
        <script>
            $(document).ready(function() {
                $('#tabellaTeamsDrivers').dataTable({
                    "responsive": true,
                    "autoWidth": false,
                    "bSort":true,
                    "pageLength": 50,
                    "paging": true,
                    "bPaginate":true,
                    "pagingType":"full_numbers",
                    "columnDefs": [
                        {
                            "targets": 0,
                            "width": "200px",
                        },
                        {
                            "targets": 1,
                            "width": "20px",
                            "className": 'dt-center',
                        },
                        {
                            "targets": -1,
                            "width": "30px",
                            "className": 'dt-center',
                            'orderable': false
                        },
                    ],
                    "order": [[0, 'asc']]
                });

                $('#tabellaCircuits').dataTable({
                    "responsive": true,
                    "autoWidth": false,
                    "bSort":true,
                    "pageLength": 50,
                    "paging": true,
                    "bPaginate":true,
                    "pagingType":"full_numbers",
                    "columnDefs": [
                        {
                            "targets": 0,
                            "width": "20px",
                        },
                        {
                            "targets": [4,5],
                            'orderable': false
                        },
                        {
                            "targets": -1,
                            "width": "60px",
                            "className": 'dt-center',
                            'orderable': false
                        }
                    ],
                    "order": [[0, 'asc']]
                });

                $('#tabellaRankingTeams').dataTable({
                    "responsive": true,
                    "autoWidth": false,
                    "bSort":true,
                    "pageLength": 50,
                    "paging": true,
                    "bPaginate":true,
                    "pagingType":"full_numbers",
                    "columnDefs": [
                        {
                            "targets": 0,
                            "width": "30px",
                            "className": 'dt-center',
                        },
                        {
                            "targets": 1,
                            "width": "50px",
                            "className": 'dt-center',
                        },
                        {
                            "targets": -1,
                            "width": "60px",
                            "className": 'dt-center',
                            'orderable': false
                        },
                    ],
                    "order": [[1, 'desc']]
                });

                $('#tabellaRankingDrivers').dataTable({
                    "responsive": true,
                    "autoWidth": false,
                    "bSort":true,
                    "pageLength": 50,
                    "paging": true,
                    "bPaginate":true,
                    "pagingType":"full_numbers",
                    "columnDefs": [
                        {
                            "targets": 0,
                            "width": "30px",
                            "className": 'dt-center',
                        },
                        {
                            "targets": 1,
                            "width": "50px",
                            "className": 'dt-center',
                        },
                        {
                            "targets": -1,
                            "width": "90px",
                            "className": 'dt-center',
                            'orderable': false
                        },
                    ],
                    "order": [[1, 'desc']]
                });

                document.getElementById('modalRankingTeams')
                    .addEventListener('show.bs.modal', function (event) {
                        const button = event.relatedTarget;
                        this.querySelector('#rankingTeamId').value = button.getAttribute('data-ranking-team-id');
                        this.querySelector('#rankingTeam').value = button.getAttribute('data-ranking-team-name');
                        this.querySelector('#rankingTeamPts').value = button.getAttribute('data-ranking-team-pts');
                        this.querySelector('#teamTitle').style.background = button.getAttribute('data-ranking-team-color');;
                    });

                document.getElementById('modalRankingDrivers')
                    .addEventListener('show.bs.modal', function (event) {
                        const button = event.relatedTarget;
                        this.querySelector('#rankingDriverId').value = button.getAttribute('data-ranking-driver-id');
                        this.querySelector('#rankingDriver').value = button.getAttribute('data-ranking-driver-name');
                        this.querySelector('#rankingDriverTeam').value = button.getAttribute('data-ranking-team-name');
                        this.querySelector('#rankingDriverPts').value = button.getAttribute('data-ranking-driver-pts');
                        this.querySelector('#driverTeamTitle').style.background = button.getAttribute('data-ranking-team-color');
                    });

                document.getElementById('modalRankingDriversDelete')
                    .addEventListener('show.bs.modal', function (event) {
                        const button = event.relatedTarget;
                        this.querySelector('#rankingDriverIdDelete').value = button.getAttribute('data-ranking-driver-id');
                        this.querySelector('#rankingDriverDelete').value = button.getAttribute('data-ranking-driver-name');
                        this.querySelector('#rankingDriverTeamDelete').value = button.getAttribute('data-ranking-team-name');
                        this.querySelector('#rankingDriverPtsDelete').value = button.getAttribute('data-ranking-driver-pts');
                        this.querySelector('#driverTeamTitleDelete').style.background = button.getAttribute('data-ranking-team-color');
                    });

                const params = new URLSearchParams(window.location.search);

                if (params.has('tab')) {
                    const tab = params.get('tab'); // es. "circuits"
                    console.log('Parametro tab trovato:', tab);

                    const trigger = document.querySelector('[data-bs-target="#' + tab + '"]');
                    if (trigger) {
                        const tabObj = new bootstrap.Tab(trigger);
                        tabObj.show();
                    }
                }

                $('.offcanvasModal').on('click', function(){
                    $.post(
                        "{{route('editions.circuit.show')}}",
                        {
                            _token: '{{csrf_token()}}',
                            editionCircuitId: this.id
                        },
                        function (data) {
                            $('#offcanvasTitle').text(data.title);

                            // GRID
                            let grids = Object.values(data.grids);
                            grids.sort((a, b) => Number(a.position) - Number(b.position));
                            let gridHtml = '';
                            grids.forEach(function (grid) {
                                gridHtml += `
                                    <div class="row mb-1 pb-1" style="border-bottom:1px solid #eee;">
                                        <div class="col-2 d-flex align-items-center gap-2">
                                            <div style="width:30px;height:30px;line-height:30px;text-align:center;border:1px solid #ccc;" class="h5">
                                                ${grid.position}
                                            </div>
                                            <div style="width:30px;height:30px;line-height:30px;text-align:center;border:1px solid #ccc;" class="h5">
                                                ${grid.driver_team.number}
                                            </div>
                                            <span class="h5" style="background:${grid.driver_team.team.color};width:100%;text-align:left;overflow:hidden;white-space:nowrap;color:#fff;">
                                                <i class="fa fa-car-side mx-1"></i>
                                                ${grid.driver_team.team.name}
                                            </span>
                                        </div>
                                        <div class="col-7 h5">
                                            <b>${grid.driver_team.driver.name}</b>
                                        </div>
                                        <div class="col-2 h5 text-end">
                                            ${grid.time ?? '-'}
                                        </div>
                                    </div>
                                `;
                            });
                            $('#gridList').html(gridHtml);

                            // RACE
                            let raceResults = Object.values(data.raceResults);
                            raceResults.sort((a, b) => Number(a.position) - Number(b.position));
                            let gridRace = '';
                            raceResults.forEach(function (raceResult) {
                                gridRace += `
                                    <div class="row mb-1 pb-1" style="border-bottom:1px solid #eee;">
                                        <div class="col-2 d-flex align-items-center gap-2">
                                            <div style="width:30px;height:30px;line-height:30px;text-align:center;border:1px solid #ccc;" class="h5">
                                                ${raceResult.position}
                                            </div>
                                            <div style="width:30px;height:30px;line-height:30px;text-align:center;border:1px solid #ccc;" class="h5">
                                                ${raceResult.driver_team.number}
                                            </div>
                                            <span class="h5" style="background:${raceResult.driver_team.team.color};width:100%;text-align:left;overflow:hidden;white-space:nowrap;color:#fff;">
                                                <i class="fa fa-car-side mx-1"></i>
                                                ${raceResult.driver_team.team.name}
                                            </span>
                                        </div>
                                        <div class="col-7 h5">
                                            <b>${raceResult.driver_team.driver.name}</b>
                                        </div>
                                        <div class="col-2 h5 text-end">
                                            ${raceResult.time ?? '-'}
                                        </div>
                                    </div>
                                `;
                            });
                            $('#raceList').html(gridRace);

                            // SPRINT
                            let sprints = Object.values(data.sprints);
                            sprints.sort((a, b) => Number(a.position) - Number(b.position));
                            let sprintHtml = '';
                            sprints.forEach(function (sprint) {
                                sprintHtml += `
                                    <div class="row mb-1 pb-1" style="border-bottom:1px solid #eee;">
                                        <div class="col-2 d-flex align-items-center gap-2">
                                            <div style="width:30px;height:30px;line-height:30px;text-align:center;border:1px solid #ccc;" class="h5">
                                                ${sprint.position}
                                            </div>
                                            <div style="width:30px;height:30px;line-height:30px;text-align:center;border:1px solid #ccc;" class="h5">
                                                ${sprint.driver_team.number}
                                            </div>
                                            <span class="h5" style="background:${sprint.driver_team.team.color};width:100%;text-align:left;overflow:hidden;white-space:nowrap;color:#fff;">
                                                <i class="fa fa-car-side mx-1"></i>
                                                ${sprint.driver_team.team.name}
                                            </span>
                                        </div>
                                        <div class="col-7 h5">
                                            <b>${sprint.driver_team.driver.name}</b>
                                        </div>
                                        <div class="col-2 h5 text-end">
                                            ${sprint.time ?? '-'}
                                        </div>
                                    </div>
                                `;
                            });
                            $('#sprintsList').html(sprintHtml);
                        },
                    );
                });

                $('#team_id').on('change', function(){
                    $.post(
                        "{{route('editions.driver.team.cars')}}",
                        {
                            _token: '{{csrf_token()}}',
                            team_id: $('#team_id').val()
                        },
                        function (data) {
                            $('#car_id').empty();
                            $('#car_id').append('<option value="" disabled selected>Car</option>');
                            data.forEach(function (dt) {
                                $('#car_id').append('<option value="'+dt.id+'">'+dt.name+'</option>');
                            });
                        }
                    );
                });
            });
        </script>
    @stop
</x-app-layout>
