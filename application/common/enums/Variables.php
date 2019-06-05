<?php

namespace app\common\enums;

class Variables{
    public static $LoginState_ok = 1;//登录校验成功
    public static $LoginState_none = 2;//无该用户
    public static $LoginState_expire = 3;//登录过期

    public static $UserType_normal = 1;//普通用户
    public static $UserType_admin = 2;//管理员
}