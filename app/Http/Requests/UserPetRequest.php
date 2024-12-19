<?php

namespace App\Http\Requests;

use App\Enums\GenderEnum;
use App\Enums\PetCategoryEnum;
use Illuminate\Validation\Rule;

class UserPetRequest extends CommentsRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
//        switch ($this->method()) {
//            case 'GET':
//            case 'POST':
//            case 'PUT':
//            case 'PATCH':
//            case 'DELETE':
//            default:
//                return [];
//        }


//        return [
//            'name' => ['bail', 'required'],
//            'type' => ['bail', Rule::enum(PetTypeEnum::class), 'required'],
//            'is_default' => ['bail', 'boolean', 'required'],
//            'gender' => ['bail', Rule::enum(GenderEnum::class), 'required'],
//            'age' => ['bail', 'filled', 'integer', 'numeric'],
//            'breed' => ['bail', 'filled', 'string'],
//            'color' => ['bail', 'filled', 'string'],
//            'weight' => ['bail', 'filled', 'numeric'],
//            'avatar' => ['bail', 'filled', 'url'],
//            'remark' => ['bail', 'filled', 'string']
//        ];
//        dd($this->route()->action['as'],
//            $this->route()->getActionName(),
//            $this->route()->action,
//            $this->route()->getAction(),
//            $this->route()->getAction('uses'),
//            preg_replace('/(.*)@/i', '', $this->route()->getActionName())
//        );
        $action = trim(preg_replace('/(.*)@/i', '', $this->route()->getActionName()));

//        dump($action);

//        $a = match ($action) {
//            'index' => [],
//            'store' => [
//                'name' => ['bail', 'required'],
//                'type' => ['bail', Rule::enum(PetTypeEnum::class), 'required'],
//                'is_default' => ['bail', 'boolean', 'required'],
//                'gender' => ['bail', Rule::enum(GenderEnum::class), 'required'],
//                'age' => ['bail', 'filled', 'integer', 'numeric'],
//                'breed' => ['bail', 'filled', 'string'],
//                'color' => ['bail', 'filled', 'string'],
//                'weight' => ['bail', 'filled', 'numeric'],
//                'avatar' => ['bail', 'filled', 'url'],
//                'remark' => ['bail', 'filled', 'string']
//            ],
//            'show' => [],
//            'update' => [],
//            'destroy' => [],
//        };
//
//        dd($a);

        return match ($action) {
            'index' => [],
            'store' => [
                'breed_id' => ['required'],
                'breed_title' => ['required'],
                'name' => ['required'],
                'breed_type' => [Rule::enum(PetCategoryEnum::class), 'required'],
                'gender' => [Rule::enum(GenderEnum::class), 'required'],
                'weight' => ['filled', 'numeric'],
                'birth' => ['required', 'string'],
                'color' => ['filled', 'string'],
                'avatar' => ['filled', 'url'],
                'remark' => ['filled', 'string'],
                'is_sterilization' => ['boolean', 'filled'],
                'is_default' => ['boolean', 'required']
//                'name' => ['required'],
//                'name' => ['required'],
//                'name' => ['required'],
//                'type' => [Rule::enum(PetTypeEnum::class), 'required'],
//                'age' => ['filled', 'integer', 'numeric'],
//                'breed' => ['filled', 'string'],
            ],
            'show' => [],
            'update' => [
                'breed_id' => ['required'],
                'breed_title' => ['required'],
                'name' => ['required'],
                'breed_type' => [Rule::enum(PetCategoryEnum::class), 'required'],
                'gender' => [Rule::enum(GenderEnum::class), 'required'],
                'weight' => ['filled', 'numeric'],
                'birth' => ['required', 'string'],
                'color' => ['filled', 'string'],
                'avatar' => ['filled', 'url'],
                'remark' => ['filled', 'string'],
                'is_sterilization' => ['boolean', 'filled'],
                'is_default' => ['boolean', 'required']
            ],
            'destroy' => [],
            'upload' => [
                'avatar' => ['required', 'file']
            ]
        };
    }

//    public function after(Validator $validator)
//    {
////        throw new WechatApiException('0100001', $validator->errors()->first());
//    }
}
