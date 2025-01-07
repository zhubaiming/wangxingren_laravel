<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\ProductSku;
use App\Models\SysPetBreed;
use Illuminate\Http\Request;

class ProductSkuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $validated = arrHumpToLine($request->input());

        $payload = SysPetBreed::whereHas('spu', function ($spu) use ($validated) {
            $spu->where('id', $validated['spu_id']);
        })->with('sku', function ($sku) use ($validated) {
            $sku->where('spu_id', $validated['spu_id']);
        })->get();

//        dd($payload->toArray());

        return $this->returnIndex($payload, 'PetBreedResource', 'sku_index', false);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = arrHumpToLine($request->input());

        $insert = [];
        foreach ($validated['sku_list'] as $sku) {
            $insert[] = [
                'spu_id' => $validated['spu_id'],
                'breed_id' => $sku['breed_id'],
                'weight_min' => applyFloatToIntegerModifier($sku['weight_min']),
                'weight_max' => applyFloatToIntegerModifier($sku['weight_max']),
                'duration' => $sku['duration'],
                'stock' => $sku['stock'],
                'price' => applyFloatToIntegerModifier($sku['price'])
            ];
        }

        ProductSku::where('spu_id', $validated['spu_id'])->delete();
        ProductSku::insert($insert);

        return $this->success('success');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
