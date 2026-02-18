<x-app-layout>
    <x-slot name="header">
        <div class="row">
            <div class="col-10">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Editions
                </h2>
            </div>
            <div class="col-2 text-end">
                <a href="{{route('editions.create')}}">
                    <button type="button" class="btn btn-sm btn-outline-primary">
                        <i class="fa-solid fa-circle-plus"></i> New
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
                    <table class="table table-hover table-striped table-bordered border" id="tabella">
                        <thead>
                            <tr>
                                <th class="bg-light">Edition</th>
                                <th class="bg-light">Year</th>
                                <th class="bg-light">Leading team</th>
                                <th class="bg-light">Leading driver</th>
                                <th class="bg-light">Wiki</th>
                                <th class="bg-light"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($editions as $i => $edition)
                                <tr>
                                    <td>{{$edition->edition}}</td>
                                    <td>{{$edition->year}}</td>
                                    <td></td>
                                    <td></td>
                                    <td>
                                        @if($edition->wikipedia)
                                            <a href="{{$edition->wikipedia}}" target="_blank" title="Wikipedia"><i class="fa-brands fa-wikipedia-w px-1"></i></a>
                                        @else
                                            <i class="fa-brands fa-wikipedia-w text-secondary px-1" title="No Wikipedia"></i>
                                        @endif
                                    </td>
                                    <td>
                                        <button  type="button" class="btn btn-sm btn-outline-info offcanvasModal" data-bs-toggle="offcanvas" data-bs-target="#offcanvasBottom" aria-controls="offcanvasBottom" id="{{$edition->id}}">
                                            <i class="fa fa-eye"></i>
                                        </button>

                                        <a href="{{route('editions.edit',$edition)}}" class="btn btn-sm btn-outline-primary">
                                            <i class="fa-solid fa-edit"></i>
                                        </a>

                                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#modalDelete{{$i}}">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="col-1"></div>
            </div>
        </div>
    </div>

    <div class="offcanvas offcanvas-bottom" tabindex="-1" id="offcanvasBottom" aria-labelledby="offcanvasBottomLabel">
        <div class="offcanvas-header">
            <span class="offcanvas-title h3" id="offcanvasTitle"></span>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body small">
            <ul class="nav nav-tabs" id="info" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#grid" type="button" role="tab" aria-controls="grid" aria-selected="false" title="Drivers standing">
                        <i class="fa-solid fa-gamepad"></i>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#race" type="button" role="tab" aria-controls="result" aria-selected="false" title="Teams standing">
                        <i class="fa-solid fa-users-between-lines"></i>
                    </button>
                </li>
            </ul>

            <div class="tab-content mt-3" id="infoContent">
                <div class="tab-pane fade show active" id="grid" role="tabpanel" aria-labelledby="grid">
                    <span class="h3">Drivers standing</span>
                    <div class="row mb-1 my-3">
{{--                        <div class="col-2 d-flex align-items-center gap-2">--}}
{{--                            <div style="width:30px;text-align:center;" title="Position">P</div>--}}
{{--                            <div style="width:30px;text-align:center;" title="Number">N</div>--}}
{{--                            <span>Team</span>--}}
{{--                        </div>--}}
{{--                        <div class="col-7">Driver</div>--}}
{{--                        <div class="col-2 text-end">Time</div>--}}
                    </div>
                    <div class="my-3" id="driversStandingList">
                        @include('pages.editions.partials.drivers_standing')
                    </div>
                </div>
                <div class="tab-pane fade" id="race" role="tabpanel" aria-labelledby="result">
                    <span class="h3">Teams standing</span>
                    <div class="row mb-1 my-3">
{{--                        <div class="col-2 d-flex align-items-center gap-2">--}}
{{--                            <div style="width:30px;text-align:center;" title="Position">P</div>--}}
{{--                            <div style="width:30px;text-align:center;" title="Number">N</div>--}}
{{--                            <span>Team</span>--}}
{{--                        </div>--}}
{{--                        <div class="col-7">Driver</div>--}}
{{--                        <div class="col-2 text-end">Time</div>--}}
                    </div>
                    <div class="my-3"  id="teamsStandingList">
                        @include('pages.editions.partials.teams_standing')
                    </div>
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

    @section('modal')
        <!-- Modal Delete -->
        @foreach($editions as $i => $edition)
            <div class="modal fade" id="modalDelete{{$i}}" tabindex="-1" aria-labelledby="modalDelete{{$i}}" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="exampleModalLabel">Delete edition</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="{{route('editions.destroy',$edition)}}" method="post">
                            @csrf
                            @method('DELETE')
                            <div class="modal-body">
                                Are you sure to delete {{$edition->edition}} edition?
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i> Delete</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    @stop

    @section('scripts')
        <script>
            $(document).ready(function() {
                $('#tabella').dataTable({
                    "responsive": true,
                    "bSort":true,
                    "pageLength": 10,
                    "paging": true,
                    "bPaginate":true,
                    "pagingType":"full_numbers",
                    "columnDefs": [
                        {
                            "targets": [0,1],
                            "width": "70px",
                            "className": 'dt-center',
                            'orderable': false
                        },
                        {
                            "targets": 4,
                            "width": "20px",
                            "className": 'dt-center',
                            'orderable': false
                        },
                        {
                            "targets": -1,
                            "width": "100px",
                            "className": 'dt-center',
                            'orderable': false
                        },
                    ],
                    "order": [[0, 'desc']]
                });
            });

            $('.offcanvasModal').on('click', function(){
                $.post(
                    "{{route('editions.show')}}",
                    {
                        _token: '{{csrf_token()}}',
                        editionId: this.id
                    },
                    function (data) {
                        $('#offcanvasTitle').text(data.title);
                        console.log(data);
                        $('#standingDriversList').html('');
                        $('#standingTeamsList').html('');

                        // DRIVERS
                        let drivers = Object.values(data.standingDrivers);
                        drivers.sort((a, b) => Number(b.points) - Number(a.points));
                        let standingDriversHtml = '';
                        let i = 1;
                        drivers.forEach(function (driver) {
                            standingDriversHtml += `
                                    <div class="row mb-1 pb-1" style="border-bottom:1px solid #eee;">
                                        <div class="col-11 d-flex align-items-center gap-2">
                                            <div class="col-1">
                                                <div style="width:30px;height:30px;line-height:30px;text-align:center;border:1px solid #ccc;" class="h5">
                                                    ${i}
                                                </div>
                                            </div>
                                            <div class="col-5 h5">${driver.driver.name}</div>
                                            <div class="col-1 h5">${driver.driver.country.acronym}</div>
                                            <div class="col-5 h5">${driver.team.name}</div>
                                        </div>
                                        <div class="col-1 d-flex align-items-center gap-2">
                                            <div style="width:80px;height:30px;line-height:30px;text-align:center;" class="h5">
                                                ${driver.points}
                                            </div>
                                        </div>
                                    </div>
                                `;
                            i++;
                        });
                        $('#standingDriversList').html(standingDriversHtml);


                        // TEAM
                        let teams = Object.values(data.standingTeams);
                        teams.sort((a, b) => Number(b.points) - Number(a.points));
                        let standingTeamsHtml = '';
                        let t = 1;
                        teams.forEach(function (team) {
                            standingTeamsHtml += `
                                    <div class="row mb-1 pb-1" style="border-bottom:1px solid #eee;">
                                        <div class="col-11 d-flex align-items-center gap-2">
                                            <div class="col-1">
                                                <div style="width:30px;height:30px;line-height:30px;text-align:center;border:1px solid #ccc;" class="h5">
                                                    ${t}
                                                </div>
                                            </div>
                                            <div class="col-11 h5">${team.team.name}</div>
                                        </div>
                                        <div class="col-1 d-flex align-items-center gap-2">
                                            <div style="width:80px;height:30px;line-height:30px;text-align:center;" class="h5">
                                                ${team.points}
                                            </div>
                                        </div>
                                    </div>
                                `;
                            t++;
                        });
                        $('#standingTeamsList').html(standingTeamsHtml);
                    },
                );
            });
        </script>
    @stop
</x-app-layout>
