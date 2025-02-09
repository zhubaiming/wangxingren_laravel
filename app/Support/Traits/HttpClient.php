<?php

namespace App\Support\Traits;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

trait HttpClient
{
    private $retry_times = 3;

    private $retry_sleep_milliseconds = 2000;

    public function __construct()
    {
        if (app()->isLocal()) {
            Http::fake([
                /*
                 * 失败 - 状态200
                 * {
                 *   "errcode": 41002,
                 *   "errmsg": "appid missing rid: 6705d19f-57bc9262-5556641d"
                 * }
                 * 成功
                 * {
                 *   "access_token": "85_fJRWezfh5Hvi3obSQ3joEMNixWBRjs1SICjgQAld7k2I5bvy0D7tgeSvlcUCpxeq9UJrcQK0eyloqv43lySKKtkBwwlGFth3cIM5YrHOKUduACc9UvwNbtffs80OXCeADAAJO",
                 *   "expires_in": 7200
                 * }
                 */
                // 获取接口调用凭据
                'https://api.weixin.qq.com/cgi-bin/token*' => Http::response([
                    'access_token' => '85_fJRWezfh5Hvi3obSQ3joEMNixWBRjs1SICjgQAld7k2I5bvy0D7tgeSvlcUCpxeq9UJrcQK0eyloqv43lySKKtkBwwlGFth3cIM5YrHOKUduACc9UvwNbtffs80OXCeADAAJO',
                    'expires_in' => 7200
                ], 200),
                // 获取稳定版接口调用凭据
                'https://api.weixin.qq.com/cgi-bin/stable_token*' => Http::response([
                    'access_token' => '85_1Bj8Ls06_RGcbfYRxtckzmIO9sbPESFFp5qakbq_t_tjkovfjCiNM5Hl0HiZQCtlIx1FXZfEJ9Cfp2wkY77M7EYqkzXdWE2H-QhbmtIhBS1F9BdyqGWSQ42s9f4JEPhAJADYJ',
                    'expires_in' => 7200
                ], 200),
                // 小程序登录
                'https://api.weixin.qq.com/sns/jscode2session*' => Http::response([
                    'session_key' => 'YBmLfJzSL4jlGGQf26tV+A==',
                    'unionid' => null,
                    'errmsg' => null,
                    'openid' => 'oaQAW7UWF6z-jH6YljVJi4uvtdI4',
//                    'openid' => 'oaQAW7RBqF5QGJInV83RLfb6IFEg',
//                    'openid' => 'test1',
                    'errcode' => null
                ], 200),
                // 支付后获取 Unionid
                'https://api.weixin.qq.com/wxa/getpaidunionid*' => Http::response([
                    'unionid' => 'oTmHYjg-tElZ68xxxxxxxxhy1Rgk',
                    'errcode' => 0,
                    'errmsg' => 'ok'
                ], 200),
                // 获取手机号
                'https://api.weixin.qq.com/wxa/business/getuserphonenumber*' => Http::response([
                    'errcode' => 0,
                    'errmsg' => 'ok',
                    'phone_info' => [
                        'phoneNumber' => '13811111111',
                        'purePhoneNumber' => '13811111111',
                        'countryCode' => '86',
                        'watermark' => [
                            'timestamp' => 1727673918,
                            'appid' => 'wxd8bcfa43ca3fb256'
                        ]
                    ]
                ], 200),
                '*' => Http::response(['message' => 'http fake'], 200),
            ]);
        }
    }

    private function joinUrl($uri = null): string
    {
        return $this->base_url . $uri;
    }

    private function sendRequest($url, $method, $parameters = [], $data = [])
    {
        try {
            $http = Http::acceptJson()->retry($this->retry_times, $this->retry_sleep_milliseconds)->withQueryParameters($parameters)->throw();

            $http_response = match (strtolower($method)) {
                'get' => $http->get($url),
                'post' => $http->post($url, $data),
            };

            return $this->processResponse($http_response)->json();
        } catch (ConnectionException $connectionException) { // 超时
            dd('超时');
        } catch (RequestException $requestException) { // 所有请求都失败
//            dd('失败');

            dd([
                'code' => $requestException->getCode(),
                'message' => $requestException->getMessage(),
                'response' => $requestException->response
            ]);

            if ($http_response->badRequest()) { // 400 Bad Request
                dd([
                    'code' => 400,
                    'message' => 'bad request',
                    'response' => $http_response
                ]);
            } elseif ($response->unauthorized()) { // 401 Unauthorized
                dd([
                    'code' => 401,
                    'message' => 'unauthorized',
                    'response' => $response
                ]);
            } elseif ($response->paymentRequired()) { // 402 Payment Required
                dd([
                    'code' => 402,
                    'message' => 'payment required',
                    'response' => $response
                ]);
            } elseif ($response->forbidden()) { // 403 Forbidden
                dd([
                    'code' => 403,
                    'message' => 'forbidden',
                    'response' => $response
                ]);
            } elseif ($response->notFound()) { // 404 Not Found
                dd([
                    'code' => 404,
                    'message' => 'not found',
                    'response' => $response
                ]);
            } elseif ($response->requestTimeout()) { // 408 Request Timeout
                dd([
                    'code' => 408,
                    'message' => 'request timeout',
                    'response' => $response
                ]);
            } elseif ($response->conflict()) { // 409 Conflict
                dd([
                    'code' => 409,
                    'message' => 'conflict',
                    'response' => $response
                ]);
            } elseif ($response->unprocessableEntity()) { // 422 Unprocessable Entity
                dd([
                    'code' => 422,
                    'message' => 'unprocessable entity',
                    'response' => $response
                ]);
            } elseif ($response->tooManyRequests()) { // 429 Too Many Requests
                dd([
                    'code' => 429,
                    'message' => 'too many requests',
                    'response' => $response
                ]);
            } elseif ($response->serverError()) { // 500 Internal Server Error
                dd([
                    'code' => 500,
                    'message' => 'internal server error',
                    'response' => $response
                ]);
            } else {
                dd([
                    'code' => $response->status(),
                    'message' => '5xx',
                    'response' => $response
                ]);
            }
        }
    }

    private function processResponse($response)
    {
        if ($response->successful()) {
            if ($response->ok()) { // 200 OK
                return $response;
            } elseif ($response->created()) { // 201 Created
                dd([
                    'code' => 201,
                    'message' => 'created',
                    'response' => $response
                ]);
            } elseif ($response->accepted()) { // 202 Accepted
                dd([
                    'code' => 202,
                    'message' => 'accepted',
                    'response' => $response
                ]);
            } elseif ($response->noContent()) { // 204 No Content
                dd([
                    'code' => 204,
                    'message' => 'no content',
                    'response' => $response
                ]);
            } else {
                dd([
                    'code' => $response->status(),
                    'message' => '2xx',
                    'response' => $response
                ]);
            }
        } else {
            if ($response->movedPermanently()) { // 301 Moved Permanently
                dd([
                    'code' => 301,
                    'message' => 'moved permanently',
                    'response' => $response
                ]);
            } elseif ($response->found()) { // 302 Found
                dd([
                    'code' => 302,
                    'message' => 'found',
                    'response' => $response
                ]);
            } else {
                dd([
                    'code' => $response->status(),
                    'message' => '3xx',
                    'response' => $response
                ]);
            }
        }
    }
}