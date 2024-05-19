@unless ($breadcrumbs->isEmpty())
    <ol class="breadcrumb text-muted fs-6 fw-semibold">
        @foreach ($breadcrumbs as $breadcrumb)
            @if (!is_null($breadcrumb->url) && !$loop->last)
                @if ($loop->first)
                    <li class="breadcrumb-item"><a href="{{ $breadcrumb->url }}">{!! getIcon('home') !!}</a></li>
                @else
                    <li class="breadcrumb-item"><a href="{{ $breadcrumb->url }}">{{ $breadcrumb->title }}</a></li>
                @endif
            @else
                <li class="breadcrumb-item text-muted active">{{ $breadcrumb->title }}</li>
            @endif
        @endforeach
    </ol>
@endunless
