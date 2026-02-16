<x-app-layout>
    <x-slot name="header">
        <div class="row">
            <div class="col-8">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Modify car
                </h2>
            </div>
            <div class="col-4 text-end">
                <a href="{{route('cars.index')}}">
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
                    <form method="post" action="{{route('cars.update', $car)}}">
                        @csrf
                        @method('PATCH')

                        <div class="mb-3">
                            <label for="name" class="form-label">Name<span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{$car->name}}" required>
                        </div>

                        <div class="mb-3">
                            <label for="team_id" class="form-label">Team<span class="text-danger">*</span></label>
                            <select name="team_id" class="form-control" required>
                                <option value=""></option>
                                @foreach($teams as $team)
                                    <option value="{{$team->id}}" @selected($team->id == $car->team_id)>{{$team->name}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="edition_id" class="form-label">Edition<span class="text-danger">*</span></label>
                            <select name="edition_id" class="form-control" required>
                                <option value=""></option>
                                @foreach($editions as $edition)
                                    <option value="{{$edition->id}}" @selected($edition->id == $car->edition_id)>{{$edition->edition}} - {{$edition->year}}</option>
                                @endforeach
                            </select>
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
    </div>

    @section('scripts')
        <script>
            $(document).ready(function() {
                //
            });
        </script>
    @stop
</x-app-layout>
