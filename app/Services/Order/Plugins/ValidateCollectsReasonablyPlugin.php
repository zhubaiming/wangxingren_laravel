<?php

namespace App\Services\Order\Plugins;

use App\Enums\PayChannelEnum;
use App\Enums\ResponseEnum;
use App\Exceptions\BusinessException;
use App\Models\ClientUserAddress;
use App\Models\ClientUserCoupon;
use App\Models\ClientUserPet;
use App\Models\ProductSku;
use App\Models\ProductSpu;
use Carbon\CarbonImmutable;
use Hongyi\Designer\Contracts\PluginInterface;
use Hongyi\Designer\Patchwerk;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ValidateCollectsReasonablyPlugin implements PluginInterface
{
    public function handle(Patchwerk $patchwerk, \Closure $next): Patchwerk
    {
        $parameters = $patchwerk->getParameters();

        $collects = $parameters['collects'];

        try {
            $spu = ProductSpu::findOrFail($collects['spu_id']);
            $sku = ProductSku::where('id', $collects['sku_id'])->where('spu_id', $collects['spu_id'])->firstOrFail();
            $address = ClientUserAddress::where('id', $collects['address_id'])->where('user_id', $parameters['user_id'])->firstOrFail();
            $pet = ClientUserPet::where('id', $collects['pet_id'])->where('user_id', $parameters['user_id'])->firstOrFail();

            $payer_total = $collects['payer_total'] ?? $sku->price;
            $is_revise_price = !($sku->price === $payer_total);

            $coupon = null;
            if (!empty($collects['coupon_id']) || !empty($collects['coupon_code'])) {
                $coupon = ClientUserCoupon::where('user_id', $parameters['user_id'])
                    ->where('status', true)
                    ->where('min_total', '<=', $sku->price);

                if (!empty($collects['coupon_id'])) $coupon = $coupon->where('id', $collects['coupon_id']);
                if (!empty($collects['coupon_code'])) $coupon = $coupon->where('code', $collects['coupon_code']);

                $coupon = $coupon->firstOrFail();

                $payer_total = intval(bcsub($sku->price, $coupon->amount, 0));
            }
        } catch (ModelNotFoundException $exception) {
            throw new BusinessException(ResponseEnum::HTTP_ERROR, '非法创建订单');
        }

        $collects = array_merge($collects, [
            'total' => $sku->price,
            'payer_total' => max($payer_total, 0),
            'spu_json' => $spu->toArray(),
            'category_id' => $spu->category_id,
            'category_title' => $spu->category_id,
            'trademark_id' => $spu->trademark_id,
            'trademark_title' => $spu->trademark_id,
            'sku_json' => $sku->toArray(),
            'address_json' => $address->toArray(),
            'pet_json' => $pet->toArray(),
//            'coupon_json' => isset($coupon) ? $coupon->toArray() : null,
            'coupon_json' => $coupon?->toArray(),
            'coupon_total' => isset($coupon) ? $coupon->amount : 0,
            'is_revise_price' => $is_revise_price,
            'revise_by' => $is_revise_price ? $collects['revise_by'] : null,
            'pay_success_at' => max($payer_total, 0) === 0 || in_array($collects['pay_channel'], PayChannelEnum::getOffLineChannels()) ? CarbonImmutable::now(config('app.timezone')) : null,
        ]);

        $patchwerk->mergeParameters(['collects' => $collects]);

        return $next($patchwerk);
    }
}
