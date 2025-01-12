<?php

namespace App\Support\Traits;

use Carbon\Carbon;

trait ApiToken
{
    /**
     * Base64 编码后，将 "+" 替换成 "-"，"/" 替换成 "_"，并去掉 "="
     * @param $data
     * @return string
     */
    protected function base64UrlEncode($data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * Base64 解码后，将 "-" 替换成 "+"，"_" 替换成 "/"，并补齐 "="
     * @param $data
     * @return false|string
     */
    protected function base64UrlDecode($data): false|string
    {
        return base64_decode(strtr($data, '-_', '+/') . str_repeat('=', 3 - (strlen($data) + 3) % 4));
    }

    public function generateJsonWebToken(array $credentials = [], int $ttl = 0): string
    {
        // 定义头部
        $header = [
            'alg' => config('tokens.alg'), // 算法类型，可使用配置文件定义
            'typ' => 'JWT' // Token 类型，可使用配置文件定义
        ];

        $credentials = array_filter(
            $credentials,
            fn($key) => !str_contains($key, 'iss') || !str_contains($key, 'iat') || !str_contains($key, 'exp'),
            ARRAY_FILTER_USE_KEY
        );

        // 签发时间
        $issuedAt = Carbon::now();
        // 定义 payload
        $payload = array_merge([
            /**
             * JWT规定了7个官方字段：
             */
            'iss' => config('app.name'), // 签发人(issuer)
            'nbf' => '', // 生效时间(Not Before)
            'iat' => $issuedAt->timestamp, // 签发时间(Issued At)
            'exp' => $issuedAt->addSeconds($this->getTTL($ttl))->timestamp, // 过期时间(expiration time)
            'sub' => '', // 主题(subject)[自定义主题字段，如用户id]
            'aud' => '', // 受众(audience)[接收者]
            'jti' => '', // 编号(JWT ID)
        ], $credentials);

        // 编码头部和负载
        $headerEncoded = $this->base64UrlEncode(json_encode($header, JSON_NUMERIC_CHECK | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        $payloadEncoded = $this->base64UrlEncode(json_encode($payload, JSON_NUMERIC_CHECK | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

        // 生成签名
        $signatureEncoded = $this->base64UrlEncode($this->generateSignature($headerEncoded, $payloadEncoded));

        return sprintf('%s.%s.%s', $headerEncoded, $payloadEncoded, $signatureEncoded);
    }

    public function validateJsonWebToken($jwt)
    {
        // 拆分 JWT 为三部分
        $parts = explode('.', $jwt);
        if (count(($parts)) !== 3) {
            return false;
        }

        [$headerEncoded, $payloadEncoded, $signatureEncoded] = $parts;

        // 重新生成签名
        $expectedSignatureEncoded = $this->base64UrlEncode($this->generateSignature($headerEncoded, $payloadEncoded));

        // 验证签名是否匹配
        if (!hash_equals($expectedSignatureEncoded, $signatureEncoded)) {
            return false; // 签名无效
        }

        // 解码负载并验证格式
        $payload = json_decode($this->base64UrlDecode($payloadEncoded), true);
        if (!$payload) {
            return false; // 负载无效
        }

        // 验证过期时间
        if (isset($payload['exp']) && Carbon::createFromTimestamp($payload['exp'], config('app.timezone'))->lt(Carbon::now())) {
            return false; // Token 已过期
        }

        return $payload; // 返回解码后的有效负载
    }

    private function generateSignature($headerEncoded, $payloadEncoded): string
    {
        $signatureStr = sprintf('%s.%s', $headerEncoded, $payloadEncoded);

        return $this->{sprintf('get%sSignature', config('tokens.alg'))}($signatureStr);
    }

    /**
     * 对称加密 (HMAC) hash256
     *
     * @param $signatureStr
     * @return string
     */
    private function getHS256Signature($signatureStr): string
    {
        return hash_hmac('sha256', $signatureStr, config('tokens.secret'), true);
    }

    /**
     * 对称加密 (HMAC) hash384
     *
     * @param $signatureStr
     * @return string
     */
    private function getHS384Signature($signatureStr): string
    {
        return hash_hmac('sha384', $signatureStr, config('tokens.secret'), true);
    }

    /**
     * 对称加密 (HMAC) hash512
     *
     * @param $signatureStr
     * @return string
     */
    private function getHS512Signature($signatureStr): string
    {
        return hash_hmac('sha512', $signatureStr, config('tokens.secret'), true);
    }

    /**
     * 非对称加密 (RSA) rsa256
     *
     * @param $signatureStr
     * @return string
     */
    private function getRS256Signature($signatureStr): string
    {
        $privateKey = openssl_pkey_get_private('file://path/to/private.key');

        openssl_sign($signatureStr, $signature, $privateKey, OPENSSL_ALGO_SHA256);

        return $signature;
    }

    /**
     * 非对称加密 (RSA) rsa384
     *
     * @param $signatureStr
     * @return string
     */
    private function getRS384Signature($signatureStr): string
    {
        $privateKey = openssl_pkey_get_private('file://path/to/private.key');

        openssl_sign($signatureStr, $signature, $privateKey, OPENSSL_ALGO_SHA384);

        return $signature;
    }

    /**
     * 非对称加密 (RSA) rsa512
     *
     * @param $signatureStr
     * @return string
     */
    private function getRS512Signature($signatureStr): string
    {
        $privateKey = openssl_pkey_get_private('file://path/to/private.key');

        openssl_sign($signatureStr, $signature, $privateKey, OPENSSL_ALGO_SHA512);

        return $signature;
    }

    /**
     * 椭圆曲线加密 (ECDSA) es256
     *
     * @param $signatureStr
     * @return string
     */
    private function getES256Signature($signatureStr): string
    {
        $privateKey = openssl_pkey_get_private('file://path/to/private_ecdsa.key');

        openssl_sign($signatureStr, $signature, $privateKey, OPENSSL_ALGO_SHA256);

        return $signature;
    }

    /**
     * 椭圆曲线加密 (ECDSA) es384
     *
     * @param $signatureStr
     * @return string
     */
    private function getES384Signature($signatureStr): string
    {
        $privateKey = openssl_pkey_get_private('file://path/to/private_ecdsa.key');

        openssl_sign($signatureStr, $signature, $privateKey, OPENSSL_ALGO_SHA384);

        return $signature;
    }

    /**
     * 椭圆曲线加密 (ECDSA) es512
     *
     * @param $signatureStr
     * @return string
     */
    private function getES512Signature($signatureStr): string
    {
        $privateKey = openssl_pkey_get_private('file://path/to/private_ecdsa.key');

        openssl_sign($signatureStr, $signature, $privateKey, OPENSSL_ALGO_SHA512);

        return $signature;
    }

    private function getTTL($ttl)
    {
        return $ttl === 0 ? config('tokens.ttl') : $ttl;
    }
}