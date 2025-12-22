<x-app-layout>
    <x-slot name="header">
        <div class="row">
            <div class="col-10">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    New edition
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
                    <form method="post" action="{{route('editions.store')}}">
                        @csrf

                        <div class="mb-3">
                            <label for="edition" class="form-label">Edition<span class="text-danger">*</span></label>
                            <input type="number" name="edition" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="year" class="form-label">Year<span class="text-danger">*</span></label>
                            <input type="number" name="year" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="wikipedia" class="form-label">Wikipedia</label>
                            <input type="text" name="wikipedia" class="form-control">
                        </div>

                        <div class="mb-3 text-end">
                            <button type="submit" class="btn btn-sm btn-outline-primary">
                                <i class="fa-solid fa-floppy-disk"></i> Save
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
