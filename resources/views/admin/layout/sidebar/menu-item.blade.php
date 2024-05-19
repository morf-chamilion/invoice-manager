<div class="menu-item">
    <a class="menu-link" href="{{ $route }}">
        @isset($icon)
            <span class="menu-icon">{!! getIcon($icon, 'fs-2') !!}</span>
        @else
            <span class="menu-bullet">
                <span class="bullet bullet-dot"></span>
            </span>
        @endisset
        <span class="menu-title">{{ $content }}</span>
    </a>
</div>
