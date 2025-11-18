<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Edit edition/circuit
        </h2>
    </x-slot>

    <div class="container-fluid border">
        <div class="card mt-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-6">

                    </div>
                    <div class="col-6 text-end">
                        <a href="javascript:history.back();">
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
                    <form method="post" action="{{route('editions.circuit.update')}}">
                        @csrf
                        <input type="hidden" name="editionCircuitId" value="{{$editionCircuit->id}}">

                        <div class="row">
                            <div class="col-4">
                                <select name="circuit_id" id="circuit_id" class="form-control" required>
                                    <option value="" disabled selected>Select circuit</option>
                                    @foreach($circuits as $circuit)
                                        <option value="{{$circuit->id}}" @selected($circuit->id == $editionCircuit->circuit_id)>{{$circuit->country->name}} - {{$circuit->city}} - {{$circuit->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-2">
                                <select name="round" id="round" class="form-control" required>
                                    <option value="" disabled selected>Select round</option>
                                    @for($i=1;$i<25;$i++)
                                        <option value="{{$i}}" @selected($i == $editionCircuit->round)>{{$i}}</option>
                                    @endfor
                                </select>
                            </div>

                            <div class="col-3">
                                <button type="submit" class="btn btn-outline-primary">
                                    <i class="fa-solid fa-floppy-disk"></i> Update
                                </button>
                            </div>
                        </div>
                    </form>
                    <br>
                </div>

                <div class="col-1"></div>
            </div>
        </div>
    </div>

    @section('scripts')
        <script>
            //
        </script>
    @stop
</x-app-layout>
