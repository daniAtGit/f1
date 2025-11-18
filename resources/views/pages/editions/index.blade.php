<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Editions
        </h2>
    </x-slot>

    <div class="container-fluid border">
        <div class="card mt-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-6">

                    </div>
                    <div class="col-6 text-end">
                        <a href="{{route('editions.create')}}">
                            <button type="button" class="btn btn-sm btn-outline-primary">
                                <i class="fa-solid fa-circle-plus"></i> New
                            </button>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <br>

        <div class="card mb-4">
            <div class="row mt-3">
                <div class="col-1"></div>

                <div class="col-10">
                    <table class="table table-hover table-striped table-bordered border" id="tabella">
                        <thead>
                            <tr>
                                <th class="bg-light">Edition</th>
                                <th class="bg-light">Year</th>
                                <th class="bg-light">Wiki</th>
                                <th class="bg-light"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($editions as $i => $edition)
                                <tr>
                                    <td>{{$edition->edition}}</td>
                                    <td>{{$edition->year}}</td>
                                    <td>
                                        @if($edition->wikipedia)
                                            <a href="{{$edition->wikipedia}}" target="_blank" title="Wikipedia"><i class="fa-brands fa-wikipedia-w px-1"></i></a>
                                        @else
                                            <i class="fa-brands fa-wikipedia-w text-secondary px-1" title="No Wikipedia"></i>
                                        @endif
                                    </td>
                                    <td>
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
                            "targets": -1,
                            "width": "120px",
                            "className": 'dt-center',
                            'orderable': false
                        },
                    ],
                    "order": [[0, 'desc']]
                });
            });
        </script>
    @stop
</x-app-layout>
