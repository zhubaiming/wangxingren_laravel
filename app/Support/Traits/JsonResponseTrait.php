<?php

namespace App\Support\Traits;

trait JsonResponseTrait
{
    protected $response_headers = [
        'Content-Type' => 'application/json'
    ];

//    public function withHeaders(array $headers)
//    {
//        $this->response_headers = $headers;
//    }
//
//
//    /**
//     *  Respond with an accepted response and associate a location and/or content if provided.
//     *
//     * @param array $data
//     */
//    public function accepted($data = [], string $message = '', string $location = ''): JsonResponse
//    {
//        return tap($this->success($data, $message, 202), function ($response) use ($location) {
//            if ($location) {
//                $response->header('Location', $location);
//            }
//        });
//    }
//
//    /**
//     * Respond with a created response and associate a location if provided.
//     *
//     * @param null $data
//     */
//    public function created($data = [], string $message = '', string $location = '')
//    {
//        return tap($this->success($data, $message, 201), function ($response) use ($location) {
//            if ($location) {
//                $response->header('Location', $location);
//            }
//        });
//    }
//
//    /**
//     * Respond with a no content response.
//     */
//    public function noContent(string $message = 'success')
//    {
//        return $this->success(message: $message, code: 204);
//    }
//
////    /**
////     * Alias of success method, no need to specify data parameter.
////     */
////    public function ok(string $message = 'success', int|\BackedEnum $code = 200): JsonResponse
////    {
////        return $this->success($message, code: $code);
////    }
//
//    /**
//     * Alias of the successful method, no need to specify the message and data parameters.
//     * You can use ResponseCodeEnum to localize the message.
//     */
//    public function localize(int|\BackedEnum $code = 200): JsonResponse
//    {
//        return $this->ok(code: $code);
//    }
//
//    /**
//     * Return a 400 bad request error.
//     */
//    public function errorBadRequest(string $message = ''): JsonResponse
//    {
//        return $this->fail($message, 400);
//    }
//
//    /**
//     * Return a 401 unauthorized error.
//     */
//    public function errorUnauthorized(string $message = ''): JsonResponse
//    {
//        return $this->fail($message, 401);
//    }
//
//    /**
//     * Return a 403 forbidden error.
//     */
//    public function errorForbidden(string $message = ''): JsonResponse
//    {
//        return $this->fail($message, 403);
//    }
//
//    /**
//     * Return a 404 not found error.
//     */
//    public function errorNotFound(string $message = ''): JsonResponse
//    {
//        return $this->fail($message, 404);
//    }
//
//    /**
//     * Return a 405 method not allowed error.
//     */
//    public function errorMethodNotAllowed(string $message = ''): JsonResponse
//    {
//        return $this->fail($message, 405);
//    }
//
//    public function fail(string $error_code = '', string $message = '')
//    {
//        return response()->json([
//            'code' => $error_code,
//
//        ]);
//    }
//
////    public function success($data = [], string $message = '', int $code = 200)
////    {
////        return response()->json([
////            'code' => '0',
////            'message' => __('http_response.' . $message)
////        ], $code);
////    }
//
////    /**
////     * Alias of success method, no need to specify data parameter.
////     */
////    public function ok(string $message = 'success', int|\BackedEnum $code = 200): JsonResponse
////    {
////        return $this->success($message, code: $code);
////    }
//
//    public function noData(string $error_code = '0', string $message = 'success')
//    {
//        return response()->json([
//            'code' => $error_code,
//            'message' => __('http_response.' . $message)
//        ]);
//    }

    public function success(int $error_code = 200, string $message = 'success', array $data = [])
    {
        return response()->json([
            'code' => $error_code,
            'message' => __('http_response.' . $message),
            'payload' => $data
        ]);
    }
}