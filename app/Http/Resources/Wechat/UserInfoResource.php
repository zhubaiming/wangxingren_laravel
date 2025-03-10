<?php

namespace App\Http\Resources\Wechat;

use App\Http\Resources\CommentsResource;

class UserInfoResource extends CommentsResource
{

    protected function resourceData(): array
    {
        $paginate = $this->additional['paginate'] ?? true;
        $format = $this->additional['format'] ?? 'default';

        return match ($paginate) {
            true => match ($format) {
                'index' => [
                    'id' => $this->id,
                    'province' => $this->province,
                    'city' => $this->city,
                    'district' => $this->district,
                    'street' => $this->street,
                    'address' => $this->address,
                    'full_address' => $this->full_address,
                    'person_name' => $this->person_name,
                    'person_phone_prefix' => $this->person_phone_prefix,
                    'person_phone_number' => $this->person_phone_number,
                    'is_default' => $this->is_default
                ],
                'show' => [
                    'id' => $this->id,
                    'title' => $this->title,
                    'permissions' => $this->permissions->pluck('id'),
                    'menus' => $this->menus->pluck('id')
                ],
                'default' => []
            },
            false => match ($format) {
                /*
                 *  "id" => 1
  "uid" => "01JKFK3JG97TK2P5E04ZET2ENC"
  "phone_number" => "15940551528"
  "phone_prefix" => "86"
  "nick_name" => null
  "avatar" => null
  "name" => null
  "card_no" => null
  "gender" => 0
  "birthday" => null
  "birth_month" => null
  "birth_day" => null
  "remark" => null
  "last_login_ip" => null
  "is_login" => 1
  "is_freeze" => 0
  "level" => 0
  "integral" => 0
  "created_at" => "2025-02-07T06:56:09.000000Z"
  "updated_at" => "2025-02-07T06:56:09.000000Z"
  "deleted_at" => null
  "pets" => array:1 [
    0 => array:18 [
      "id" => 1
      "user_id" => 1
      "breed_id" => 732
      "breed_title" => "巴吉度"
      "name" => "测试巴吉度"
      "breed_type" => 2
      "gender" => 1
      "weight" => "26.00"
      "birth" => "2025-01"
      "color" => null
      "avatar" => array:1 [
        0 => array:1 [
          "url" => "https://www.wangxingren.fun/storage/ClientUser/Pet/Avatar/982bbc078142a2e0b4915a407ae7db10.png"
        ]
      ]
      "remark" => null
      "is_sterilization" => false
      "is_default" => true
      "created_at" => "2025-01-10T08:09:03.000000Z"
      "updated_at" => "2025-01-10T08:09:03.000000Z"
      "deleted_at" => null
      "user" => array:21 [
        "id" => 1
        "uid" => "01JKFK3JG97TK2P5E04ZET2ENC"
        "phone_number" => "15940551528"
        "phone_prefix" => "86"
        "nick_name" => null
        "avatar" => null
        "name" => null
        "card_no" => null
        "gender" => 0
        "birthday" => null
        "birth_month" => null
        "birth_day" => null
        "remark" => null
        "last_login_ip" => null
        "is_login" => 1
        "is_freeze" => 0
        "level" => 0
        "integral" => 0
        "created_at" => "2025-02-07T06:56:09.000000Z"
        "updated_at" => "2025-02-07T06:56:09.000000Z"
        "deleted_at" => null
      ]
    ]
  ]
  "addresses" => array:1 [
    0 => array:17 [
      "id" => 1
      "user_id" => 1
      "country" => "中国"
      "province" => "辽宁省"
      "city" => "沈阳市"
      "district" => "浑南区"
      "street" => null
      "address" => "沈中大街206号沈阳市人民政府"
      "full_address" => "中国辽宁省沈阳市浑南区沈中大街206号沈阳市人民政府"
      "person_name" => "aaa"
      "person_phone_prefix" => "86"
      "person_phone_number" => "13111111111"
      "is_default" => true
      "created_at" => "2025-01-12T08:06:34.000000Z"
      "updated_at" => "2025-01-12T08:06:34.000000Z"
      "deleted_at" => null
      "user" => array:21 [
        "id" => 1
        "uid" => "01JKFK3JG97TK2P5E04ZET2ENC"
        "phone_number" => "15940551528"
        "phone_prefix" => "86"
        "nick_name" => null
        "avatar" => null
        "name" => null
        "card_no" => null
        "gender" => 0
        "birthday" => null
        "birth_month" => null
        "birth_day" => null
        "remark" => null
        "last_login_ip" => null
        "is_login" => 1
        "is_freeze" => 0
        "level" => 0
        "integral" => 0
        "created_at" => "2025-02-07T06:56:09.000000Z"
        "updated_at" => "2025-02-07T06:56:09.000000Z"
        "deleted_at" => null
      ]
    ]
  ]
                 */
                'info' => [
                    'nick_name' => $this->nick_name,
                    'avatar' => $this->avatar,
                    'level' => $this->level,
                    'integral' => $this->integral,
                ]
            },
        };
    }
}
