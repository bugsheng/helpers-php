<?php
/**
 * Created by PhpStorm.
 * User: s6177
 * Date: 2018/9/29
 * Time: 14:15
 */

use Illuminate\Support\Collection;

if (!function_exists('loadImg')) {
    /**
     * 保存网络图片到服务器
     * 小程序传的头像是网络地址需要周转一下
     *
     * @param $image_url
     * @param $local_url
     *
     * @return false|int
     */
    function loadImg($image_url, $local_url)
    {
        $img_file    = file_get_contents($image_url);
        $img_content = base64_encode($img_file);
        return file_put_contents($local_url, base64_decode($img_content));
    }
}

if (!function_exists('getClientIp')) {
    /**
     * 获取客户端 ip
     *
     * @return array|false|null|string
     */
    function getClientIp()
    {
        static $realip = null;
        if ($realip !== null) {
            return $realip;
        }
        //判断服务器是否允许$_SERVER
        if (isset($_SERVER)) {
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $realip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
                $realip = $_SERVER['HTTP_CLIENT_IP'];
            } else {
                $realip = $_SERVER['REMOTE_ADDR'];
            }
        } else {
            //不允许就使用getenv获取
            if (getenv("HTTP_X_FORWARDED_FOR")) {
                $realip = getenv("HTTP_X_FORWARDED_FOR");
            } elseif (getenv("HTTP_CLIENT_IP")) {
                $realip = getenv("HTTP_CLIENT_IP");
            } else {
                $realip = getenv("REMOTE_ADDR");
            }
        }

        return $realip;
    }
}

if (!function_exists('trimAllBlankSpace')) {
    /**
     * 过滤用户输入数据中的空格 全角空格 tab
     *
     * @param $str
     *
     * @return mixed
     *
     */
    function trimAllBlankSpace($str)
    {
        $search  = [" ", "　", "\t"];
        $replace = ["", "", ""];
        return str_replace($search, $replace, $str);
    }
}

if (!function_exists('getHourAndMin')) {
    /**
     * 将时间戳转换成 xx 时\xx 分
     *
     * @param $time
     *
     * @return array
     */
    function getHourAndMin($time)
    {
        $sec = round($time / 60);
        if ($sec >= 60) {
            $hour = floor($sec / 60);
            $min  = $sec % 60;

        } else {
            $hour = 0;
            $min  = $sec;
        }
        return ['hour' => $hour, 'min' => $min];
    }
}

if (!function_exists('addslashesDeep')) {
    /**
     * 递归方式的对变量中的特殊字符进行转义
     *
     * @param $value
     *
     * @return array|string
     */
    function addslashesDeep($value)
    {
        if (empty($value)) {
            return $value;
        } else {
            return is_array($value) ? array_map('addslashesDeep', $value) : addslashes($value);
        }
    }
}

if (!function_exists('list_to_tree')) {
    /**
     * 把返回的数据集转换成Tree
     *
     * @param array  $list 要转换的数据集
     * @param string $pid  parent标记字段
     *
     * @return array
     */
    function list_to_tree($list, $pid = 'parent_id')
    {
        // 创建Tree
        $tree = [];
        if (is_array($list)) {
            // 创建基于主键的数组引用
            $refer = [];
            foreach ($list as $key => $data) {
                $refer[$data['id']] =& $list[$key];
            }
            foreach ($list as $key => $data) {
                // 判断是否存在parent
                $parentId = $data[$pid];
                if ($parentId == 0) {
                    $tree[] =& $list[$key];
                } else {
                    if (isset($refer[$parentId])) {
                        $parent               =& $refer[$parentId];
                        $parent['children'][] =& $list[$key];
                    }
                }
            }
        }

        return $tree;
    }
}

if (!function_exists('formatNumberWithWan')) {
    /**
     * 格式化数字 过万显示单位w 保留两位小数
     *
     * @param $v
     * @param $wan_decimals
     * @param $decimals
     * @param $unit
     *
     * @return string
     */
    function formatNumberWithWan($v, $wan_decimals = 2, $decimals = 2, $unit = 'w')
    {
        return $v > 10000 || $v < -10000 ? (sprintf('%.' . $wan_decimals . 'f',
                $v * 1 / 10000) . $unit) : sprintf('%.' . $decimals . 'f', $v * 1);
    }
}

if (!function_exists('getDivideInteger')) {
    /**
     * 均分正整数为多份
     *
     * @param int $number 要均分的正整数 或 0
     * @param int $total  均分的份数
     *
     * @return array|false|string[]
     */
    function getDivideInteger(int $number, int $total)
    {
        if ($number < 0 || $total <= 0) {
            return false;
        }

        // 平均整数
        $per = intval($number / $total);
        // 余数
        $rest = $number % $total;

        // 余数均分

        $number_str = str_repeat(($per + 1) . ',', $rest) . str_repeat($per . ',', $total - $rest - 1) . $per;
        return explode(',', $number_str);
    }

}

