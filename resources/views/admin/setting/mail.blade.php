<x-default-layout>
    <form method="post" action="{{ $action }}" autocomplete="off">
        @method('post')
        @csrf

        <div class="d-flex flex-column mb-5">
            <div class="card">
                <div class="card-header">
                    <header>
                        <h2 class="text-lg mt-8 font-medium text-gray-900">
                            {{ __($pageData['title']) }}
                        </h2>
                    </header>
                </div>
                <div class="card-body">
                    <div class="mb-8">
                        <x-input-label for="notifications" :value="__('Notifications')" />
                        <div class="form-repeater rounded border p-10" data-repeater-max-items="12">
                            <div class="form-group">
                                <div data-repeater-list="notifications">
                                    @php $notifications = old('notifications', $settings->get('notifications')) ?? ['']; @endphp
                                    @foreach ($notifications as $notification)
                                        <div data-repeater-item class="mt-3">
                                            <div class="form-group row">
                                                <div class="col-lg-10">
                                                    <x-input-label :value="__('Email')" />
                                                    <x-input-text class="mb-2" name="email"
                                                        value="{{ $notification['email'] ?? '' }}" />
                                                </div>
                                                <div class="col-lg-2">
                                                    <button type="button" data-repeater-delete
                                                        class="btn btn-icon btn-sm btn-light-danger mt-3 mt-md-9 w-100">
                                                        <i class="fa fa-trash fs-9 me-2"></i> Delete
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="form-group mt-5">
                                <button type="button" data-repeater-create class="btn btn-sm btn-light-primary">
                                    <i class="fa fa-plus fs-6"></i> Add
                                </button>
                            </div>
                        </div>
                        <x-input-error :messages="$errors->get('notification_emails')" />
                    </div>

                    <div class="mb-8 col-xl-4">
                        <x-input-label for="logo" :value="__('Logo')" />
                        <x-input-file id="logo" name="logo" :fileMaxSize="2" :value="$settings->getMedia('logo')" />
                        <x-input-error :messages="$errors->get('logo')" />
                    </div>

                    <div class="mb-8">
                        <x-input-label for="footer_content" :value="__('Footer Content')" />
                        <x-input-editor name="footer_content" id="footer_content">
                            {{ old('footer_content', $settings->get('footer_content')) }}
                        </x-input-editor>
                        <x-input-error :messages="$errors->get('footer_content')" />
                    </div>
                </div>
            </div>
        </div>

        <!--begin::Mail Templates-->
        <div class="accordion accordion-icon-toggle mb-5" id="kt_accordion_mail">

            <x-mail-template :name="__('user_admin_create_mail')" :title="__('Admin Created Mail Template')" :context="__('To Admin')" :$settings>
                <div class="d-flex align-items-center rounded py-5 px-5 bg-light-info mt-5">
                    <span class="menu-icon">{!! getIcon('information', 'fs-3x text-info me-5') !!}</span>
                    <div class="text-gray-700 fw-bold fs-6">
                        <p class="mb-0">
                            Add <code>[name]</code> to insert the user's name.
                        </p>
                        <p class="mb-0">
                            Add <code>[email]</code> to insert the user's email.
                        </p>
                        <p class="mb-0">
                            Add <code>[password]</code> to insert the user's password.
                        </p>
                        <p class="mb-0">
                            Add <code>[verification_link]</code> to insert the account verification link.
                        </p>
                    </div>
                </div>
            </x-mail-template>
        </div>
        <!--end::Mail Templates-->

        <div class="col-lg-12">
            <div class="card mb-5">
                <div class="card-body">
                    <div class="d-flex justify-content-end gap-4">
                        <x-button-primary>{{ __('Update') }}</x-button-primary>
                    </div>
                </div>
            </div>
        </div>

    </form>
</x-default-layout>
