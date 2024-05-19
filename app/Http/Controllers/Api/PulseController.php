<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\ApiBaseController;
use Symfony\Component\HttpFoundation\Response as Http;
use App\Services\PulseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PulseController extends ApiBaseController
{
	public function __construct(
		private PulseService $pulseService,
	) {
	}

	/**
	 * Show the resource.
	 */
	public function show(Request $request): JsonResponse
	{
		$pulse = $this->pulseService;

		if (!$pulse->validateAuthorizationRequest($request)) {
			return response()->json([
				'message' => Http::$statusTexts[Http::HTTP_FORBIDDEN]
			], Http::HTTP_FORBIDDEN);
		}

		return response()->json([
			'dependencies' => $pulse->getPackagesAndDependencies(),
		]);
	}
}
