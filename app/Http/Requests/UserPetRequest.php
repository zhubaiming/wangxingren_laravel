<?php

namespace App\Http\Requests;

use App\Enums\GenderEnum;
use App\Enums\PetCategory;
use App\Exceptions\WechatApiException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UserPetRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
//        if (app()->isLocal()) {
//            return true;
//        }
//
//        return false;
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
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
                'variety_id' => ['required'],
                'variety_name' => ['required'],
                'name' => ['required'],
                'category' => [Rule::enum(PetCategory::class), 'required'],
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
                'variety_id' => ['required'],
                'variety_name' => ['required'],
                'name' => ['required'],
                'category' => [Rule::enum(PetCategory::class), 'required'],
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
