<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\Response as Http;

class JsonResponseException extends Exception
{
    /**
     * The status code to use for the response.
     */
    public int $status = Http::HTTP_UNPROCESSABLE_ENTITY;

    /**
     * Create a new exception instance.
     */
    public function __construct(string $message)
    {
        parent::__construct($message);
    }

    /**
     * Report the exception.
     */
    public function report(): ?bool
    {
        return false;
    }

    /**
     * Render the exception into an HTTP response.
     */
    public function render(): JsonResponse
    {
        return Response::json(
            $this->responseBody(),
            $this->status,
        );
    }

    /**
     * Json response body.
     */
    private function responseBody(): array
    {
        return [
            'status' => false,
            'message' => $this->getMessage()
        ];
    }
}
