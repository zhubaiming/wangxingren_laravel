<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class SysPetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('sys_pets')->truncate();

        $list = $this->getList();

        array_multisort(array_column($list, 'id'), SORT_ASC, $list);

        foreach ($list as $item) {
            DB::table('sys_pets')->insert([
                'version' => $item['version'],
                'type' => $item['type'],
                'sizeType' => $item['dogSizeType'],
                'name' => $item['name'],
                'code' => $item['code'],
                'reference' => $item['reference'],
                'createdBy' => 'sys',
                'picture' => $item['picture'],
                'remark' => $item['remark']
            ]);
        }
    }

    private function getList()
    {
        list($s1, $s2) = explode(' ', microtime());
        $microtime = sprintf('%.0f', (floatval($s1) + floatval($s2)) * 1000);

        $response = Http::withHeaders([
            'tenantid' => '00ae459e842642f78b9ab0d8e7c027b4',
            'appid' => '6259662812989361028'
        ])->replaceHeaders([
            'user-agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0.0.0 Safari/537.36'
        ])->withQueryParameters([
            '_' => $microtime,
            'page' => 1,
            'size' => 999
        ])->get('https://cdp.myfoodiepet.com/deepexi-dm-admin/api/v1/petBreed/page');

        if ($response->ok()) {
            return $response->json()['payload']['content'];
        } else {
            DB::table('sys_pets')->truncate();
            exit();
        }
    }
}
