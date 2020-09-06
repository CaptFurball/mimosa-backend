<?php

namespace App\Responses;

class GenericResponse
{
    const REJECTED = 'REJECTED';
    const SUCCESS  = 'SUCCESS';
    const ERROR    = 'ERROR';

    const MALFORMED_REQUEST = 'MALFORMED_REQUEST';

    public function createResponse(string $status, string $code, array $message = [], array $error = [])
    {
        $body = [
            'status' => $status,
            'code' => $code,
        ];

        if (!empty($message)) {
            $body['message'] = $message;
        }

        if (!empty($error)) {
            $body['error'] = $error;
        }

        return response()->json($body);
    }

    public function createSuccessResponse(string $code, array $message = [])
    {
        return $this->createResponse(self::SUCCESS, $code, $message);
    }

    public function createRejectedResponse(string $code, array $error = [])
    {
        return $this->createResponse(self::REJECTED, $code, [], $error);
    }

    public function createMalformedRequestResponse(array $error = [])
    {
        return $this->createRejectedResponse(self::MALFORMED_REQUEST, $error);
    }

    public function createErrorResponse(string $code, array $error = [])
    {
        return $this->createResponse(self::ERROR, $code, [], $error);
    }
}