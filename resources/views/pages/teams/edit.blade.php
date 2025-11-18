<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Edit team
        </h2>
    </x-slot>

    <div class="container-fluid border">
        <div class="card mt-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-6">

                    </div>
                    <div class="col-6 text-end">
                        <a href="{{route('teams.index')}}">
                            <button type="button" class="btn btn-sm btn-outline-secondary">
                                <i class="fa-solid fa-arrow-left"></i> Back
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
                    <form method="post" action="{{route('teams.update', $team)}}">
                        @csrf
                        @method('PATCH')

                        <div class="mb-3">
                            <label for="name" class="form-label">Name<span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{$team->name}}" required>
                        </div>

                        <div class="mb-3">
                            <label for="color" class="form-label">Color</label>
                            <input type="color" name="color" class="form-control" value="{{$team->color}}">
                        </div>

                        <div class="mb-3">
                            <label for="nation" class="form-label">Country</label>
                            <select name="country" class="form-control">
                                <option value=""></option>
                                @foreach($countries as $country)
                                    <option value="{{$country->id}}" @selected($country->id == $team->country_id)>{{$country->name}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="wikipedia" class="form-label">Wikipedia</label>
                            <input type="text" name="wikipedia" class="form-control" value="{{$team->wikipedia}}">
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
