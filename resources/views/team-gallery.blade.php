<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('layouts.head.head')
        <style>
            .team-gallery-card { border-top: 4px solid var(--team-color, #0d6efd); }
            .team-gallery-car { height: 190px; object-fit: contain; width: 100%; }
            .team-gallery-placeholder { height: 190px; }
        </style>
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
            <a href="{{ route('welcome') }}"><x-application-logo class="h-10 w-auto fill-current text-dark" /></a>
        </div>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-4">
                <div class="d-flex justify-content-between align-items-center gap-2 mb-3">
                    <a href="{{ route('team.single', $team) }}" class="btn btn-outline-dark">&larr; Back</a>
                    <div class="input-group w-auto">
                        <span class="bg-white input-group-text">Team</span>
                        <select class="form-select" id="changeGalleryTeam" aria-label="Cambia team">
                            @foreach($teams as $teamOption)
                                <option value="{{ $teamOption->id }}" @selected($teamOption->id === $team->id)>{{ $teamOption->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-3">
                    <div class="p-4 text-center">
                        <div
                            class="d-flex align-items-center justify-content-center text-muted fst-italic"
                            id="teamGalleryLogo"
                            data-alt="Logo {{ $team->name }}"
                            style="height:180px;"
                        >Caricamento logo…</div>
                    </div>
                </div>

                <div class="row g-3">
                    @forelse($galleryEditions as $galleryEdition)
                        <div class="col-12 col-md-6 col-xl-4">
                            <article class="bg-white overflow-hidden shadow-sm sm:rounded-lg h-100 team-gallery-card" style="--team-color: {{ $team->color ?: '#0d6efd' }};">
                                <div class="p-3">
                                    <div class="d-flex justify-content-between align-items-baseline mb-2">
                                        <h2 class="h4 mb-0">
                                            <a href="{{ route('team.single', ['team' => $team, 'edition' => $galleryEdition['id']]) }}" class="text-decoration-none text-dark">
                                                {{ $galleryEdition['year'] }}
                                            </a>
                                        </h2>
                                        @if($galleryEdition['position'] !== null)<span class="text-muted small">Pos. {{ $galleryEdition['position'] }}</span>@endif
                                    </div>

                                    <div
                                        class="team-gallery-placeholder d-flex align-items-center justify-content-center bg-light text-muted small mb-3"
                                        data-gallery-car="{{ $galleryEdition['car']?->id }}"
                                        data-alt="{{ $galleryEdition['car']?->name }} - {{ $galleryEdition['year'] }}"
                                    >Caricamento immagine…</div>

                                    @if($galleryEdition['car']?->name)
                                        <div class="small mb-2">
                                            <a href="https://www.google.com/search?tbm=isch&amp;q={{ urlencode(trim($galleryEdition['car']->name.' F1 '.$galleryEdition['year'])) }}" target="_blank" rel="noopener noreferrer">
                                                {{ $galleryEdition['car']->name }}
                                            </a>
                                        </div>
                                    @endif

                                    <div class="border-top pt-2">
                                        @forelse($galleryEdition['drivers'] as $driver)
                                            <div class="d-flex justify-content-between gap-2 small py-1">
                                                <a href="{{ route('driver.single', $driver['id']) }}">{{ $driver['name'] }}</a>
                                                @if($driver['position'] !== null)<span class="text-muted text-nowrap">Pos. {{ $driver['position'] }}</span>@endif
                                            </div>
                                        @empty
                                            <div class="text-muted small">Piloti non disponibili</div>
                                        @endforelse
                                    </div>
                                </div>
                            </article>
                        </div>
                    @empty
                        <div class="col-12"><div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-3 text-muted">Nessuna edizione disponibile per questo team.</div></div>
                    @endforelse
                </div>
            </div>
        </div>

        <script>
            const galleryImageUrl = @json(route('team.gallery.image', $team));
            const galleryTeamUrlTemplate = @json(route('team.gallery', ['team' => '__TEAM__']));

            document.getElementById('changeGalleryTeam')?.addEventListener('change', function (event) {
                window.location.href = galleryTeamUrlTemplate.replace('__TEAM__', event.target.value);
            });

            async function loadGalleryImage(placeholder, carId = null) {
                const url = new URL(galleryImageUrl, window.location.origin);
                if (carId) url.searchParams.set('car', carId);

                try {
                    const response = await fetch(url, { headers: { Accept: 'application/json' } });
                    const payload = await response.json();

                    if (!payload.url) throw new Error('Image unavailable');

                    const image = document.createElement('img');
                    image.src = payload.url;
                    image.alt = placeholder.dataset.alt;
                    image.loading = 'lazy';
                    image.className = carId ? 'team-gallery-car mb-3' : 'img-fluid d-block mx-auto';
                    image.style.cssText = carId ? '' : 'max-height:180px;object-fit:contain;';
                    image.addEventListener('error', () => {
                        placeholder.textContent = carId ? 'Immagine auto non disponibile' : 'Logo non disponibile';
                        image.replaceWith(placeholder);
                    }, { once: true });
                    placeholder.replaceWith(image);
                } catch (_) {
                    placeholder.textContent = carId ? 'Immagine auto non disponibile' : 'Logo non disponibile';
                }
            }

            loadGalleryImage(document.getElementById('teamGalleryLogo'));

            const carPlaceholders = document.querySelectorAll('[data-gallery-car]');
            const loadCar = (placeholder) => {
                const carId = placeholder.dataset.galleryCar;

                if (!carId) {
                    placeholder.textContent = 'Immagine auto non disponibile';
                    return;
                }

                loadGalleryImage(placeholder, carId);
            };

            if ('IntersectionObserver' in window) {
                const observer = new IntersectionObserver((entries) => {
                    entries.filter((entry) => entry.isIntersecting).forEach((entry) => {
                        observer.unobserve(entry.target);
                        loadCar(entry.target);
                    });
                }, { rootMargin: '250px' });
                carPlaceholders.forEach((placeholder) => observer.observe(placeholder));
            } else {
                carPlaceholders.forEach(loadCar);
            }
        </script>

        @include('layouts.footer.footer')
    </body>
</html>
