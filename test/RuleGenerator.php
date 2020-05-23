<?php
/**
 * RuleGenerator
 */

 /**
  * 随机从数组中取出一个元素
  * 
  * @param array $array
  * @return mixed
  */
function roll($array) {
    return $array[mt_rand(0, count($array) - 1)];
}

/**
 * 百分概率
 * 
 * @param int $num
 * @return bool
 */
function percent($num) {
    return mt_rand(0, 99) < $num;
}

/**
 * 随机字符串
 * 
 * @param int $length 长度
 * @param string $charset 字符集
 * @return string
 */
function random_string($length, $charset = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789*-+.=-&^%$#@!~/ []{}:"|\'()`,<>;_') {
    $size = strlen($charset);
    $res = '';
    for ($i = 0; $i < $length; $i++) {
        $res .= $charset[mt_rand(0,$size - 1)];
    }
    return $res;
}

class RuleGenerator {
    /**
     * 生成字符串
     */
    private static function makeString() {
        if (percent(50)) {
            return '"' . addslashes(random_string(mt_rand(12, 50))) . '"';
        }
        return '\'' . str_replace('\'', '\\\'', random_string(mt_rand(12, 50))) . '\'';
    }

    /**
     * 生成正则字面量
     */
    private static function makeRegex() {
        $modifiers = str_shuffle('imsxADSUXJu');
        $len = strlen($modifiers);
        $start = mt_rand(0, $len - 1);
        $len = mt_rand(0, $len - $start);
        return '/' . preg_quote(random_string(mt_rand(12, 50)), '/') . '/' . substr($modifiers, $start, $len);
    }

    /**
     * 生成 Judge 的条件块
     */
    private static function makeJudgeCondition() {
        static $names = array('uid', 'nick', 'email', 'url', 'content', 'length', 'ip', 'ua');
        static $int_names = array('uid', 'length');
        static $optrs = array(
            array('==', '!=', '<', '>', '<=', '>='),
            array('==', '<-', '~')
        );
        $name = roll($names);
        if (in_array($name, $int_names)) {
            $optr = roll($optrs[0]);
            $target = strval(mt_rand());
        } else {
            $optr = roll($optrs[1]);
            if ($optr == '~') $target = self::makeRegex();
            else $target = self::makeString();
        }
        return "[ {$name} {$optr} {$target} ]";
    }

    /**
     * 生成整个 Judge，带嵌套
     */
    private static function makeJudge($_) {
        static $signals = array('accept', 'review', 'spam', 'deny', 'skip');
        $res = self::makeJudgeCondition();
        if (percent(25)) {
            $res .= percent(50) ? ' : ' : ' ! ';
            if (percent($_)) $res .= self::makeJudge($_ - mt_rand(0, 10));
            else $res .= roll($signals);
        } else {
            $res .= ' : ';
            if (percent($_)) $res .= self::makeJudge($_ - mt_rand(0, 10));
            else $res .= roll($signals);
            $res .= ' ! ';
            if (percent($_)) $res .= self::makeJudge($_ - mt_rand(0, 10));
            else $res .= roll($signals);
        }
        return $res . ' ;';
    }

    /**
     * 生成随机规则
     */
    public static function newRule() {
        return self::makeJudge(100);
    }
}
