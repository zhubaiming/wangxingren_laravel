<?php

namespace App\Services;

use App\Services\CommentsService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class SystemPetCategory extends CommentsService
{
    public function fork()
    {
        $list = $this->forkList();

        array_multisort(array_column($list, 'id'), SORT_ASC, $list);

        foreach ($list as $item) {
            $item['size_type'] = $item['dogSizeType'];
            $item['created_by'] = 'sys';
        }

        DB::table('sys_pets')->insert($list);
    }

    private function forkList()
    {
        list($s1, $s2) = explode(' ', microtime());
        $microtime = sprintf('%0.f', (floatval($s1) + floatval($s2)) * 1000);

        $response = Http::withHeaders([
            'tenantid' => '00ae459e842642f78b9ab0d8e7c027b4',
            'appid' => '6259662812989361028'
        ])->replaceHeaders([
            'User-agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0.0.0 Safari/537.36'
        ])->withQueryParameters([
            '_' => $microtime,
            'page' => 1,
            'size' => 999
        ])->get('https://cdp.myfoodiepet.com/deepexi-dm-admin/api/v1/petBreed/page');

        if ($response->ok()) {
            return $response->json()['payload']['content'];
        } else {
//            DB::table('sys_pets')->truncate();
//            exit();
        }
    }
}