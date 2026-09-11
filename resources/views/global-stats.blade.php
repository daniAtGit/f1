<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('layouts.head.head')
</head>
<body class="antialiased bg-light">
    @if (Route::has('login'))
        <div class="position-absolute top-0 end-0 p-3 p-md-4 d-flex gap-2">
            @auth
                <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-dark">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="btn btn-sm btn-dark">Login</a>
            @endauth
        </div>
    @endif

    <div class="position-absolute top-0 start-50 translate-middle-x p-3 p-md-4" style="z-index:20;">
        <a href="{{ route('welcome') }}">
            <x-application-logo class="h-10 w-auto fill-current text-dark" />
        </a>
    </div>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-5 pt-4">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-3 p-md-4 text-gray-900" x-data="{ activeTab: 'drivers' }">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
                        <div>
                            <h1 class="h4 mb-0">Stats</h1>
                        </div>
                        <a href="{{ route('welcome') }}" class="btn btn-sm btn-outline-secondary">Back</a>
                    </div>

                    <ul class="nav nav-tabs mb-4" role="tablist" aria-label="Categorie statistiche">
                        <li class="nav-item" role="presentation">
                            <button type="button" class="nav-link" :class="{ 'active': activeTab === 'drivers' }" @click="activeTab = 'drivers'; $nextTick(() => window.adjustStatsTables?.())">Drivers</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button type="button" class="nav-link" :class="{ 'active': activeTab === 'teams' }" @click="activeTab = 'teams'; $nextTick(() => window.adjustStatsTables?.())">Teams</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button type="button" class="nav-link" :class="{ 'active': activeTab === 'circuits' }" @click="activeTab = 'circuits'; $nextTick(() => window.adjustStatsTables?.())">Circuits</button>
                        </li>
                    </ul>

                    <div x-show="activeTab === 'drivers'">
                        <div class="d-flex justify-content-end mb-3">
                            <button type="button" id="show-all-drivers" class="btn btn-sm btn-outline-secondary">Vedi tutti</button>
                        </div>
                        <div class="table-responsive stats-table-wrapper">
                            <table id="driver-stats-table" class="table table-sm table-hover align-middle mb-0 stats-table">
                                <thead>
                                    <tr>
                                        <th>Titles</th>
                                        <th>Driver</th>
                                        <th>Races</th>
                                        <th>Pole</th>
                                        <th>Podiums</th>
                                        <th>Victories</th>
                                        <th>Sprints</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($driverStatistics as $statistic)
                                        <tr data-titles="{{ $statistic['titles'] }}">
                                            <td>{{ $statistic['titles'] }}</td>
                                            <td><a href="{{ route('driver.single', $statistic['driver']) }}" class="text-decoration-none text-reset"><x-driver-name :driver="$statistic['driver']" :compact="true" /></a></td>
                                            <td>{{ $statistic['races'] }}</td>
                                            <td>{{ $statistic['poles'] }}</td>
                                            <td>{{ $statistic['podiums'] }}</td>
                                            <td>{{ $statistic['raceWins'] }}</td>
                                            <td>{{ $statistic['sprintWins'] }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="7" class="text-center text-muted py-4">Nessun dato disponibile.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div x-show="activeTab === 'teams'" x-cloak>
                        <div class="d-flex justify-content-end mb-3">
                            <button type="button" id="show-all-teams" class="btn btn-sm btn-outline-secondary">Vedi tutti</button>
                        </div>
                        <div class="table-responsive stats-table-wrapper">
                            <table id="team-stats-table" class="table table-sm table-hover align-middle mb-0 stats-table">
                                <thead>
                                    <tr>
                                        <th>Titles</th>
                                        <th>Team</th>
                                        <th>Races</th>
                                        <th>Pole</th>
                                        <th>Podium</th>
                                        <th>Victories</th>
                                        <th>Sprints</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($teamStatistics as $statistic)
                                        <tr data-titles="{{ $statistic['titles'] }}">
                                            <td>{{ $statistic['titles'] }}</td>
                                            <td>
                                                <a href="{{ route('team.single', $statistic['team']) }}" class="d-inline-flex align-items-center gap-1 text-nowrap text-decoration-none text-reset">
                                                    @if($statistic['team']->country?->flag_icon_url)
                                                        <span style="width:20px;height:20px;padding:2px;border:1px solid #ccc;display:inline-flex;align-items:center;justify-content:center;flex:0 0 auto;">
                                                            <img src="{{ $statistic['team']->country->flag_icon_url }}" alt="{{ $statistic['team']->country->name }}" title="{{ $statistic['team']->country->name }}" width="14" height="10" style="object-fit:cover;">
                                                        </span>
                                                    @endif
                                                    <span>{{ $statistic['team']->name }}</span>
                                                </a>
                                            </td>
                                            <td>{{ $statistic['races'] }}</td>
                                            <td>{{ $statistic['poles'] }}</td>
                                            <td>{{ $statistic['podiums'] }}</td>
                                            <td>{{ $statistic['raceWins'] }}</td>
                                            <td>{{ $statistic['sprintWins'] }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="7" class="text-center text-muted py-4">Nessun dato disponibile.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div x-show="activeTab === 'circuits'" x-cloak>
                        <div class="table-responsive stats-table-wrapper">
                            <table id="circuit-stats-table" class="table table-sm table-hover align-middle mb-0 stats-table">
                                <thead>
                                    <tr>
                                        <th>N.</th>
                                        <th>Circuit</th>
                                        <th>Races</th>
                                        <th>Poles</th>
                                        <th>Podiums</th>
                                        <th>Victories</th>
                                        <th>Sprints</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($circuitStatistics as $statistic)
                                        <tr>
                                            <td>{{ $statistic['editions'] }}</td>
                                            <td>
                                                <a href="{{ route('circuit.single', $statistic['circuit']) }}" class="d-inline-flex align-items-center gap-1 text-nowrap text-decoration-none text-reset">
                                                    @if($statistic['circuit']->country?->flag_icon_url)
                                                        <span style="width:20px;height:20px;padding:2px;border:1px solid #ccc;display:inline-flex;align-items:center;justify-content:center;flex:0 0 auto;">
                                                            <img src="{{ $statistic['circuit']->country->flag_icon_url }}" alt="{{ $statistic['circuit']->country->name }}" title="{{ $statistic['circuit']->country->name }}" width="14" height="10" style="object-fit:cover;">
                                                        </span>
                                                    @endif
                                                    <span>{{ $statistic['circuit']->name }}</span>
                                                </a>
                                            </td>
                                            <td>@if($statistic['mostRacesDriver'])<span class="d-block fw-bold">{{ $statistic['mostRacesDriver']['count'] }}</span><x-driver-name :driver="$statistic['mostRacesDriver']['driver']" :compact="true" />@endif</td>
                                            <td>@if($statistic['mostPolesDriver'])<span class="d-block fw-bold">{{ $statistic['mostPolesDriver']['count'] }}</span><x-driver-name :driver="$statistic['mostPolesDriver']['driver']" :compact="true" />@endif</td>
                                            <td>@if($statistic['mostPodiumsDriver'])<span class="d-block fw-bold">{{ $statistic['mostPodiumsDriver']['count'] }}</span><x-driver-name :driver="$statistic['mostPodiumsDriver']['driver']" :compact="true" />@endif</td>
                                            <td>@if($statistic['mostRaceWinsDriver'])<span class="d-block fw-bold">{{ $statistic['mostRaceWinsDriver']['count'] }}</span><x-driver-name :driver="$statistic['mostRaceWinsDriver']['driver']" :compact="true" />@endif</td>
                                            <td>@if($statistic['mostSprintWinsDriver'])<span class="d-block fw-bold">{{ $statistic['mostSprintWinsDriver']['count'] }}</span><x-driver-name :driver="$statistic['mostSprintWinsDriver']['driver']" :compact="true" />@endif</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="7" class="text-center text-muted py-4">Nessun dato disponibile.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('layouts.footer.footer')
    <style>
        .stats-table.dataTable {
            box-sizing: border-box !important;
            width: 100% !important;
        }

        .stats-table.dataTable > thead > tr > th,
        .stats-table.dataTable > tbody > tr > td {
            border-color: #dee2e6 !important;
        }

        #driver-stats-table_wrapper .dataTables_filter input,
        #team-stats-table_wrapper .dataTables_filter input {
            border: 1px solid #ced4da !important;
            border-radius: .375rem;
        }

        @media (min-width: 768px) {
            .stats-table-wrapper {
                overflow-x: visible;
            }
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let showAllDrivers = false;

            $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
                if (settings.nTable.id !== 'driver-stats-table' || showAllDrivers) {
                    return true;
                }

                return Number($(settings.aoData[dataIndex].nTr).data('titles')) > 0;
            });

            const driverTable = $('#driver-stats-table').DataTable({
                order: [[0, 'desc'], [1, 'asc']],
                columnDefs: [
                    { targets: [0, 2, 3, 4, 5, 6], className: 'text-center' },
                ],
                language: {
                    search: 'Cerca:',
                    zeroRecords: 'Nessun pilota con titoli.',
                    info: 'Visualizzati _START_–_END_ di _TOTAL_ piloti',
                    infoEmpty: 'Nessun pilota con titoli.',
                    lengthMenu: 'Mostra _MENU_ piloti',
                },
            });

            document.getElementById('show-all-drivers').addEventListener('click', function () {
                showAllDrivers = true;
                this.remove();
                driverTable.draw();
            });

            let showAllTeams = false;

            $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
                if (settings.nTable.id !== 'team-stats-table' || showAllTeams) {
                    return true;
                }

                return Number($(settings.aoData[dataIndex].nTr).data('titles')) > 0;
            });

            const teamTable = $('#team-stats-table').DataTable({
                order: [[0, 'desc'], [1, 'asc']],
                columnDefs: [
                    { targets: [0, 2, 3, 4, 5, 6], className: 'text-center' },
                ],
                language: {
                    search: 'Cerca:',
                    zeroRecords: 'Nessun team con titoli.',
                    info: 'Visualizzati _START_–_END_ di _TOTAL_ team',
                    infoEmpty: 'Nessun team con titoli.',
                    lengthMenu: 'Mostra _MENU_ team',
                },
            });

            document.getElementById('show-all-teams').addEventListener('click', function () {
                showAllTeams = true;
                this.remove();
                teamTable.draw();
            });

            $('#circuit-stats-table').DataTable({
                order: [[0, 'desc'], [1, 'asc']],
                columnDefs: [
                    { targets: 0, className: 'text-center' },
                ],
                language: {
                    search: 'Cerca:',
                    zeroRecords: 'Nessun circuito trovato.',
                    info: 'Visualizzati _START_–_END_ di _TOTAL_ circuiti',
                    infoEmpty: 'Nessun circuito disponibile.',
                    lengthMenu: 'Mostra _MENU_ circuiti',
                },
            });

            window.adjustStatsTables = function () {
                $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
            };
        });
    </script>
</body>
</html>
