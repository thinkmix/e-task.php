<?php

namespace app\common;

use think\facade\Session;
use think\Db;
use app\common\enums\Variables;

class Check
{
    /**
     * 权限校验
     * @param 
     * @return 
    */
    public static function Auth($needAuth)
    {
        $uid = self::getUserId();
        if (Session::has($uid))
        {
            $sql = sprintf('SELECT user_type FROM tb_user WHERE user_id = %d ',$uid  );
            $result = Db::query($sql);
            if (empty($result))
            {
                return false;
            } else{
                return ($result[0]['user_type'] & $needAuth) > 0;
            }
        }
        return false;
    }
    /**
     * 参数设置校验
     * @param 
     * @return 
    */
    public static function ParamSet($needParam)
    {
        $arrlength=count($needParam);
        for($x=0;$x<$arrlength;$x++)
        {
            if (is_null(request()->param($needParam[$x])))
            {
                return false;
            }
        }
        return true;
    }
    /**
     * 参数为空校验
     * @param 
     * @return 
    */
    public static function ParamEmpty($needParam)
    {
        $arrlength=count($needParam);
        for($x=0;$x<$arrlength;$x++)
        {
            if (empty(request()->param($needParam[$x])))
            {
                return false;
            }
        }
        return true;
    }
    /**
     * 登录校验
     * @param 
     * @return 
    */
    public static function Login()
    {
        $uid = self::getUserId();
        if (is_null($uid))
        {
            return Variables::$LoginState_none;
        }
        $nowTime = date("U");
        if (Session::has($uid))
        {
            $sessionTime = Session::get($uid);
            if ($nowTime - $sessionTime > 60*60*24)//24小时过期
            {
                 return Variables::$LoginState_expire;
            }
            return Variables::$LoginState_ok;
        }
        return Variables::$LoginState_expire;
    }
    public static function getUserId()
     {
        if (!empty(request()->param('userId')))
         {
            $uid = request()->param('userId');
            return $uid;
         }
        return null;
    }
}