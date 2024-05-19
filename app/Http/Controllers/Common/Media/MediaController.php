<?php

namespace App\Http\Controllers\Common\Media;

use App\Http\Controllers\Controller;
use App\Services\MediaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Response as HttpResponse;
use Symfony\Component\HttpFoundation\Response as Http;

class MediaController extends Controller
{
	public function __construct(
		private MediaService $mediaService,
	) {
	}

	/**
	 * FilePond process route for asynchronous upload.
	 */
	public function store(Request $request): HttpResponse
	{
		return Response::make(
			$this->mediaService->store($request),
			Http::HTTP_OK,
			['content-type' => 'text/plain']
		);
	}
}
