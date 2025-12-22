<x-app-layout>
    <x-slot name="header">
        <div class="row">
            <div class="col-8">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Teams
                </h2>
            </div>
            <div class="col-4 text-end">
                <a href="{{route('teams.create')}}">
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
                                <th class="bg-light">Name</th>
                                <th class="bg-light">Color</th>
                                <th class="bg-light">Country</th>
                                <th class="bg-light">Wiki</th>
                                <th class="bg-light"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($teams as $i => $team)
                                <tr>
                                    <td>{{$team->name}}</td>
                                    <td><badge class="badge" style="background:{{$team->color}};width:150px;text-align:left;"><i class="fa fa-car-side"></i></badge></td>
                                    <td>{{$team->country?->name}}</td>
                                    <td>
                                        @if($team->wikipedia)
                                            <a href="{{$team->wikipedia}}" target="_blank" title="Wikipedia"><i class="fa-brands fa-wikipedia-w px-1"></i></a>
                                        @else
                                            <i class="fa-brands fa-wikipedia-w text-secondary px-1" title="No Wikipedia"></i>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{route('teams.show',$team)}}" class="btn btn-sm btn-outline-info">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>

                                        <a href="{{route('teams.edit',$team)}}" class="btn btn-sm btn-outline-primary">
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


    @section('modal')
        <!-- Modal Delete -->
        @foreach($teams as $i => $team)
            <div class="modal fade" id="modalDelete{{$i}}" tabindex="-1" aria-labelledby="modalDelete{{$i}}" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="exampleModalLabel">Delete team</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="{{route('teams.destroy',$team)}}" method="post">
                            @csrf
                            @method('DELETE')
                            <div class="modal-body">
                                Are you sure to delete {{$team->name}} team?
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
                            "targets": 1,
                            "width": "150px",
                            'orderable': false
                        },
                        {
                            "targets": 2,
                            "width": "200px",
                        },
                        {
                            "targets": 3,
                            "width": "60px",
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
                    "order": [[0, 'asc']]
                });
            });
        </script>
    @stop
</x-app-layout>
