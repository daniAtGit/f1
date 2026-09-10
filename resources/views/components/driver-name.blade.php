@props(['driver', 'class' => '', 'showFlag' => true, 'compact' => false])

<span {{ $attributes->class(['d-inline-flex', 'align-items-center', 'gap-1', 'text-nowrap' => $compact, $class]) }} @if($compact) style="font-size:.8rem;line-height:20px;" @endif>
    @if($showFlag && $driver?->country?->flag_icon_url)
        <span style="width:{{ $compact ? '20px' : '30px' }};height:{{ $compact ? '20px' : '30px' }};padding:{{ $compact ? '2px' : '3px' }};border:1px solid #ccc;display:inline-flex;align-items:center;justify-content:center;flex:0 0 auto;">
            <img
                src="{{ $driver->country->flag_icon_url }}"
                alt="{{ $driver->country->name }}"
                title="{{ $driver->country->name }}"
                width="{{ $compact ? '14' : '20' }}"
                height="{{ $compact ? '10' : '14' }}"
                style="object-fit:cover;"
                loading="lazy"
            >
        </span>
    @endif
    <span>{{ $driver?->name }}</span>
</span>
