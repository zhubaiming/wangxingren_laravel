<?php

namespace App\Services\Wechat;

use App\Enums\OrderStatusEnum;
use App\Events\NewPayedOrderEvent;
use App\Models\ClientUserOrder;
use Carbon\Carbon;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use WeChatPay\Builder;
use WeChatPay\Crypto\AesGcm;
use WeChatPay\Crypto\Rsa;
use WeChatPay\Formatter;
use WeChatPay\Util\PemUtil;

class MiniProgramPaymentService
{
    private $url_path = 'v3/pay/transactions/';

    /**
     * 商户号
     *
     * @var mixed
     */
    private $merchantId;

    /**
     * 「商户API证书」的「证书序列号」
     *
     * @var
     */
    private $merchantCertificateSerial;

    /**
     * 商户API私钥
     *
     * @var
     */
    private $merchantPrivateKeyFilePath;

    private $merchantPrivateKeyInstance;

    /**
     * 微信支付平台证书
     *
     * @var
     */
    private $platformCertificateFilePath;

    private $platformPublicKeyInstance;

    /**
     * 从「微信支付平台证书」中获取「证书序列号」
     *
     * @var
     */
    private $platformCertificateSerial;

    /**
     * APIv3 客户端实例
     *
     * @var
     */
    private $instance;

    public function __construct()
    {
        $this->merchantId = config('wechat.merchant.mchid');

        $this->merchantCertificateSerial = config('wechat.merchant.apiclient_serial');

        $this->merchantPrivateKeyFilePath = 'file:' . DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR . storage_path('app' . DIRECTORY_SEPARATOR . 'private' . DIRECTORY_SEPARATOR . 'apiclient_key.pem');

        $this->platformCertificateFilePath = 'file:' . DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR . storage_path('app' . DIRECTORY_SEPARATOR . 'private' . DIRECTORY_SEPARATOR . 'cert.pem');

        $this->createWeChatPayInstance();
    }

    /**
     * 使用「商户API私钥」生成请求的签名
     *
     * @return void
     */
    private function cryptoRsaPrivateKeyInstance()
    {
        $this->merchantPrivateKeyInstance = Rsa::from($this->merchantPrivateKeyFilePath, Rsa::KEY_TYPE_PRIVATE);
    }

    /**
     * 使用「微信支付平台证书」来验证微信支付应答的签名
     *
     * @return void
     */
    private function cryptoRsaPublicKeyInstance()
    {
        $this->platformPublicKeyInstance = Rsa::from($this->platformCertificateFilePath, Rsa::KEY_TYPE_PUBLIC);
    }

    private function setCertificateSerial()
    {
        $this->platformCertificateSerial = PemUtil::parseCertificateSerialNo($this->platformCertificateFilePath);
    }

    private function createWeChatPayInstance()
    {
        $this->cryptoRsaPrivateKeyInstance();

        $this->cryptoRsaPublicKeyInstance();

        $this->setCertificateSerial();

        $this->instance = Builder::factory([
            'mchid' => $this->merchantId,
            'serial' => $this->merchantCertificateSerial,
            'privateKey' => $this->merchantPrivateKeyInstance,
            'certs' => [
                $this->platformCertificateSerial => $this->platformPublicKeyInstance,
            ],
        ]);
    }

    /**
     * 小程序下单
     *
     * @param string $out_trade_no
     * @param int $total
     * @param string $openid
     * @return array
     */
    public function requestPayment(string $out_trade_no, int $total, string $openid, string $description = '')
    {
        try {
            $http_response = $this->instance->chain($this->url_path . 'jsapi')
                ->post(['json' => [
                    'appid' => config('wechat.miniprogram.app_id'), // 公众号ID
                    'mchid' => $this->merchantId, // 直连商户号
                    'description' => $description, // 商品描述
//                    'out_trade_no' => app()->isLocal() ? 'test_payment_' . rand(100000, 999999) : $out_trade_no, // 商户系统内部订单号，只能是数字、大小写字母_-*且在同一个商户号下唯一
                    'out_trade_no' => $out_trade_no, // 商户系统内部订单号，只能是数字、大小写字母_-*且在同一个商户号下唯一
//                'time_expire' => '', // 订单失效时间(非必填)，格式为yyyy-MM-DDTHH:mm:ss+TIMEZONE，yyyy-MM-DD表示年月日，T出现在字符串中，表示time元素的开头，HH:mm:ss表示时分秒，TIMEZONE表示时区（+08:00表示东八区时间，领先UTC8小时，即北京时间）。例如：2015-05-20T13:29:35+08:00表示，北京时间2015年5月20日13点29分35秒
//                'attach' => 'attach_test_123456789', // 附加数据(非必填)，在查询API和支付通知中原样返回，可作为自定义参数使用，实际情况下只有支付完成状态才会返回该字段
                    'notify_url' => 'https://wangxingren.fun/wechat_notify/payment/jsapi', // 异步接收微信支付结果通知的回调地址，通知URL必须为外网可访问的URL，不能携带参数。 公网域名必须为HTTPS，如果是走专线接入，使用专线NAT IP或者私有回调域名可使用HTTP
//                'goods_tag' => '', // 订单优惠标记(非必填)
//                'support_fapiao' => false, // 电子发票入口开放标识(非必填)，传入true时，支付成功消息和支付详情页将出现开票入口。需要在微信支付商户平台或微信公众平台开通电子发票功能，传此字段才可生效。true：是   false：否
                    'amount' => [ // 订单金额信息
                        'total' => app()->isLocal() ? 1 : $total, // 订单总金额，单位为分
                        'currency' => 'CNY' // 货币类型，CNY：人民币，境内商户号仅支持人民币
                    ],
                    'payer' => [ // 支付者信息
                        'openid' => app()->isLocal() ? env('LOCAL_WECHAT_OPENID') : $openid// 用户在普通商户AppID下的唯一标识。 下单前需获取到用户的OpenID
                    ],
//                'detail' => [ // 优惠功能(非必填)
//                    'cost_price' => 1, // 订单原价(非必填) 1、商户侧一张小票订单可能被分多次支付，订单原价用于记录整张小票的交易金额。2、当订单原价与支付金额不相等，则不享受优惠。3、该字段主要用于防止同一张小票分多次支付，以享受多次优惠的情况，正常支付订单不必上传此参数。
//                    'invoice_id' => '', // 商家小票ID(非必填)
//                    'goods_detail' => [ // 单品列表信息，条目个数限制：【1，6000】
//                        'merchant_goods_id' => '', // 商户侧商品编码，由半角的大小写字母、数字、中划线、下划线中的一种或几种组成
//                        'wechatpay_goods_id' => '', // 微信支付定义的统一商品编号(非必填)（没有可不传）
//                        'goods_name' => '', // 商品的实际名称(非必填)
//                        'quantity' => 1, // 用户购买的数量
//                        'unit_price' => '' // 商品单价，单位为：分。如果商户有优惠，需传输商户优惠后的单价(例如：用户对一笔100元的订单使用了商场发的纸质优惠券100-50，则活动商品的单价应为原单价-50)
//                    ],
//                ],
//                'scene_info' => [ // 支付场景描述(非必填)
//                    'payer_client_ip' => '', // 用户的客户端IP，支持IPv4和IPv6两种格式的IP地址
//                    'device_id' => '', // 商户端设备号(非必填)（门店号或收银设备ID）
//                    'store_info' => 1 // 商户门店信息(非必填)
//                ],
                    'settle_info' => [ // 结算信息(非必填)
                        'profit_sharing' => false // 是否指定分账(非必填)，true：是，false：否
                    ]
                ]]);

            // 预支付交易会话标识，用于后续接口调用中使用，该值有效期为2小时
            $prepay_id = (json_decode($http_response->getBody(), true))['prepay_id'];

            $timeStamp = strval(Formatter::timestamp());
            $nonceStr = Formatter::nonce();

            return [
                'timeStamp' => $timeStamp,
                'nonceStr' => $nonceStr,
                'package' => 'prepay_id=' . $prepay_id,
                'signType' => 'RSA',
                'paySign' => Rsa::sign(Formatter::joinedByLineFeed(...array_values([
                    'appId' => config('wechat.miniprogram.app_id'),
                    'timeStamp' => $timeStamp,
                    'nonceStr' => $nonceStr,
                    'package' => 'prepay_id=' . $prepay_id
                ])), $this->merchantPrivateKeyInstance)];
        } catch (RequestException $requestException) {
            $r = $requestException->getResponse();

            return [
                'code' => $r->getStatusCode(),
                'status' => -1,
                'message' => $requestException->getMessage(),
                'data' => [
                    'reason_phrase' => $r->getReasonPhrase(),
                    'body' => json_decode($r->getBody(), true),
                    'trace_as_string' => $requestException->getTraceAsString()
                ]
            ];
        }
    }

    /**
     * 支付通知
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function decryptNotify(Request $request)
    {
        $wechatpay_header_signature = $request->header('wechatpay-signature');         // 请求头部 - Wechatpay-Signature(请根据实际情况获取)
        $wechatpay_header_nonce = $request->header('wechatpay-nonce');                 // 请求头部 - Wechatpay-Nonce(请根据实际情况获取)
        $wechatpay_header_timestamp = strval($request->header('wechatpay-timestamp')); // 请求头部 - Wechatpay-Timestamp(请根据实际情况获取)
        $wechatpay_header_serial = $request->header('wechatpay-serial');               // 请求头部 - Wechatpay-Serial(请根据实际情况获取)
//        $wechatpay_body = $request->post();                                                 // 请根据实际情况获取，例如: file_get_contents('php://input');
        $wechatpay_body = file_get_contents('php://input');

        // 检查通知时间偏移量，允许5分钟之内的偏移
        $timeOffsetStatus = 300 >= intval(abs(bcsub(Formatter::timestamp(), (int)$wechatpay_header_timestamp, 0)));
        $verifiedStatus = Rsa::verify(
        // 构造验签名串
            Formatter::joinedByLineFeed($wechatpay_header_timestamp, $wechatpay_header_nonce, $wechatpay_body),
            $wechatpay_header_signature,
            $this->platformPublicKeyInstance
        );

        $log = 'timeOffsetStatus: ' . ($timeOffsetStatus ? 'true' : 'false') . ', verifiedStatus: ' . ($verifiedStatus ? 'true' : 'false');
        Log::channel('test')->info($log);

        if ($timeOffsetStatus && $verifiedStatus) {
            // 使用PHP7的数据解构语法，从Array中解构并赋值变量
            ['resource' => [
                'ciphertext' => $ciphertext,
                'nonce' => $nonce,
                'associated_data' => $aad
            ]] = json_decode($wechatpay_body, true);
            // 加密文本消息解密
            $wechatpay_body_resource = AesGcm::decrypt($ciphertext, config('wechat.merchant.api_v3_key'), $nonce, $aad);
            Log::channel('test')->info($wechatpay_body_resource);
            // 把解密后的文本转换为PHP Array数组
            $wechatpay_body_resource_array = json_decode($wechatpay_body_resource, true);

            if ('SUCCESS' === $wechatpay_body_resource_array['trade_state']) {
                ClientUserOrder::where('pay_channel', 1)->where('trade_no', $wechatpay_body_resource_array['out_trade_no'])->update([
                    'mchid' => $wechatpay_body_resource_array['mchid'],
                    'transaction_id' => $wechatpay_body_resource_array['transaction_id'],
                    'trade_type' => $wechatpay_body_resource_array['trade_type'],
                    'bank_type' => $wechatpay_body_resource_array['bank_type'],
                    'pay_success_at' => Carbon::parse($wechatpay_body_resource_array['success_time'], config('app.timezone')),
                    'currency' => $wechatpay_body_resource_array['amount']['currency'],
                    'payer_currency' => $wechatpay_body_resource_array['amount']['payer_currency'],
                    'status' => OrderStatusEnum::finishing
                ]);

                NewPayedOrderEvent::dispatch();
            }
        }

        return response()->json();
    }

    /**
     * 微信支付订单号查询订单
     *
     * @return
     */
    public function selectOrderByTransactionId(string $transaction_id)
    {
        $http_response = $this->instance->chain($this->url_path . 'id/' . $transaction_id)
            ->get(['json' => [
                'mchid' => $this->merchantId
            ]]);
    }

    /**
     * 商户订单号查询订单
     *
     * @return
     */
    public function selectOrderByOutTradeNo(string $out_trade_no)
    {
        $http_response = $this->instance->chain($this->url_path . 'out-trade-no/' . $out_trade_no)
            ->get(['json' => [
                'mchid' => $this->merchantId
            ]]);
    }

    /**
     * 关闭订单
     *
     * @return
     */
    public function requestClosePayment(string $out_trade_no)
    {
        $http_response = $this->instance->chain($this->url_path . 'out-trade-no/' . $out_trade_no . '/close')
            ->post(['json' => [
                'mchid' => $this->merchantId
            ]]);
    }

    /**
     * 退款申请
     *
     * @param string $transaction_id
     * @param string $out_refund_no
     * @param int $amount
     * @param int $total
     * @param string|null $reason
     * @return array|bool
     */
    public function requestRefund(string $transaction_id, string $out_refund_no, int $amount, int $total, string $reason = null)
    {
        try {
            $http_response = $this->instance->chain('v3/refund/domestic/refunds')
                ->post(['json' => [
                    'transaction_id' => $transaction_id, // 微信支付订单号
//                    'out_trade_no' => '',
                    'out_refund_no' => $out_refund_no,
                    'reason' => $reason, // 1、该退款原因参数的长度不得超过80个字节；2、当订单退款金额小于等于1元且为部分退款时，退款原因将不会在消息中体现
                    'notify_url' => 'https://wangxingren.fun/wechat_notify/domestic/refunds',
//                    'funds_account' => '',
                    'amount' => [
                        'refund' => $amount, // 退款金额，币种的最小单位，只能为整数，不能超过原订单支付金额
                        'from' => [
                            'account' => 'UNAVAILABLE',
                            'amount' => $amount
                        ],
                        'total' => $total, // 原支付交易的订单总金额，币种的最小单位，只能为整数
                        'currency' => 'CNY' // 符合ISO 4217标准的三位字母代码，固定传：CNY，代表人民币
                    ],
//                    'goods_detail' => [
//                        [
//                            'merchant_goods_id' => '',
//                            'wechatpay_goods_id' => '',
//                            'goods_name' => '',
//                            'unit_price' => '',
//                            'refund_amount' => '',
//                            'refund_quantity' => ''
//                        ]
//                    ]
                ]]);

            return json_decode($http_response->getBody(), true);
        } catch (RequestException $requestException) {
            return false;
//            $r = $requestException->getResponse();
//
//            return [
//                'code' => $r->getStatusCode(),
//                'status' => -1,
//                'message' => $requestException->getMessage(),
//                'data' => [
//                    'reason_phrase' => $r->getReasonPhrase(),
//                    'body' => json_decode($r->getBody(), true),
//                    'trace_as_string' => $requestException->getTraceAsString()
//                ]
//            ];
        }
    }

    /**
     * 查询单笔退款（通过商户退款单号）
     *
     * @return
     */
    public function selectRefundByOutTradeNo(string $out_trade_no)
    {
        $http_response = $this->instance->chain('v3/refund/domestic/refunds/' . $out_trade_no)
            ->get(['json' => [
                'mchid' => $this->merchantId
            ]]);
    }

    /**
     * 退款结果通知
     *
     * @param Request $request
     * @return bool
     */
    public function decryptNotify1(Request $request)
    {
        $wechatpay_header_signature = $request->header('wechatpay-signature');         // 请求头部 - Wechatpay-Signature(请根据实际情况获取)
        $wechatpay_header_nonce = $request->header('wechatpay-nonce');                 // 请求头部 - Wechatpay-Nonce(请根据实际情况获取)
        $wechatpay_header_timestamp = strval($request->header('wechatpay-timestamp')); // 请求头部 - Wechatpay-Timestamp(请根据实际情况获取)
        $wechatpay_header_serial = $request->header('wechatpay-serial');               // 请求头部 - Wechatpay-Serial(请根据实际情况获取)
        $wechatpay_body = $request->post();                                                 // 请根据实际情况获取，例如: file_get_contents('php://input');

        // 检查通知时间偏移量，允许5分钟之内的偏移
        $timeOffsetStatus = app()->isLocal() ? true : 300 >= intval(abs(bcsub(Formatter::timestamp(), $wechatpay_header_timestamp, 0)));
        $verifiedStatus = Rsa::verify(
        // 构造验签名串
            Formatter::joinedByLineFeed($wechatpay_header_timestamp, $wechatpay_header_nonce, json_encode($wechatpay_body, 320)),
            $wechatpay_header_signature,
            $this->platformPublicKeyInstance
        );

        if ($timeOffsetStatus && $verifiedStatus) {
            // 使用PHP7的数据解构语法，从Array中解构并赋值变量
            ['resource' => [
                'ciphertext' => $ciphertext,
                'nonce' => $nonce,
                'associated_data' => $aad
            ]] = $wechatpay_body;
            // 加密文本消息解密
            $wechatpay_body_resource = AesGcm::decrypt($ciphertext, config('wechat.merchant.api_v3_key'), $nonce, $aad);
            // 把解密后的文本转换为PHP Array数组
            $wechatpay_body_resource_array = json_decode($wechatpay_body_resource, true);
            dd($wechatpay_body_resource_array);// 打印解密后的结果
            /*
             *  [
                    "mchid" => "1680836934"                            // 商户的商户号，由微信支付生成并下发
                    "appid" => "wxd8bcfa43ca3fb256"                    // 直连商户申请的公众号或移动应用AppID
                    "out_trade_no" => "test_payment_497783"            // 商户系统内部订单号，可以是数字、大小写字母_-*的任意组合且在同一个商户号下唯一
                    "transaction_id" => "4200002387202410098296855473" // 微信支付系统生成的订单号
                    "trade_type" => "JSAPI"                            // 交易类型，枚举值：JSAPI：公众号支付，NATIVE：扫码支付，App：App支付，MICROPAY：付款码支付，MWEB：H5支付，FACEPAY：刷脸支付
                    "trade_state" => "SUCCESS"                         // 交易状态，枚举值：SUCCESS：支付成功，REFUND：转入退款，NOTPAY：未支付，CLOSED：已关闭，REVOKED：已撤销（付款码支付），USERPAYING：用户支付中（付款码支付），PAYERROR：支付失败(其他原因，如银行返回失败)
                    "trade_state_desc" => "支付成功"                    // 交易状态描述
                    "bank_type" => "OTHERS"                            // 银行类型，采用字符串类型的银行标识。银行标识请参考：https://pay.weixin.qq.com/docs/merchant/development/chart/bank-type.html
                    "attach" => ""                                     // 附加数据(选填)，在查询API和支付通知中原样返回，可作为自定义参数使用，实际情况下只有支付完成状态才会返回该字段
                    "success_time" => "2024-10-09T21:33:59+08:00"      // 支付完成时间，格式为yyyy-MM-DDTHH:mm:ss+TIMEZONE
                    "payer" => [                                       // 支付者信息
                        "openid" => "oaQAW7UWF6z-jH6YljVJi4uvtdI4"     // 用户在直连商户AppID下的唯一标识
                    ]
                    "amount" => [                                      // 订单金额信息
                        "total" => 1                                   // 订单总金额，单位为分
                        "payer_total" => 1                             // 用户支付金额，单位为分
                        "currency" => "CNY"                            // CNY：人民币，境内商户号仅支持人民币
                        "payer_currency" => "CNY"                      // 用户支付币种
                    ]
                ]
             */
        }

        return $timeOffsetStatus && $verifiedStatus;
    }
}