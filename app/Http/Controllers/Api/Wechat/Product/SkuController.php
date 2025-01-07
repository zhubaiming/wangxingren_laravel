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
        $validated = arrHumpToLine($request->input());

        $payload = ProductSku::where('trademark_id', $validated['trademark_id'])
            ->where('category_id', $validated['category_id'])
            ->where('spu_id', $validated['spu_id'])
            ->where('breed_id', $validated['breed_id'])
            ->where('weight_min', '<=', applyFloatToIntegerModifier($validated['weight']))
            ->where('weight_max', '>=', applyFloatToIntegerModifier($validated['weight']))
            ->firstOrFail();

        return $this->success((new ProductSkuResource($payload))->additional(['format' => __FUNCTION__]));
    }
}
