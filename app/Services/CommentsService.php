<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class CommentsService
{
    protected $model;

    protected $table_name;

    protected $events;

    protected function setTable($table_name)
    {
        $this->table_name = $table_name;
    }

    protected function setModel($model_name)
    {
        $class = '\\' . ltrim($model_name, '\\');

        $this->model = new $class;
    }

    protected function comboConditional($query, $conditions = [], $scopes = [], $relations = [], $aggregates = [], $fields = [])
    {
        // 添加要查询的指定字段
        $query = $query->select($fields);

        // 添加 where 条件
        foreach ($conditions as $field => $value) {
            if ($field === 'in') {
                $query->whereIn($value[0], $value[1]);
            } elseif (is_array($value)) {
                $query->where(...$value); // 支持复杂查询条件，例如 ['age', '>', 18]
            } else {
                $query->where($field, $value); // 普通的键值对条件
            }
        }

        // 动态调用作用域
        foreach ($scopes as $scope => $parameters) {
            if (method_exists($query->getModel(), 'scope' . ucfirst($scope))) {
                $query->$scope(...(array)$parameters);
            }
        }

        // 预加载加载关联关系并支持作用域和嵌套预加载
        if (!empty($relations)) {
            $query = $this->applyRelations($query, $relations);
        }

        // 处理聚合（如 withMin, withMax 等）
        foreach ($aggregates as $relation => $aggregate_functions) {
            foreach ($aggregate_functions as $function => $field) {
                $method = 'with' . ucfirst($function);  // 构造方法名，例如 'withMin'
                if (method_exists($query, $method)) {
                    $query->{$method}($relation, $field);
                }
            }
        }

        return $query;
    }

    protected function applyRelations(Builder $query, array $relations): Builder
    {
        foreach ($relations as $relation => $scope) {
            if (is_string($relation)) {
                // 带作用域的关联加载
                $query->with([$relation => $scope]);
            } elseif (is_string($scope)) {
                // 仅关联名称的加载
                $query->with($scope);
            } elseif (is_array($scope)) {
                // 嵌套预加载处理
                $query->with([$relation => function ($q) use ($scope) {
                    $this->applyRelations($q, $scope);
                }]);
            }
        }

//        foreach ($relations as $relation => $options) {
//            if (is_array($options)) {
//                $fields = $options['fields'] ?? ['*']; // 关联字段，默认为全部字段
//                $scope = $options['scope'] ?? null;
//
//                // 使用作用域加载关联关系并限定字段
//                if (is_callable($scope)) {
//                    $query->with([$relation => function ($q) use ($scope, $fields) {
//                        $q->select($fields); // 限定查询字段
//                        $scope($q); // 应用作用域
//                    }]);
//                } else {
//                    // 如果有嵌套关系，递归处理
//                    $nestedRelations = array_filter($options, fn($key) => $key !== 'fields' && $key !== 'scope', ARRAY_FILTER_USE_KEY);
//                    if (!empty($nestedRelations)) {
//                        $query->with([$relation => function ($q) use ($nestedRelations, $fields) {
//                            $q->select($fields);
//                            $this->applyRelations($q, $nestedRelations); // 递归加载嵌套关联关系
//                        }]);
//                    } else {
//                        $query->with([$relation => function ($q) use ($fields) {
//                            $q->select($fields);
//                        }]);
//                    }
//                }
//            } else {
//                // 如果没有字段限制，使用默认关联加载
//                $query->with($options);
//            }
//        }

        return $query;
    }

    public function getList1(array $fields = ['*'], array $withScopes = [], array $conditions = [], array $order_by = [], bool $paginate = true, bool $simplePaginate = false, int $perPage = 10, int $page = 1)
    {
        // 初始化查询构造器
        $query = $this->model->newQuery();

        $query->select($fields);

        if (!empty($withScopes)) {
            count($withScopes) === 1 && $withScopes[0] === 'all' ? $query->withoutGlobalScopes() : $query->withoutGlobalScopes($withScopes);
        }

        // 应用查询条件
        foreach ($conditions as $field => $condition) {
            if (is_array($condition)) {
                $operator = $condition['operator'] ?? '=';
                $value = $condition['value'] ?? null;

                switch ($operator) {
                    case 'in':
                        $query->whereIn($field, $value);
                        break;
                    case 'not_in':
                        $query->whereNotIn($field, $value);
                        break;
                    case 'or_in':
                        $query->orWhereIn($field, $value);
                        break;
                    case 'or_not_in':
                        $query->orWhereNotIn($field, $value);
                        break;
                    case 'between':
                        $query->whereBetween($field, $value);
                        break;
                    case 'not_between':
                        $query->whereNotBetween($field, $value);
                        break;
                    case 'or_between':
                        $query->orWhereBetween($field, $value);
                        break;
                    case 'or_not_between':
                        $query->orWhereNotBetween($field, $value);
                        break;
                    case 'null':
                        $query->whereNull($field);
                        break;
                    case 'not_null':
                        $query->shereNotNull($field);
                        break;
                    case 'date':
                        $query->whereDate($field, $value);
                        break;
                    case 'month':
                        $query->whereMonth($field, $value);
                        break;
                    case 'day':
                        $query->whereDay($field, $value);
                        break;
                    case 'year':
                        $query->whereYear($field, $value);
                        break;
                    case 'time':
                        $query->whereTime($field, '=', $value);
                        break;
                }
            } else {
                // 默认全等
                $query->where($field, '=', $condition);
            }
        }

        // 添加排序规则
        foreach ($order_by as $column => $direction) {
            $query->orderBy($column, $direction);
        }

        if ($paginate) {
            return $simplePaginate ? $query->simplePaginate($perPage, $fields, 'page', $page) : $query->paginate($perPage, $fields, 'page', $page);
        }

        return $query->get();
    }

    /**
     * 获取列表
     *
     * @param array $conditions 查询条件
     * @param array $scopes 需要应用的作用域
     * @param array $relations 关联加载
     * @param array $aggregates 统计操作, 如: ['relation_name' => ['max' => 'field', 'sum' => 'field']]
     * @param array $fields 要查询的字段
     * @param array $order_by 排序规则, 如: ['created_at' => 'desc']
     * @param bool $paginate 是否分页
     * @param int $page 分页页码
     * @param int $per_page 分页条数
     *
     * @return mixed
     */
    public function getList(array $conditions = [], array $scopes = [], array $relations = [], array $aggregates = [], array $fields = ['*'], bool $is_distinct = false, array $distinct_fields = ['*'], array $order_by = [], bool $is_without_global_Scopes = false, array $without_global_scopes = [], bool $paginate = false, bool $paginate_simple = false, int $page = 1, int $per_page = 15): mixed
    {
        // 初始化查询构造器
        $query = $this->model->newQuery();

        $query = $this->comboConditional($query, $conditions, $scopes, $relations, $aggregates, $fields);

        // 添加排序规则
        foreach ($order_by as $column => $direction) {
            $query->orderBy($column, $direction);
        }

        if ($is_distinct) {
            $query->distinct(...$distinct_fields);
        }

        if ($is_without_global_Scopes) {
            if (count($without_global_scopes) === 0) {
                $query->withoutGlobalScopes();
            } else {
                $query->withoutGlobalScopes($without_global_scopes);
            }
        }

        // 分页或全量返回
        return $paginate ? ($paginate_simple ? $query->simplePaginate($per_page, page: $page) : $query->paginate($per_page, page: $page)) : $query->get();
    }

    /**
     * 获取单条记录
     *
     * @param array $conditions 查询条件
     * @param array $scopes 需要应用的作用域
     * @param array $relations 关联加载
     * @param array $aggregates 统计操作, 如: ['relation_name' => ['max' => 'field', 'sum' => 'field']]
     * @param array $fields 要查询的字段
     *
     * @return Model
     */
    public function find(array $conditions = [], array $scopes = [], array $relations = [], array $aggregates = [], array $fields = ['*']): Model
    {
        // 初始化查询构造器
        $query = $this->model->newQuery();

        $query = $this->comboConditional($query, $conditions, $scopes, $relations, $aggregates, $fields);

        return $query->firstOrFail();
    }

    /**
     * 创建记录
     *
     * @param array $data
     * @return Model|Collection
     */
    public function create(array $data): Model|Collection
    {
        return $this->model->create($data);
    }

    /**
     * 更新记录
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        $model = $this->model->findOrFail($id);
        return $model->update($data);
    }

    /**
     * 删除记录
     *
     * @param int $id
     * @return bool|null
     */
    public function delete(array|int $id): ?bool
    {
//        $model = $this->model->findOrFail($id);
//        return $model->delete();
        return $this->model->destroy($id);
    }
}