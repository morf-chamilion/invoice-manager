@props(['type' => false, 'model'])
<div class="col-lg-3" id="form_metadata_container">
    <div class="card" data-kt-sticky="true" data-kt-sticky-name="form-metadata-card"
        data-kt-sticky-width="{target: '#form_metadata_container'}" data-kt-sticky-top="100px"
        data-kt-sticky-animation="false" data-kt-sticky-zindex="95">
        <div class="card-body">

            {{ $slot }}

            @if ($type || !empty($model->createdByUser) || !empty($model->updatedByUser))
                <div class="separator separator-dashed mb-8"></div>
            @endif

            @if (!empty($model->createdByUser))
                <h4 class="form-label">Created By</h4>
                <div class="d-flex justify-content-between mb-4">
                    <p class="text-muted">{{ $model->createdByUser->name }}</p>
                    <p class="text-muted">{{ $model->created_at->diffForHumans() }}
                    </p>
                </div>
            @endif

            @if (!empty($model->updatedByUser))
                <h4 class="form-label">{{ __('Last Updated By') }}</h4>
                <div class="d-flex justify-content-between mb-4">
                    <p class="text-muted">{{ $model?->updatedByUser->name }}</p>
                    <p class="text-muted">{{ $model?->updated_at->diffForHumans() }}</p>
                </div>
            @endif

            @if ($type && (!empty($model->createdByUser) || !empty($model->updatedByUser)))
                <div class="separator separator-dashed mb-8"></div>
            @endif

            @if ($type)
                <div class="flex items-center gap-4">
                    <x-button-primary class="w-100">{{ __($type) }}</x-button-primary>
                </div>
            @endif
        </div>
    </div>
</div>
