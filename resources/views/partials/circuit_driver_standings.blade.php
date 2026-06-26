<div class="mb-3">
    <i class="{{ $icon }}"></i> {{ $title }}
</div>

@forelse($standings as $standingDriver)
    <div class="d-flex align-items-center gap-2 mb-1 pb-1" style="border-bottom:1px solid #eee;">
        <div style="width:50px;" class="h5 text-center mb-0">
            {{ $standingDriver['firstPlaces'] }}
        </div>
        <div class="flex-grow-1 h6 mb-0">
            <a href="{{ route('driver.single', $standingDriver['driver']) }}" class="text-decoration-none">
                {{ $standingDriver['driver']->name }}
            </a>
        </div>
    </div>
@empty
    <div class="text-muted">Nessun dato disponibile</div>
@endforelse
