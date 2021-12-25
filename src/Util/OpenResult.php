<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * 输出结果的封装
 * 只要get不要set,进行更好的封装
 * @param <T>
 */

namespace Captainbi\Hyperf\Util;

use Hyperf\Contract\ConfigInterface;
use Hyperf\Utils\ApplicationContext;

class OpenResult
{
    /**
     * @param $code
     * @param $msg
     * @param array $data
     * @param $next_token
     * @param $max_result
     * @return string
     */
    private static function data($code, $msg, $data = [], $next_token = '', $max_result = 0)
    {
        $is_open_next_token = ApplicationContext::getContainer()->get(ConfigInterface::class)->get('common.is_open_next_token');
        if ($is_open_next_token) {
            return json_encode(array(
                'code' => $code ?: 0,
                'msg' => $msg ?: 'success',
                'next_token' => $next_token ?: '',
                'max_result' => $max_result ?: 0,
                'data' => (object)$data,
            ), JSON_UNESCAPED_UNICODE);
        } else {
            return json_encode(array(
                'code' => $code ?: 0,
                'msg' => $msg ?: 'success',
                'max_result' => $max_result ?: 0,
                'data' => (object)$data,
            ), JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * 成功
     * @param array $data
     * @param string $msg
     * @param string $next_token
     * @param int $max_result
     * @return mixed
     */
    public static function success($data = [], $next_token = '', $max_result = 0, $msg = 'success')
    {
        return self::data(200, $msg, $data, $next_token, $max_result);
    }

    /**
     * 失败
     * @param array $data
     * @param string $msg
     * @param int $code
     * @return mixed
     */
    public static function fail($data = [], $msg = 'error', $code = -1)
    {
        return self::data($code, $msg, $data);
    }

    /**
     * fail 的别名，一般错误都不需要 data 参数
     *
     * @param string $msg
     * @param int $code
     * @param array $data
     * @return string
     */
    public static function error(string $msg = 'error', int $code = -1, array $data = []): string
    {
        return self::data($code, $msg, $data);
    }
}
