<?php

namespace App\Http\Controllers\Api\Wechat\Product;

use App\Http\Controllers\Controller;
use App\Http\Resources\Wechat\ProductSkuResource;
use App\Models\ProductSku;
use Illuminate\Http\Request;

class SkuController extends Controller
{

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        $validate = arrHumpToLine($request->input());

        $payload = ProductSku::where('trademark_id', $validate['trademark_id'])
            ->where('category_id', $validate['category_id'])
            ->where('spu_id', $validate['spu_id'])
            ->where('breed_id', $validate['breed_id'])
            ->where('weight_min', '<=', applyFloatToIntegerModifier($validate['weight']))
            ->where('weight_max', '>=', applyFloatToIntegerModifier($validate['weight']))
            ->firstOrFail();

        return $this->success((new ProductSkuResource($payload))->additional(['format' => __FUNCTION__]));
    }
}
