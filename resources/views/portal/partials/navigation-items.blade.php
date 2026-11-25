@foreach($items as $item)
    <li>
        @if($item['url'])
            <a class="nav {{ $item['active'] ? 'active' : '' }}" href="{{ $item['url'] }}" @if($item['active']) aria-current="page" @endif @if($item['new_tab']) target="_blank" rel="noopener noreferrer" @endif>
                {{ \Filament\Support\generate_icon_html($item['icon'], attributes: new \Illuminate\View\ComponentAttributeBag(['class' => 'icon', 'aria-hidden' => 'true'])) }}
                <span class="nav-label">{{ __($item['label']) }}</span>
                @if($item['badge'] !== null)<span class="nav-badge">{{ $item['badge'] }}</span>@endif
            </a>
        @else
            <span class="nav"><span class="nav-label">{{ __($item['label']) }}</span></span>
        @endif
        @if($item['children']->isNotEmpty())
            <ul class="nav-children">@include('portal.partials.navigation-items', ['items' => $item['children']])</ul>
        @endif
    </li>
@endforeach
