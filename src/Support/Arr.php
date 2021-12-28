<?php


namespace BugSheng\Normal\Support;


use Illuminate\Support\Arr as SupportArr;

class Arr extends SupportArr
{

    /**
     * 将二维数组根据某一个key进行分组重组
     *
     * @param array  $array
     * @param string $group_key
     *
     * @return array
     */
    public static function arrayGroup(array $array, $group_key): array
    {

        $isStdClass = false;
        if (!is_array($array[0])) {
            $isStdClass = true;
        }

        $cur_arr = [];   //current row
        $result = [];
        foreach ($array as $item) {
            if ($isStdClass) {
                $cur_arr = (array)$item;
            } else {
                $cur_arr = $item;
            }

            if (!array_key_exists($group_key, $cur_arr)) {
                return [];
            }

            $result[$cur_arr[$group_key]][] = $cur_arr;

        }
        unset($cur_arr);

        return $result;
    }

    /**
     * 判断一个多维数组中是否存在某一个值
     *
     * @param string $value
     * @param array $array
     *
     * @return bool
     */
    public static function deepInArray($value, $array)
    {
        foreach ($array as $item) {
            if (!is_array($item)) {
                if ($item == $value) {
                    return true;
                } else {
                    continue;
                }
            }

            if (in_array($value, $item)) {
                return true;
            } else {
                if (self::deepInArray($value, $item)) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * 判断数组的键是否存在，并且佱不为空
     *
     * @param $arr
     * @param $column
     *
     * @return null
     */
    public static function issetAndNotEmpty($arr, $column)
    {
        return (isset($arr[$column]) && $arr[$column]) ? $arr[$column] : '';
    }

    /**
     * 作用：根据二维数组中的部分键值判断二维数组中是否有重复值
     *
     * @param array $arr  目标数组
     * @param array $keys 要进行判断的键值组合的数组
     *
     * @return bool 是有重复 true有重复 false无重复
     */
    public static function hasArrayRepeat($arr = [], $keys = []): bool
    {
        $unique_arr = [];
        foreach ($arr as $k => $v) {
            $str = "";
            foreach ($keys as $a => $b) {
                $str .= "{$v[$b]},";
            }
            if (!in_array($str, $unique_arr)) {
                $unique_arr[] = $str;
            } else {
                return true;
            }
        }
        return false;
    }

    /**
     * 作用：根据二维数组中的部分键值判断二维数组中是否有重复值,返回重复值
     *
     * @param array $arr  目标数组
     * @param array $keys 要进行判断的键值组合的数组
     *
     * @return array 重复的值
     */
    public static function getArrayRepeat($arr = [], $keys = []): array
    {
        $unique_arr = [];
        $repeat_arr = [];
        foreach ($arr as $k => $v) {
            $str = "";
            foreach ($keys as $a => $b) {
                $str .= "{$v[$b]},";
            }
            if (!in_array($str, $unique_arr)) {
                $unique_arr[] = $str;
            } else {
                $repeat_arr[] = $v;
            }
        }
        return $repeat_arr;
    }

    /**
     * 提取或删除包含因子的成员,得到一个新二维数组
     *
     * @param array $data     原始数组
     * @param array $del_data mixd 传入的改变因子
     * @param bool  $flag     为false就是原始数组删除包含因子的成员，true就是提取包含因子的成员
     *
     * @return array
     */
    public static function delMemberGetNewArray(array $data, array $del_data, $flag = false): array
    {
        if (!$data || !count($data)) {
            return [];
        }
        if (!$del_data || !count($del_data)) {
            return [];
        }
        $flag_array = [false, true];
        if (!in_array($flag, $flag_array)) {
            return [];
        }
        $new_data  = [];
        $count     = sizeof($del_data);
        $org_count = sizeof($data[0]);
        if ($count > $org_count) {
            return [];
        }//如果del_data的个数大于数组，返回false
        foreach ($data as $key => $value) {
            //提取制定成员操作
            if ($flag) {
                //提取单个成员操作
                if (count($del_data) == 1) {
                    if (array_key_exists($del_data[0], $value)) {
                        $new_data[$key][$del_data[0]] = $value[$del_data[0]];
                        if ($count == count($data) - 1) {
                            return $new_data;
                        }
                    } else {
                        return [];
                    }
                } else {
                    //提取多个成员
                    $keys      = array_keys($value);
                    $new_array = array_intersect($keys, $del_data);
                    if (count($new_array)) {
                        foreach ($new_array as $temp) {
                            $new_data[$key][$temp] = $value[$temp];
                        }
                    }
                }
            } else {
                //传入数组删除操作
                foreach ($del_data as $del_value) {
                    unset($value[$del_value]);
                }
                //传入单个变量删除操作
                $new_data[$key] = $value;
            }
        }
        return $new_data;
    }


    /**
     * php7.2废弃each方法，该方法为each的替代方法
     *
     * @param $array
     *
     * @return array|bool
     */
    public static function admEach(&$array)
    {
        $res = [];
        $key = key($array);
        if ($key !== null) {
            next($array);
            $res[1] = $res['value'] = $array[$key];
            $res[0] = $res['key'] = $key;
        } else {
            $res = false;
        }
        return $res;
    }

}
