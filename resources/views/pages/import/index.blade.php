<x-app-layout>
    <x-slot name="header">
        <div class="row">
            <div class="col-7">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Import
                </h2>
            </div>
            <div class="col-5 text-end">
                --
            </div>
        </div>
    </x-slot>

    <div class="container-fluid border">
        <div class="card my-4">
            <div class="row mt-3">
                <div class="col-1"></div>

                <div class="col-10">
                    <form method="post" action="{{route('import.store')}}" id="importForm">
                        @csrf

                        <div class="mb-3">
                            <label for="edition" class="form-label">Edition<span class="text-danger">*</span></label>
                            <select name="edition" id="edition" class="form-control" required>
                                <option value="" disabled selected>Select edition</option>
                                @foreach($editions as $edition)
                                    <option value="{{$edition->id}}" @selected(old('edition') === $edition->id)>{{$edition->edition}} - {{$edition->year}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="circuit" class="form-label">Circuit<span class="text-danger">*</span></label>
                            <select name="circuit" id="circuit" class="form-control" required disabled>
                                <option value="" disabled selected>Select edition first</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="type" class="form-label">Type<span class="text-danger">*</span></label>
                            <select name="type" id="type" class="form-control" required>
                                <option value="" disabled selected>Select type</option>
                                <option value="grid">Grid</option>
                                <option value="race">Race</option>
                                <option value="sprint">Sprint</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label mb-0">Datas<span class="text-danger">*</span></label>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="clearImportGrid">
                                    <i class="fa fa-eraser"></i> Clear
                                </button>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered table-sm align-middle mb-0" id="importGrid">
                                    <thead>
                                        <tr>
                                            <th style="width:48px;"></th>
                                            <th class="text-center">pos</th>
                                            <th class="text-center">num</th>
                                            <th class="text-center">team</th>
                                            <th class="text-center">time</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>

                            <textarea name="grid_data" id="gridData" class="d-none"></textarea>
                        </div>

                        <div class="mb-3 text-end">
                            <button type="submit" class="btn btn-sm btn-outline-primary">
                                <i class="fa-solid fa-floppy-disk"></i> Import
                            </button>
                        </div>

                    </form>
                </div>

                <div class="col-1"></div>
            </div>
        </div>
    </div>

    @section('style')
        <style>
            #importGrid th {
                background: #f8f9fa;
                font-weight: 600;
                user-select: none;
            }

            #importGrid td {
                background: #fff;
                cursor: cell;
                outline: none;
            }

            #importGrid td:focus {
                box-shadow: inset 0 0 0 2px #0d6efd;
            }

            #importGrid td.text-muted {
                cursor: default;
                height: 40px;
            }
        </style>
    @stop

    @section('scripts')
        <script>
            const editionSelect = document.getElementById('edition');
            const circuitSelect = document.getElementById('circuit');
            const importForm = document.getElementById('importForm');
            const circuitsUrl = @json(route('import.editions.circuits', ['edition' => '__EDITION__']));

            function resetCircuitSelect(label = 'Select edition first') {
                circuitSelect.innerHTML = '';
                circuitSelect.add(new Option(label, '', true, true));
                circuitSelect.options[0].disabled = true;
                circuitSelect.disabled = true;
            }

            const selectedCircuit = @json(old('circuit'));

            function loadCircuits(editionId) {
                if (!editionId) {
                    resetCircuitSelect();
                    buildImportGrid(0);
                    return;
                }

                resetCircuitSelect('Loading circuits...');

                fetch(circuitsUrl.replace('__EDITION__', editionId), {
                    headers: {
                        'Accept': 'application/json',
                    },
                })
                    .then(response => response.ok ? response.json() : Promise.reject(response))
                    .then(data => {
                        const circuits = data.circuits ?? [];

                        circuitSelect.innerHTML = '';
                        circuitSelect.add(new Option('Select circuit', '', true, true));
                        circuitSelect.options[0].disabled = true;

                        circuits.forEach(circuit => {
                            const option = new Option(circuit.name, circuit.id);
                            option.selected = circuit.id === selectedCircuit;
                            circuitSelect.add(option);
                        });

                        circuitSelect.disabled = circuits.length === 0;

                        if (circuits.length === 0) {
                            resetCircuitSelect('No circuits available');
                        }

                        buildImportGrid(data.drivers_count ?? 0);
                    })
                    .catch(() => {
                        resetCircuitSelect('Unable to load circuits');
                        buildImportGrid(0);
                    });
            }

            editionSelect?.addEventListener('change', function () {
                loadCircuits(this.value);
            });

            const importGrid = document.getElementById('importGrid');
            const gridData = document.getElementById('gridData');
            const clearImportGrid = document.getElementById('clearImportGrid');
            const importGridBody = importGrid.querySelector('tbody');

            function buildImportGrid(rowsCount) {
                importGridBody.innerHTML = '';

                if (rowsCount <= 0) {
                    const tr = document.createElement('tr');
                    const td = document.createElement('td');

                    td.colSpan = 5;
                    td.className = 'text-muted';
                    td.textContent = 'Select an edition to create the import rows.';
                    tr.appendChild(td);
                    importGridBody.appendChild(tr);
                    updateGridData();
                    return;
                }

                for (let row = 0; row < rowsCount; row++) {
                    const tr = document.createElement('tr');
                    const th = document.createElement('th');

                    th.className = 'text-center bg-light';
                    th.textContent = row + 1;
                    tr.appendChild(th);

                    for (let col = 0; col < 4; col++) {
                        const td = document.createElement('td');

                        td.contentEditable = 'true';
                        td.dataset.row = row;
                        td.dataset.col = col;
                        td.style.minWidth = '120px';
                        td.style.height = '32px';
                        tr.appendChild(td);
                    }

                    importGridBody.appendChild(tr);
                }

                updateGridData();
            }

            function gridCells() {
                return Array.from(importGrid.querySelectorAll('td[contenteditable="true"]'));
            }

            function updateGridData() {
                const rows = [];

                importGrid.querySelectorAll('tbody tr').forEach(function (row) {
                    rows.push(Array.from(row.querySelectorAll('td')).map(cell => cell.textContent.trim()));
                });

                gridData.value = JSON.stringify(rows);
            }

            importGrid?.addEventListener('input', updateGridData);

            importGrid?.addEventListener('paste', function (event) {
                const startCell = event.target.closest('td[contenteditable="true"]');
                if (!startCell) {
                    return;
                }

                event.preventDefault();

                const pastedText = event.clipboardData.getData('text/plain');
                const rows = pastedText
                    .replace(/\r/g, '')
                    .split('\n')
                    .filter((row, index, list) => row.length > 0 || index < list.length - 1)
                    .map(row => row.split('\t'));

                const startRow = Number(startCell.dataset.row);
                const startCol = Number(startCell.dataset.col);

                rows.forEach(function (row, rowIndex) {
                    row.forEach(function (value, colIndex) {
                        const cell = importGrid.querySelector(
                            `td[data-row="${startRow + rowIndex}"][data-col="${startCol + colIndex}"]`
                        );

                        if (cell) {
                            cell.textContent = value.trim();
                        }
                    });
                });

                updateGridData();
            });

            clearImportGrid?.addEventListener('click', function () {
                gridCells().forEach(cell => cell.textContent = '');
                updateGridData();
            });

            importForm?.addEventListener('submit', function () {
                updateGridData();
            });

            loadCircuits(editionSelect?.value);
        </script>
    @stop
</x-app-layout>
