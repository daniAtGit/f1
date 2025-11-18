<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Countries
        </h2>
    </x-slot>

    <div class="container-fluid border">
        <div class="card mt-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-10">

                    </div>
                    <div class="col-2 text-end">
                        <form method="post" action="{{route('countries.store')}}">
                            @csrf

                            <div class="input-group">
                                <input type="text" name="name" class="form-control" placeholder="New" aria-label="Tipo" aria-describedby="button-addon2" required>
                                <button class="btn btn-primary" type="submit" id="button-addon2"><i class="fa-solid fa-circle-plus"></i></button>
                            </div>
                        </form>
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
                                <th class="bg-light">Drivers</th>
                                <th class="bg-light">Teams</th>
                                <th class="bg-light">Circuits</th>
                                <th class="bg-light"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($countries as $i => $country)
                                <tr>
                                    <td>{{$country->name}}</td>
                                    <td>{{$country->drivers->count()}}</td>
                                    <td>{{$country->teams->count()}}</td>
                                    <td>{{$country->circuits->count()}}</td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalEdit{{$i}}">
                                            <i class="fa fa-edit"></i>
                                        </button>

                                        @if(!$country->drivers->count() && !$country->circuits->count()  && !$country->teams->count())
                                            <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#modalDelete{{$i}}">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        @else
                                            <button type="button" class="btn btn-sm btn-danger" disabled>
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        @endif
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
        @foreach($countries as $i => $country)
            <div class="modal fade" id="modalDelete{{$i}}" tabindex="-1" aria-labelledby="modalDelete{{$i}}" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="exampleModalLabel">Delete country</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="{{route('countries.destroy',$country)}}" method="post">
                            @csrf
                            @method('DELETE')
                            <div class="modal-body">
                                Are you sure to delete {{$country->name}}?
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i> Delete</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="modalEdit{{$i}}" tabindex="-1" aria-labelledby="modalEdit{{$i}}" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="exampleModalLabel">Edit country</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="{{route('countries.update',$country)}}" method="post">
                            @csrf
                            @method('PATCH')
                            <div class="modal-body">
                                <input type="text" name="name" value="{{$country->name}}" class="form-control">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-sm btn-outline-primary"><i class="fa fa-floppy-disk"></i> Update</button>
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
                            "targets": [1,2,3],
                            "width": "60px",
                            "className": 'dt-center'
                        },
                        {
                            "targets": -1,
                            "width": "80px",
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
