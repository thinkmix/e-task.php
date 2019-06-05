<?php
namespace app\user\controller;

use think\Db;
use app\common\Response;
use app\common\enums\Variables;
use app\common\enums\ErrorCode;
use think\facade\Session;
use app\common\Check;
use app\common\utils\Utils;

class Login
{
    /**
     * 登录接口
     * @param userName|String|必填
     * @param password|String|必填
     * @return userId,userType,userName,nickname,
     */
    public function Login()
    {
        if (Check::ParamSet(array('userName', 'password')) === false)
        {
            return Response::error(ErrorCode::PARAMS_ERROR);
        }
        $userName = request()->param('userName');
        $password = request()->param('password');//传过来的是小写md5加密的密码

        $sql = sprintf("SELECT * FROM tb_user WHERE pwd = '$password' AND user_name = '$userName'");
        $dbResult = Db::query($sql);
        $loginTime = Utils::getMillisecond();
        $nowTime = date("U");

        if ($dbResult){
            $userData = $dbResult[0];

            $userId = $userData['user_id'];
            Session::set($userId,$nowTime);
            $ret = [];
            $ret['userId'] = $userId;
            $ret['userName'] = $userData['user_name'];
            $ret['nickname'] = $userData['nickname'];
            $ret['phone'] = $userData['phone'];
            $userType = $userData['user_type'];
            $ret['userType'] = $userType;
            //更新登录时间

            if($userType == Variables::$UserType_admin){//管理员需要返回用户对应社团的ID
                $sql = sprintf("SELECT mo_id FROM tb_mo_user WHERE user_id = $userId");
                $dbResult = Db::query($sql);

                if($dbResult){
                    $ret['adminMoId'] = $dbResult[0]['mo_id'];
                }
            }
            //更新登录时间
            Db::startTrans();
            try {
                $sql = sprintf("UPDATE tb_user SET login_time = $loginTime WHERE user_id = $userId");
                $dbResult = Db::execute($sql);
                if ($dbResult <= 0)
                {
                    throw new Exception("");
                }
                // 提交事务
                Db::commit();
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
                return Response::error(ErrorCode::USER_LOGIN_FAILED);
            }
            return Response::success($ret);
        }

        return Response::error(ErrorCode::USER_LOGIN_FAILED);
    }
    /**
     * 注册接口
     * @param userName|String|必填
     * @param password|String|必填
     * @return 
     */
    public function Register()
    {
        if (Check::ParamSet(array('userName', 'password')) === false)
        {
            return Response::error(ErrorCode::PARAMS_ERROR);
        }
        $userName = request()->param('userName');
        $password = request()->param('password');//传过来的是小写md5加密的密码

        $sql = sprintf("SELECT user_id FROM tb_user WHERE user_name = '$userName'");
        $dbResult = Db::query($sql);
        //判断是否已被注册
        if ($dbResult){
            return Response::error(ErrorCode::USER_REGISTER_EXIST);
        }

        $registerTime = Utils::getMillisecond();

        Db::startTrans();
        try {
            $sql = sprintf("INSERT INTO tb_user (user_name, pwd, register_time)
            VALUES('$userName', '$password', $registerTime)");
            $dbResult = Db::execute($sql);
            if ($dbResult <= 0)
            {
                throw new Exception("");
            }
            // 提交事务
            Db::commit();
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            return Response::error(ErrorCode::USER_REGISTER_FAILED);
        }
        return Response::success();
    }
}
