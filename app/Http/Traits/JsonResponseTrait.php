<?php

namespace App\Http\Traits;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as Http;

trait JsonResponseTrait
{
	/** Response context information */
	public string $message;

	/** Response body content */
	public array $body;

	/**
	 * Json Response builder.
	 */
	public function jsonResponse(): JsonResponseTrait
	{
		return $this;
	}

	/**
	 * Respond with success.
	 */
	public function success(): JsonResponse
	{
		$response = self::resource(true, $this->message);

		if (isset($this->body)) {
			$response['body'] = $this->body;
		}

		return new JsonResponse($response, Http::HTTP_OK);
	}

	/**
	 * Response with error.
	 */
	public function error(): JsonResponse
	{
		$response = self::resource(true, $this->message);

		if (isset($this->body)) {
			$response['body'] = $this->body;
		}

		return new JsonResponse($response, Http::HTTP_BAD_REQUEST);
	}

	/**
	 * Set the response message.
	 */
	public function message(string $message): JsonResponseTrait
	{
		$this->message = $message;

		return $this;
	}

	/**
	 * Set the response body.
	 */
	public function body(array $body): JsonResponseTrait
	{
		$this->body = $body;

		return $this;
	}

	/**
	 * Response payload.
	 */
	private static function resource(bool $status, string $message, ?array $body = []): array
	{
		$resource =  [
			'status' => $status,
			'message' => $message,
		];

		if (!empty($body)) {
			$resource['body'] = $body;
		}

		return $resource;
	}
}
