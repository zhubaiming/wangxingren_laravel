<?php

use App\Services\Wechat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/payment/jsapi', function (Request $request, Wechat\MiniProgramPaymentService $service) {
    /*
     * $remote_addr => ''
     * $http_x_forwarded_for => ''
     * $request => 'POST /wechat_notify/payment/jsapi HTTP/1.1'
     * $time_local => '30/Dec/2024:07:32:37 +0000'
     *
     * 验签的签名值
     * $http_wechatpay_signature => 'pv5xBCsPCv8hfhUzg5MEXs9v0GFGYOWF92KjQBJZlLkjxxA/lEicXxE4YnN5rwNndMhuqdbFq1DX9Y/LdEysMikwFQf9gJLUBxaiqYhTqwi/j1km5UYChRwfKJDRBWF+v5xLeB+WuAx71L/fJi3BbQSLronqJkJjhuokagp3aksr5O66IC0l22QhqBaoRQSlaH3r8k0hyZzxUlqh49dgUZbXVsX63U5qNUddYdKtC11iLxtXA9FKA/U8/5ahZ1IKImEENlAH9/BjNYQie+blU/ojgEtP08P9IiWDytHVvejfvqWZLmo9/AWRF8vckzsMTPwkQC3y+i8BDVjR3DzgRw=='
     *
     * 验签的随机字符串
     * $http_wechatpay_nonce => 'tUk8R6HclVWTvmfOiXOtk3gTnLJv1Owt'
     *
     * 验签的时间戳
     * $http_wechatpay_timestamp => '1735543956'
     *
     * 验签的微信支付平台证书序列号/微信支付公钥ID
     * $http_wechatpay_serial => 'A9256DD5CB30E9FD7482299A7C36670BFC13900
     *
     * $http_request_id => '',
     * $request_body => [
     *                         回调通知的唯一编号
     *                         'id' => '35fade70-eb26-57cc-84ac-550cd792de9f',
     *                         本次回调通知创建的时间
     *                         'create_time' => '2024-12-30T15:32:36+08:00',
     *                         通知的资源数据类型，固定为encrypt-resource
     *                         'resource_type' => 'encrypt-resource',
     *                         微信支付回调通知的类型
     *                         'event_type' => 'TRANSACTION.SUCCESS',
     *                         微信支付对回调内容的摘要备注
     *                         'summary' => '支付成功',
     *                         通知资源数据
     *                         'resource' => [
     *                                           加密前的对象类型，为transaction
     *                                           'original_type' => 'transaction',
     *                                            回调数据密文的加密算法类型，目前为AEAD_AES_256_GCM，开发者需要使用同样类型的数据进行解密
     *                                           'algorithm' => 'AEAD_AES_256_GCM',
     *                                           Base64编码后的回调数据密文，商户需Base64解码并使用APIV3密钥解密，具体参考如何解密证书和回调报文
     *                                           'ciphertext' => 'OZM2fmdQF7YFdahOuNvcS2ZW8IDhLygSY/ltlXNBIk5sPNxj5QyFok8/kD//URt9sybRW2qaxuDdNdY5OlUf1EX2w0n3Ifv/J2Y6pbuexGZdKJ8uKNG9RIgthz0j9U/K7adVFqfENNx+UWqdMOgJddNJ4vLU8aa3oOyAXf3PaZ+6hpaHbNC6/ketBBW42mjUakIP8rWcHaas/UtkE3gzkM/hA5RrtawQT/sZcZFx9nDkd1bv5TAihJ5Scp3AGXaVFO5uah4wycnhopaq53KWT+/D8u3+C41vGx9xdrGuxO/hHIISgpG0ysCJu4M1r1V04rz3mb+IYQ1zx13sTkLeeTgUDy3Hzr2IGlXH5nhAcfyPDovpjj2Mr02witNLQai+l1iV6d9Jld7Sxp9J0L7HEcIByATkEj2YG8INmP0GYRa2cjtS1tVsqK30AXjK+htVH7NYfxA0fSjDqbSi2tslqw/wiF90r9pvyH4jlkDUp5eECcYFocrWg47rG98e4o9vs6zutFG0OMnzJscnaAsuxyjizPv8w1s1glulAVJny12erSgbmHmjkI0TcUOm41ddx0=',
     *                                           参与解密的附加数据，该字段可能为空
     *                                           'associated_data' => 'transaction',
     *                                           参与解密的随机串
     *                                           'nonce' => 'zbCwOIWVvwnj'
     *                                       ]
     *                     ]
     */


//    dd($request->headers);
    $service->decryptNotify($request);
//    dd($service->decryptNotify($request));
});

//{\"id\":\"48fd6bb9-7c28-59cf-a260-aa416af6cfde\",\"create_time\":\"2024-12-30T16:15:41+08:00\",\"resource_type\":\"encrypt-resource\",\"event_type\":\"TRANSACTION.SUCCESS\",\"summary\":\"支付成功\",\"resource\":{\"original_type\":\"transaction\",\"algorithm\":\"AEAD_AES_256_GCM\",\"ciphertext\":\"I1OsIh+fs0DIsM2QZh2vcguaelPL1XDEDyNZCBI05Z+8h3JIdk/iHqEFVAHq6O26wb17++HHbYlQYTWC2YSp7nFPYG/x2AN/tUL9gJ3Zn0WY3eXukbkiP2gbzSagQdt33RPKrlvAZjOfUKzDY8285LFMh651B/gUsy7sPv84U+kDvSZH6xMlyVy2VtRACf6yjo1HIprL3vQe2TnQf9cX+IDU2Xbo7jtKvejCuK0qiQPNFED/Jh2q9iIWNTWgelGDsavZKQAXOaTFzYH7h0xhEXDUaH5d5/rid7EqUjpG6qDo3a0T6VMqib4Xt0IC4mHiX2xRZHtWPzSnC3aSX5PHKlSpgah8iEqJGbo5ogkF6xD5iIRYDXDI/pQ5+NjfDjTPSmSHms6+JrDC8Ye9+y1mtanhAhk2TFCfmu+ysopm/2AHGOrCc11r5FdFo23tvQ3Pt+7dYZ0j4cSwm50wSWGBoXNjnB+7GQ6myWdT345wqOJPRX6FQ62gqcAwtri8w0WcKka62wCT/FjngdVYeHCipnQbAFnrkdtaGwkAElkEwEayOby/UxTlZDA6KC01Z3fOdyU=\",\"associated_data\":\"transaction\",\"nonce\":\"8kYXCUige8kA\"}}
