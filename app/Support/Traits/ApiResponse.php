<?php

namespace App\Support\Traits;

use App\Enums\ResponseEnum;
use App\Exceptions\BusinessException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

trait ApiResponse
{
    /**
     * 成功
     * @param $data
     * @param array $codeResponse
     * @return \Illuminate\Http\JsonResponse
     */
    public function success($data = null, array $codeResponse = ResponseEnum::HTTP_OK): \Illuminate\Http\JsonResponse
    {
        return $this->jsonResponse('success', $codeResponse, $data, null);
    }

    /**
     * 失败
     * @param array $codeResponse
     * @param $data
     * @param $error
     * @return \Illuminate\Http\JsonResponse
     */
    public function fail(array $codeResponse = ResponseEnum::HTTP_ERROR, $data = null, $error = null): \Illuminate\Http\JsonResponse
    {
        return $this->jsonResponse('fail', $codeResponse, $data, $error);
    }

    /**
     * json 响应
     * @param $status
     * @param $codeResponse
     * @param $data
     * @param $error
     * @return \Illuminate\Http\JsonResponse
     */
    private function jsonResponse($status, $codeResponse, $data, $error): \Illuminate\Http\JsonResponse
    {
        [$code, $message] = $codeResponse;

        return response()->json([
            'status' => $status,
            'code' => $code,
            'message' => $message,
            'error' => $error,
            'payload' => $data ?? null
        ]);
    }

    protected function successPaginate($page)
    {
        return $this->success($this->paginate($page));
    }

    private function paginate($page)
    {
        if ($page instanceof LengthAwarePaginator) {
            return [
                'total' => $page->total(),
                'page' => $page->currentPage(),
                'limit' => $page->perPage(),
                'pages' => $page->lastPage(),
                'list' => $page->items()
            ];
        }
        if ($page instanceof Collection) {
            $page = $page->toArray();
        }
        if (!is_array($page)) {
            return $page;
        }
        $total = count($page);
        return [
            'total' => $total, //数据总数
            'page' => 1, // 当前页码
            'limit' => $total, // 每页的数据条数
            'pages' => 1, // 最后一页的页码
            'list' => $page // 数据
        ];
    }

    /**
     * 业务异常返回
     * @param array $codeResponse
     * @param string $info
     * @return mixed
     * @throws BusinessException
     */
    public function throwBusinessException(array $codeResponse = ResponseEnum::HTTP_ERROR, string $info = '')
    {
        throw new BusinessException($codeResponse, $info);
    }
}