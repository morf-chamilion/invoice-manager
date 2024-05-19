@php
    $content = !empty($content) ? $content : (isset($pageData) ? $pageData->get('page_title') : '');
    $bannerImage = !empty($bannerImage) ? $bannerImage : (isset($pageData) ? $pageData->getFirstMedia('banner_image')?->getFullUrl() : null);
@endphp

<section class="sub-page-banner">
    @if ($bannerImage)
        <img src="{!! $bannerImage !!}" alt="banner image" class="full-image">
    @endif
    <div class="container">
        <div class="row">
            <div class="col-sm-12 col-lg-9">
                <div class="content-wrapper">
                    {!! $content !!}
                </div>
            </div>
        </div>
    </div>
</section>
