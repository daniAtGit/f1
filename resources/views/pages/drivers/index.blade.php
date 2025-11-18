<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Drivers
        </h2>
    </x-slot>

    <div class="container-fluid border">
        <div class="card mt-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-6">

                    </div>
                    <div class="col-6 text-end">
                        <a href="{{route('drivers.create')}}">
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
                                <th class="bg-light">Name</th>
                                <th class="bg-light">Number</th>
                                <th class="bg-light">BirthYear</th>
                                <th class="bg-light">Country</th>
                                <th class="bg-light">Wiki</th>
                                <th class="bg-light"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($drivers as $i => $driver)
                                <tr>
                                    <td>{{$driver->name}}</td>
                                    <td>{{$driver->number}}</td>
                                    <td>{{$driver->birth_year}}</td>
                                    <td>{{$driver->country?->name}}</td>
                                    <td>
                                        @if($driver->wikipedia)
                                            <a href="{{$driver->wikipedia}}" target="_blank" title="Wikipedia"><i class="fa-brands fa-wikipedia-w px-1"></i></a>
                                        @else
                                            <i class="fa-brands fa-wikipedia-w text-secondary px-1" title="No Wikipedia"></i>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{route('drivers.show',$driver)}}" class="btn btn-sm btn-outline-info">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                        <a href="{{route('drivers.edit',$driver)}}" class="btn btn-sm btn-outline-primary">
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
        @foreach($drivers as $i => $driver)
            <div class="modal fade" id="modalDelete{{$i}}" tabindex="-1" aria-labelledby="modalDelete{{$i}}" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="exampleModalLabel">Delete driver</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="{{route('drivers.destroy',$driver)}}" method="post">
                            @csrf
                            @method('DELETE')
                            <div class="modal-body">
                                Are you sure to delete {{$driver->name}}?
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
                    // "language": {
                    //     "lengthMenu": "Mostra _MENU_ record",
                    //     "zeroRecords": "Nessun risultato",
                    //     "info": "Pagina _PAGE_ di _PAGES_",
                    //     "infoEmpty": "Nessun risultato disponibile",
                    //     "infoFiltered": "(filtro di  _MAX_ record totali)",
                    //     "search": "",
                    //     "searchPlaceholder": "Cerca...",
                    //     "paginate": {
                    //         first:      '<<',
                    //         previous:   '‹',
                    //         next:       '›',
                    //         last:       '>>'
                    //     },
                    // },
                    "columnDefs": [
                        {
                            "targets": 1,
                            "width": "60px",
                            "className": 'dt-center'
                        },
                        {
                            "targets": 2,
                            "width": "80px",
                            "className": 'dt-center'
                        },
                        {
                            "targets": 3,
                            "width": "150px",
                        },
                        {
                            "targets": 4,
                            "width": "40px",
                            "className": 'dt-center'
                        },
                        {
                            "targets": -1,
                            "width": "120px",
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
