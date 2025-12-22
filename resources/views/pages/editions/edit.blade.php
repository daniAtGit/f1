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

                </ul>

                <div class="tab-content mt-3" id="myTabContent">
                    <div class="tab-pane fade show active" id="circuits" role="tabpanel" aria-labelledby="circuits">
                        @include('pages.editions.partials.circuits')
                    </div>
                    <div class="tab-pane fade" id="teams_drivers" role="tabpanel" aria-labelledby="teams_drivers">
                        @include('pages.editions.partials.teams_drivers')
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- grid/sprint/race richiamato da circuits.balde.php -->
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
                    <div class="my-3"  id="raceList"></div>
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
                    <div class="my-3"  id="sprintsList"></div>
                </div>
            </div>
        </div>
    </div>

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
                                                ${grid.driver_team.driver.number}
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
                                                ${raceResult.driver_team.driver.number}
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
                                                ${sprint.driver_team.driver.number}
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
            });
        </script>
    @stop
</x-app-layout>
