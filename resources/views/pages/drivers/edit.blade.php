<x-app-layout>
    <x-slot name="header">
        <div class="row">
            <div class="col-8">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Edit driver
                </h2>
            </div>
            <div class="col-4 text-end">
                <a href="{{route('drivers.index')}}">
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
                    <form method="post" action="{{route('drivers.update', $driver)}}">
                        @csrf
                        @method('PATCH')

                        <div class="mb-3">
                            <label for="name" class="form-label">Name<span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{$driver->name}}" required>
                        </div>

                        <div class="mb-3">
                            <label for="number" class="form-label">Number<span class="text-danger">*</span></label>
                            <input type="number" name="number" class="form-control" value="{{$driver->number}}" required>
                        </div>

                        <div class="mb-3">
                            <label for="birth_year" class="form-label">Birth year</label>
                            <input type="number" name="birth_year" class="form-control" value="{{$driver->birth_year}}">
                        </div>

                        <div class="mb-3">
                            <label for="nation" class="form-label">Country</label>
                            <select name="country" class="form-control">
                                <option value=""></option>
                                @foreach($countries as $country)
                                    <option value="{{$country->id}}" @selected($country->id == $driver->country_id)>{{$country->name}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="wikipedia" class="form-label">Wikipedia</label>
                            <input type="text" name="wikipedia" class="form-control" value="{{$driver->wikipedia}}">
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
