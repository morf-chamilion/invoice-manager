<?php

use App\Enums\SettingModule;
use App\Mail\CommonMail;
use App\RoutePaths\Admin\Auth\AuthRoutePath;
use App\RoutePaths\Pdf\PdfRoutePath;
use App\Services\BaseService;
use App\Services\SettingService;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;


Route::prefix('sandbox')->group(function () {
	$mailSetting = App::make(SettingService::class)->module(SettingModule::MAIL);

	Route::get('/mail/common', function () use ($mailSetting) {
		$fieldMailSubject = $mailSetting->get('user_admin_create_mail_subject');
		$fieldMailBody = $mailSetting->get('user_admin_create_mail_template');

		$mailContent = [
			'[name]' => 'System User',
			'[email]' => 'user@admin.system',
			'[password]' => 'super-secret',
			'[verification_link]' => Blade::render('<a href="{{ $link }}" class="button button-primary">{{ $title }}</a>', [
				'title' => 'Admin Login',
				'link' => route(AuthRoutePath::LOGIN),
			]),
		];

		$mailBody = BaseService::formatMailContent($fieldMailBody, $mailContent);
		$mailSubject = BaseService::formatMailContent($fieldMailSubject, $mailContent);

		return new CommonMail($mailSubject, $mailBody);
	});

	Route::get('/pdf/invoice', function () use ($mailSetting) {
		return view(PdfRoutePath::INVOICE, [
			'invoice' => \App\Models\Invoice::all()->first()
		]);
	});
});
