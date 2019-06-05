<?php

namespace app\common;

/**
 * http响应处理
 * Class Response
 * @package app\common\model
 */
class Response
{

    /**
     * 错误码
     * @var
     */
    public $code;

    /**
     * 错误信息
     * @var
     */
    public $msg;

    /**
     * data
     * @var
     */
    public $data;

    private function __construct($code, $msg, $data)
    {
        $this->code = $code;
        $this->msg = $msg;
        $this->data = $data;
    }

    /**
     * 请求成功的方法
     * @param $data
     * @return \think\response\Json
     */
    public static function success($data = null)
    {
        if (empty($data)) {
            $data = new \stdClass();
        }
        $instance = new self(200, "success", $data);
        return json($instance);
    }

    /**
     * 请求错误
     * @param $code
     * @param null $msg
     * @return \think\response\Json
     */
    public static function error($code, $msg = null)
    {
        if (is_array($code)) {
            $msg = isset($code['msg']) && $msg == null ? $code['msg'] : $msg;
            $code = isset($code['code']) ? $code['code'] : null;
        }
        $instance = new self($code, $msg, new \stdClass());
        return json($instance);
    }

}