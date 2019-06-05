<?php

namespace app\common\utils;

class Utils
{
    /**
     * 获取13位时间戳
     * @param 
     * @return 时间戳
     */
    public static function getMillisecond() {
        list($t1, $t2) = explode(' ', microtime());
        return (float)sprintf('%.0f',(floatval($t1)+floatval($t2))*1000);
    }

}