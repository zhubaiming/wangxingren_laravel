<?php

namespace App\Services;

use App\Events\CreateUserPet;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;

class UserPetService extends CommentsService
{
    public function __construct()
    {
        $this->setModel('App\Models\Pet');

        $this->events = app('events');
    }

    public function getCategoryList(string $id)
    {
        $this->setModel('App\Models\SysPetBreed');

        $conditions = ['type' => $id];

        return $this->getList($conditions);
    }

//    public function pageList()
//    {
////        dd($this->model->owner()->get());
////        return $this->model->owner()->get();
//        return $this->model->owner()->get()->toArray();
//    }

//    public function create(array $data): Model
//    {
//        if ($data['is_default']) {
//            $this->updateDefault();
//        }
//
//        $pets = Auth::guard('wechat')->user()->pets()->createMany([$data]);
//    }

    public function create(array $data): Collection
    {
        if ($data['is_default']) {
            $this->updateDefault();
        }

        return Auth::guard('wechat')->user()->pets()->createMany([$data]);
    }

//    public function info(string|int $id)
//    {
//        return $this->model->owner()->find($id);
//    }
//
//    public function update(array $data, string|int $id)
//    {
//        try {
//            $model = $this->model->owner()->findOrFail($id);
//
//            foreach ($data as $key => $value) {
//                $model->{$key} = $value;
//            }
//
//            if ($model->isDirty('is_default')) {
//                $this->updateDefault();
//            }
//
//            $model->save();
//        } catch (ModelNotFoundException $foundException) {
//            dd('要更新的模型不存在');
//        }
//    }

    private function updateDefault()
    {
        try {
            $model = $this->model->owner()->isDefault()->firstOrFail();

            $model->is_default = false;
            $model->save();
        } catch (ModelNotFoundException $foundException) {

        }
    }

//    public function delete(string|int $id)
//    {
//        $this->model->destroy($id);
//    }
}