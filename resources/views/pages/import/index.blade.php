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
                                    <option value="{{$edition->id}}" @selected((string) old('edition', request('edition')) === (string) $edition->id)>{{$edition->edition}} - {{$edition->year}}</option>
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
                                <option value="grid" @selected(old('type', request('type')) === 'grid')>Grid</option>
                                <option value="race" @selected(old('type', request('type')) === 'race')>Race</option>
                                <option value="sprint" @selected(old('type', request('type')) === 'sprint')>Sprint</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label for="formula1Data" class="form-label mb-0">Paste F1 results</label>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="clearFormula1Data">
                                    <i class="fa fa-eraser"></i> Clear
                                </button>
                            </div>
                            <textarea id="formula1Data" class="form-control" rows="6" placeholder="Copy the entire table from F1 (Starting Grid, Race Result, or Sprint) and paste it here."></textarea>
                            <div class="form-text">Position, number, team, and time/status are automatically extracted. The format is valid for Grid, Race, and Sprint.</div>
                            <div class="d-flex align-items-center gap-2 mt-2">
                                <button type="button" class="btn btn-sm btn-outline-primary" id="parseFormula1Data">
                                    <i class="fa fa-wand-magic-sparkles"></i> Process data
                                </button>
                                <span class="small" id="importFeedback" aria-live="polite"></span>
                            </div>
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
                                            <th class="text-center">driver</th>
                                            <th class="text-center">team</th>
                                            <th class="text-center">time</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>

                            <textarea name="grid_data" id="gridData" class="d-none"></textarea>
                            <input type="hidden" name="confirm_overwrite" id="confirmOverwrite" value="0">
                            @error('grid_data')
                                <div class="alert alert-danger mt-3 mb-0">{{ $message }}</div>
                            @enderror
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
            const typeSelect = document.getElementById('type');
            const importForm = document.getElementById('importForm');
            const circuitsUrl = @json(route('import.editions.circuits', ['edition' => '__EDITION__']));
            let availableDriverNumbers = new Set();
            let circuitImportStatus = {};

            function resetCircuitSelect(label = 'Select edition first') {
                circuitSelect.innerHTML = '';
                circuitSelect.add(new Option(label, '', true, true));
                circuitSelect.options[0].disabled = true;
                circuitSelect.disabled = true;
            }

            const selectedCircuit = @json(old('circuit', request('circuit')));

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
                            option.selected = String(circuit.id) === String(selectedCircuit);
                            circuitSelect.add(option);
                            circuitImportStatus[String(circuit.id)] = circuit.existing_imports ?? {};
                        });

                        availableDriverNumbers = new Set((data.driver_numbers ?? []).map(String));
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
            const formula1Data = document.getElementById('formula1Data');
            const parseFormula1Data = document.getElementById('parseFormula1Data');
            const clearFormula1Data = document.getElementById('clearFormula1Data');
            const importFeedback = document.getElementById('importFeedback');
            const confirmOverwrite = document.getElementById('confirmOverwrite');

            function buildImportGrid(rowsCount) {
                importGridBody.innerHTML = '';

                if (rowsCount <= 0) {
                    const tr = document.createElement('tr');
                    const td = document.createElement('td');

                    td.colSpan = 6;
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

                        // The driver is shown only as a reference: the submitted
                        // data remains position, number, team and time/status.
                        if (col === 1) {
                            const driverCell = document.createElement('td');

                            driverCell.className = 'driver-name text-muted';
                            driverCell.dataset.row = row;
                            driverCell.style.minWidth = '180px';
                            tr.appendChild(driverCell);
                        }
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
                    rows.push(Array.from(row.querySelectorAll('td[contenteditable="true"]')).map(cell => cell.textContent.trim()));
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

            function showImportFeedback(message, isError = false) {
                importFeedback.textContent = message;
                importFeedback.className = `small ${isError ? 'text-danger' : 'text-success'}`;
            }

            function parseFormula1Rows(value) {
                const lines = value.replace(/\r/g, '').split('\n').filter(line => line.trim() !== '');
                const parsedRows = [];
                const skippedRows = [];
                let pendingResult = null;

                const isPosition = value => /^(?:\d+|NC|DQ|DNS)$/i.test(value ?? '');
                const isDriverNumber = value => /^\d+$/.test(value ?? '');

                lines.forEach(line => {
                    const plainLine = line.trim();
                    const columns = plainLine.split('\t').map(column => column.trim());
                    const first = columns[0]?.toLowerCase() ?? '';

                    if (first === 'pos.' || first === 'pos' || first.includes('pos.no.')) {
                        return;
                    }

                    // In some browsers reading mode removes tab characters entirely.
                    // Its Race/Sprint rows then look like:
                    // "NC 18", "Lance Stroll", "Aston Martin Aramco Mercedes 5 DNF 0".
                    if (columns.length === 1) {
                        const positionAndNumber = plainLine.match(/^(\d+|NC|DQ|DNS)\s+(\d+)$/i);

                        if (positionAndNumber) {
                            pendingResult = {
                                position: positionAndNumber[1],
                                number: positionAndNumber[2],
                            };
                            return;
                        }

                        if (pendingResult) {
                            const raceOrSprintDetails = plainLine.match(/^(.*?)\s+(\d+)\s+(.+?)\s+(\d+)$/);
                            if (raceOrSprintDetails) {
                                parsedRows.push([
                                    pendingResult.position,
                                    pendingResult.number,
                                    pendingResult.driver ?? '',
                                    raceOrSprintDetails[1],
                                    raceOrSprintDetails[3],
                                ]);
                                pendingResult = null;
                                return;
                            }

                            // Starting Grid has no laps/points. Detect a time or status
                            // at the end and keep the preceding text as the team name.
                            const gridDetails = plainLine.match(/^(.*?)\s+((?:\d+:)?\d{1,2}:\d{2}\.\d{3}|\+\d+(?:\.\d+)?s|DNS)$/i);
                            if (gridDetails) {
                                parsedRows.push([
                                    pendingResult.position,
                                    pendingResult.number,
                                    pendingResult.driver ?? '',
                                    gridDetails[1],
                                    gridDetails[2],
                                ]);
                                pendingResult = null;
                            }

                            // Otherwise this is the driver-name line.
                            pendingResult.driver = plainLine;
                            return;
                        }

                        skippedRows.push(line);
                        return;
                    }

                    // Browser reading mode can split a F1 result over three lines:
                    // "Pos  No.", "Driver", then "Team  Laps  Time/Retired  Pts".
                    if (columns.length === 2 && isPosition(columns[0]) && isDriverNumber(columns[1])) {
                        pendingResult = { position: columns[0], number: columns[1] };
                        return;
                    }

                    if (pendingResult && columns.length >= 4) {
                        parsedRows.push([
                            pendingResult.position,
                            pendingResult.number,
                            pendingResult.driver ?? '',
                            columns[0],
                            columns[2],
                        ]);
                        pendingResult = null;
                        return;
                    }

                    // Starting Grid in reading mode has no laps/points, so its final
                    // line is usually just "Team  Time".
                    if (pendingResult && columns.length >= 2) {
                        parsedRows.push([
                            pendingResult.position,
                            pendingResult.number,
                            pendingResult.driver ?? '',
                            columns[0],
                            columns[1],
                        ]);
                        pendingResult = null;
                        return;
                    }

                    if (columns.length < 4) {
                        if (pendingResult) {
                            // This is the driver-name line in reading mode.
                            return;
                        }

                        skippedRows.push(line);
                        return;
                    }

                    // F1 Race/Sprint: Pos, No., Driver, Team, Laps, Time/Retired, Pts.
                    // F1 Starting Grid: Pos, No., Driver, Team, Time.
                    // Existing four-column grid remains accepted for manual pastes.
                    let row;
                    if (!isPosition(columns[0]) || !isDriverNumber(columns[1])) {
                        if (pendingResult) {
                            // This is the driver-name line in reading mode.
                            return;
                        }

                        skippedRows.push(line);
                        return;
                    }

                    if (columns.length >= 7) {
                        row = [columns[0], columns[1], columns[2], columns[3], columns[5]];
                    } else if (columns.length >= 5) {
                        row = [columns[0], columns[1], columns[2], columns[3], columns[4]];
                    } else {
                        row = [columns[0], columns[1], '', columns[2], columns[3]];
                    }

                    if (!row[0] || !row[1]) {
                        skippedRows.push(line);
                        return;
                    }

                    parsedRows.push(row);
                });

                return { parsedRows, skippedRows };
            }

            function fillImportGrid(rows) {
                const rowCount = Math.max(rows.length, Number(importGrid.querySelectorAll('td[contenteditable="true"]').length / 4));
                buildImportGrid(rowCount);

                rows.forEach((row, rowIndex) => {
                    [row[0], row[1], row[3], row[4]].forEach((value, colIndex) => {
                        const cell = importGrid.querySelector(`td[contenteditable="true"][data-row="${rowIndex}"][data-col="${colIndex}"]`);
                        if (cell) cell.textContent = value;
                    });

                    const driverCell = importGrid.querySelector(`td.driver-name[data-row="${rowIndex}"]`);
                    if (driverCell) driverCell.textContent = row[2] ?? '';
                });

                updateGridData();
            }

            parseFormula1Data?.addEventListener('click', function () {
                const { parsedRows, skippedRows } = parseFormula1Rows(formula1Data.value);

                if (!parsedRows.length) {
                    showImportFeedback('No valid rows found. Copy the entire F1 table or the text in read mode.', true);
                    return;
                }

                const unknownNumbers = [...new Set(parsedRows.map(row => row[1]).filter(number => availableDriverNumbers.size && !availableDriverNumbers.has(String(number))))];
                const duplicateNumbers = parsedRows.map(row => row[1]).filter((number, index, numbers) => numbers.indexOf(number) !== index);

                fillImportGrid(parsedRows);

                if (unknownNumbers.length || duplicateNumbers.length) {
                    const messages = [];
                    if (unknownNumbers.length) messages.push(`numbers not present in the edition: ${unknownNumbers.join(', ')}`);
                    if (duplicateNumbers.length) messages.push(`duplicate numbers: ${[...new Set(duplicateNumbers)].join(', ')}`);
                    showImportFeedback(`Check the data: ${messages.join('; ')}.`, true);
                    return;
                }

                showImportFeedback(`${parsedRows.length} results uploaded${skippedRows.length ? `; ${skippedRows.length} ignored lines` : ''}.`);
            });

            clearImportGrid?.addEventListener('click', function () {
                gridCells().forEach(cell => cell.textContent = '');
                importGrid.querySelectorAll('td.driver-name').forEach(cell => cell.textContent = '');
                updateGridData();
            });

            clearFormula1Data?.addEventListener('click', function () {
                formula1Data.value = '';
                showImportFeedback('');
            });

            importForm?.addEventListener('submit', function (event) {
                updateGridData();

                const alreadyImported = circuitImportStatus[String(circuitSelect.value)]?.[typeSelect.value];
                confirmOverwrite.value = '0';

                if (alreadyImported) {
                    const confirmed = window.confirm('Results already exist for this circuit and type. Confirming will replace the existing results.');
                    if (!confirmed) {
                        event.preventDefault();
                        return;
                    }

                    confirmOverwrite.value = '1';
                }
            });

            loadCircuits(editionSelect?.value);
        </script>
    @stop
</x-app-layout>
