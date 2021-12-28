<?php


namespace BugSheng\Normal\Support;


use Illuminate\Support\Str as SupportStr;

class Str extends SupportStr
{

    /**
     * 判断一个字符串是否是有效的json字符串
     *
     * @param $string
     *
     * @return bool
     */
    public static function isJson(string $string): bool
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    /**
     * 验证手机号码是否正确
     *
     * @param String $mobile 手机号码
     * @param bool|int $is_strict 是否严格模式
     *
     * @return boolean
     */
    public static function isMobile($mobile, $is_strict = false): bool
    {
        //手机号码验证规则
        if ($is_strict) {
            $regx = "/^((1[3,4,5,7,8][0-9])|(14[5,6,7,8,9])|(16[6])|(19[1,3,5,8,9]))\d{8}$/";
        } else {
            $regx = "/^\d{11}$/";
        }

        if (preg_match($regx, $mobile)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 判断姓名是否全是中文
     *
     * @param string $str
     *
     * @return bool
     */
    public static function isAllChinese(string $str): bool
    {
        //新疆等少数民族可能有·
        if (strpos($str, '·')) {
            //将·去掉，看看剩下的是不是都是中文
            $str = str_replace("·", '', $str);
            if (preg_match('/^[\x7f-\xff]+$/', $str)) {
                return true;//全是中文
            } else {
                return false;//不全是中文
            }
        } else {
            if (preg_match('/^[\x7f-\xff]+$/', $str)) {
                return true;//全是中文
            } else {
                return false;//不全是中文
            }
        }
    }

    /**
     * 验证身份证号码是否正确
     *
     * @param String   $id        身份证号码
     * @param bool|int $is_strict 是否严格模式
     *
     * @return boolean
     */
    public static function isIDCard($id = '', $is_strict = false): bool
    {
        $id        = strtoupper($id);
        $regx      = "/(^\d{15}$)|(^\d{17}([0-9]|X)$)/";
        $arr_split = [];
        if (!preg_match($regx, $id)) {
            return false;
        }

        if (!$is_strict) {
            return true;
        }

        if (15 == strlen($id)) //检查15位
        {
            $regx = "/^(\d{6})+(\d{2})+(\d{2})+(\d{2})+(\d{3})$/";

            @preg_match($regx, $id, $arr_split);
            //检查生日日期是否正确
            $dtm_birth = "19" . $arr_split[2] . '/' . $arr_split[3] . '/' . $arr_split[4];
            if (!strtotime($dtm_birth)) {
                return false;
            } else {
                return true;
            }
        } else      //检查18位
        {
            $regx = "/^(\d{6})+(\d{4})+(\d{2})+(\d{2})+(\d{3})([0-9]|X)$/";
            @preg_match($regx, $id, $arr_split);
            $dtm_birth = $arr_split[2] . '/' . $arr_split[3] . '/' . $arr_split[4];
            if (!strtotime($dtm_birth)) //检查生日日期是否正确
            {
                return false;
            } else {
                //检验18位身份证的校验码是否正确。
                //校验位按照ISO 7064:1983.MOD 11-2的规定生成，X可以认为是数字10。
                $arr_int = [7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2];
                $arr_ch  = ['1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2'];
                $sign    = 0;
                for ($i = 0; $i < 17; $i++) {
                    $b    = (int)$id[$i];
                    $w    = $arr_int[$i];
                    $sign += $b * $w;
                }
                $n       = $sign % 11;
                $val_num = $arr_ch[$n];
                if ($val_num != substr($id, 17, 1)) {
                    return false;
                }
                else {
                    return true;
                }
            }
        }

    }

    /**
     * 生成指定长度的数字验证码
     *
     * @param int $len
     *
     * @return string
     */
    public static function generateNumericCode(int $len): string
    {

        if ($len <= 0) {
            return '';
        }

        $code = '';
        for ($i = 0; $i < $len; $i++) {
            $code = $code . rand(0, 9);
        }

        return $code;
    }

    /**
     *　数字转换成中文的函数
     *　@param int $num 要转换的小写数字
     *　@return string 文字
     **/
    public static function numberToChinese(int $num): string
    {
        $c1 = "零一二三四五六七八九";
        $c2 = "零十百千万十百千亿";
        //精确到分后面就不要了，所以只留两个小数位
        if (strlen($num) > 10) {
            return "数字太大，请检查";
        }
        $i = 0;
        $c = "";
        while (1) {
            if ($i == 0) {
                //获取最后一位数字
                $n = substr($num, strlen($num) - 1, 1);
            } else {
                $n = $num % 10;
            }

            //每次将最后一位数字转化为中文
            $p1 = mb_substr($c1, $n, 1);

            $p2 = mb_substr($c2, $i, 1);

            if ($n != '0' || ($n == '0' && ($p2 == '亿' || $p2 == '万' || $p2 == ''))) {
                $c = $p1 . $p2 . $c;
            } else {
                $c = $p1 . $c;
            }
            $i = $i + 1;
            //去掉数字最后一位了
            $num = $num / 10;
            $num = (int)$num;
            //结束循环
            if ($num == 0) {
                break;
            }
        }
        $j = 0;

        if (mb_substr($c, 0, 2) == '一十') {
            $c = mb_substr($c, 1);
        }

        $slen = mb_strlen($c);
        while ($j < $slen) {
            $m = mb_substr($c, $j, 2);
            //处理数字中很多0的情况,每次循环去掉一个汉字“零”
            if ($m == '零十' || $m == '零万' || $m == '零亿' || $m == '零零') {
                $left  = mb_substr($c, 0, $j);
                $right = mb_substr($c, $j + 1);
                $c     = $left . $right;
                $j     = $j - 1;
                $slen  = $slen - 1;
            }
            $j = $j + 1;
        }

        return mb_substr($c, 0, -1);
    }

    /**
     * 中文转换成阿拉伯数字
     *
     * @param $string
     *
     * @return float|int|mixed
     */
    public static function chineseToNumber($string)
    {
        if (is_numeric($string)) {
            return $string;
        }
        // '仟' => '千','佰' => '百','拾' => '十',
        $string = str_replace('仟', '千', $string);
        $string = str_replace('佰', '百', $string);
        $string = str_replace('拾', '十', $string);
        $num    = 0;
        $wan    = explode('万', $string);
        if (count($wan) > 1) {
            $num    += self::chineseToNumber($wan[0]) * 10000;
            $string = $wan[1];
        }
        $qian = explode('千', $string);
        if (count($qian) > 1) {
            $num    += self::chineseToNumber($qian[0]) * 1000;
            $string = $qian[1];
        }
        $bai = explode('百', $string);
        if (count($bai) > 1) {
            $num    += self::chineseToNumber($bai[0]) * 100;
            $string = $bai[1];
        }
        $shi = explode('十', $string);
        if (count($shi) > 1) {
            $num    += self::chineseToNumber($shi[0] ? $shi[0] : '一') * 10;
            $string = $shi[1] ? $shi[1] : '零';
        }
        $ling = explode('零', $string);
        if (count($ling) > 1) {
            $string = $ling[1];
        }
        $d = [
            '一' => '1',
            '二' => '2',
            '三' => '3',
            '四' => '4',
            '五' => '5',
            '六' => '6',
            '七' => '7',
            '八' => '8',
            '九' => '9',
            '壹' => '1',
            '贰' => '2',
            '叁' => '3',
            '肆' => '4',
            '伍' => '5',
            '陆' => '6',
            '柒' => '7',
            '捌' => '8',
            '玖' => '9',
            '零' => 0,
            '0' => 0,
            'O' => 0,
            'o' => 0,
            '两' => 2
        ];
        return $num + @$d[$string];
    }

    /**
     *　数字金额转换成中文大写金额的函数
     *　@param int|float $num 要转换的小写数字或小写字符串
     *　@return string 大写文字
     *　小数位为两位
     **/
    public static function moneyToChinese($num): string
    {
        $c1 = "零壹贰叁肆伍陆柒捌玖";
        $c2 = "分角圆拾佰仟万拾佰仟亿";
        //精确到分后面就不要了，所以只留两个小数位
        $num = round($num, 2);
        //将数字转化为整数
        $num = $num * 100;
        if (strlen($num) > 10) {
            return "金额太大，请检查";
        }
        $i = 0;
        $c = "";
        while (1) {
            if ($i == 0) {
                //获取最后一位数字
                $n = substr($num, strlen($num) - 1, 1);
            } else {
                $n = $num % 10;
            }
            //每次将最后一位数字转化为中文
            $p1 = substr($c1, 3 * $n, 3);
            $p2 = substr($c2, 3 * $i, 3);
            if ($n != '0' || ($n == '0' && ($p2 == '亿' || $p2 == '万' || $p2 == '圆'))) {
                $c = $p1 . $p2 . $c;
            } else {
                $c = $p1 . $c;
            }
            $i = $i + 1;
            //去掉数字最后一位了
            $num = $num / 10;
            $num = (int)$num;
            //结束循环
            if ($num == 0) {
                break;
            }
        }
        $j    = 0;
        $slen = strlen($c);
        while ($j < $slen) {
            //utf8一个汉字相当3个字符
            $m = substr($c, $j, 6);
            //处理数字中很多0的情况,每次循环去掉一个汉字“零”
            if ($m == '零圆' || $m == '零万' || $m == '零亿' || $m == '零零') {
                $left  = substr($c, 0, $j);
                $right = substr($c, $j + 3);
                $c     = $left . $right;
                $j     = $j - 3;
                $slen  = $slen - 3;
            }
            $j = $j + 3;
        }
        //这个是为了去掉类似23.0中最后一个“零”字
        if (substr($c, strlen($c) - 3, 3) == '零') {
            $c = substr($c, 0, strlen($c) - 3);
        }
        //将处理的汉字加上“整”
        if (empty($c)) {
            return "零元整";
        } else {
            return $c . "整";
        }
    }


    /**
     * 把用户输入的文本转义（主要针对特殊符号和emoji表情）
     *
     * @param $str
     *
     * @return mixed|string
     */
    public static function userTextEncode($str)
    {
        if (!is_string($str)) {
            return $str;
        }
        if (!$str || $str == 'undefined') {
            return '';
        }

        $text = json_encode($str); //暴露出unicode
        $text = preg_replace_callback("/(\\\u[ed][0-9a-f]{3})/i", function ($str) {
            return addslashes($str[0]);
        }, $text); //将emoji的unicode留下，其他不动，这里的正则比原答案增加了d，因为我发现我很多emoji实际上是\ud开头的，反而暂时没发现有\ue开头。
        return json_decode($text);
    }

    /**
     * 解码userTextEncode转义的内容 与 userTextEncode配对使用
     *
     * @param $str
     *
     * @return mixed
     */
    public static function userTextDecode($str)
    {
        $text = json_encode($str); //暴露出unicode
        $text = preg_replace_callback('/\\\\\\\\/i', function () {
            return '\\';
        }, $text); //将两条斜杠变成一条，其他不动
        return json_decode($text);
    }

    /**
     * 获取首字母
     *
     * @param string $str 汉字字符串
     *
     * @return string 首字母
     */
    public static function getInitials($str)
    {
        if (empty($str)) {
            return '#';
        }
        $fChar = ord($str[0]);
        if ($fChar >= ord('A') && $fChar <= ord('z')) {
            return strtoupper($str[0]);
        }

        $s1  = iconv('UTF-8', 'gb2312', $str);
        $s2  = iconv('gb2312', 'UTF-8', $s1);
        $s   = $s2 == $str ? $s1 : $str;
        $asc = ord($s[0]) * 256 + ord($s[1]) - 65536;
        if ($asc >= -20319 && $asc <= -20284) {
            return 'A';
        }

        if ($asc >= -20283 && $asc <= -19776) {
            return 'B';
        }

        if ($asc >= -19775 && $asc <= -19219) {
            return 'C';
        }

        if ($asc >= -19218 && $asc <= -18711) {
            return 'D';
        }

        if ($asc >= -18710 && $asc <= -18527) {
            return 'E';
        }

        if ($asc >= -18526 && $asc <= -18240) {
            return 'F';
        }

        if ($asc >= -18239 && $asc <= -17923) {
            return 'G';
        }

        if ($asc >= -17922 && $asc <= -17418) {
            return 'H';
        }

        if ($asc >= -17417 && $asc <= -16475) {
            return 'J';
        }

        if ($asc >= -16474 && $asc <= -16213) {
            return 'K';
        }

        if ($asc >= -16212 && $asc <= -15641) {
            return 'L';
        }

        if ($asc >= -15640 && $asc <= -15166) {
            return 'M';
        }

        if ($asc >= -15165 && $asc <= -14923) {
            return 'N';
        }

        if ($asc >= -14922 && $asc <= -14915) {
            return 'O';
        }

        if ($asc >= -14914 && $asc <= -14631) {
            return 'P';
        }

        if ($asc >= -14630 && $asc <= -14150) {
            return 'Q';
        }

        if ($asc >= -14149 && $asc <= -14091) {
            return 'R';
        }

        if ($asc >= -14090 && $asc <= -13319) {
            return 'S';
        }

        if ($asc >= -13318 && $asc <= -12839) {
            return 'T';
        }

        if ($asc >= -12838 && $asc <= -12557) {
            return 'W';
        }

        if ($asc >= -12556 && $asc <= -11848) {
            return 'X';
        }

        if ($asc >= -11847 && $asc <= -11056) {
            return 'Y';
        }

        if ($asc >= -11055 && $asc <= -10247) {
            return 'Z';
        }

        return '#';
    }

    /**
     * 对字符串执行指定次数替换
     *
     * @param Mixed $search  查找目标值
     * @param Mixed $replace 替换值
     * @param Mixed $subject 执行替换的字符串／数组
     * @param Int   $limit   允许替换的次数，默认为-1，不限次数
     *
     * @return Mixed
     */
    public static function replaceLimit($search, $replace, $subject, $limit = -1)
    {
        if (is_array($search)) {
            foreach ($search as $k => $v) {
                $search[$k] = '`' . preg_quote($search[$k], '`') . '`';
            }
        } else {
            $search = '`' . preg_quote($search, '`') . '`';
        }
        return preg_replace($search, $replace, $subject, $limit);
    }


    /**
     * 三个字符或三个字符以上掐头取尾，中间用*代替
     * 俩个字符保留都不去除尾部用*代替
     *
     * @param string $str
     *
     * @return string
     */
    public static function alternativeName($str)
    {
        if (preg_match("/[\x{4e00}-\x{9fa5}]+/u", $str)) {
            //按照中文字符计算长度
            $len = mb_strlen($str, 'UTF-8');
            //echo '中文';
            if ($len > 2) {
                //三个字符或三个字符以上掐头取尾，中间用*代替
                $str = mb_substr($str, 0, 1, 'UTF-8') . '*' . mb_substr($str, -1, 1, 'UTF-8');
            } elseif ($len == 2) {
                //俩个字符保留都不去除尾部用*代替
                $str = mb_substr($str, 0, 1, 'UTF-8') . '*';
            }
        } else {
            //按照英文字串计算长度
            $len = strlen($str);
            if ($len > 2) {
                //三个字符或三个字符以上掐头取尾，中间用*代替
                $str = substr($str, 0, 1) . '*' . substr($str, -1);
            } elseif ($len == 2) {
                //俩个字符保留都不去除尾部用*代替
                $str = mb_substr($str, 0, 1, 'UTF-8') . '*';
            }
        }
        return $str;
    }
}
