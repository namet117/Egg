<?php

declare(strict_types=1);

namespace App\Traits;

trait Response
{
    protected function success(array $data, string $msg = '', int $code = 0): array
    {
        return $this->response($code, $data, $msg);
    }

    protected function failed(string $msg = '', int $code = 1, array $data = []): array
    {
        return $this->response($code, $data, $msg);
    }

    /**
     * 格式化返回的数据.
     *
     * @param int    $code 状态码
     * @param array  $data 内容
     * @param string $msg  操作状态描述
     */
    protected function response(int $code = 0, array $data = [], string $msg = ''): array
    {
        return compact('code', 'data', 'msg');
    }
}
