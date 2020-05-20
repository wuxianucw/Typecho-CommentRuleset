<?php
function roll($array) {
    return $array[mt_rand(0, count($array) - 1)];
}
function percent($num) {
    return mt_rand(0, 99) < $num;
}
function random_string($length, $charset = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789*-+.=-&^%$#@!~/\\[]{}:"|\'()`,<>;_') {
    $size = strlen($charset);
    $res = '';
    for ($i = 0; $i < $length; $i++) {
        $res .= $charset[mt_rand(0,$size - 1)];
    }
    return $res;
}
function make_string() {
    if (percent(50)) {
        return '"' . addslashes(random_string(mt_rand(12, 50))) . '"';
    }
    return '\'' . addslashes(random_string(mt_rand(12, 50))) . '\'';
}
function make_regex() {
    $modifiers = str_shuffle('imsxADSUXJu');
    $len = strlen($modifiers);
    $start = mt_rand(0, $len - 1);
    $len = mt_rand(0, $len - $start);
    return '/' . preg_quote(random_string(mt_rand(12, 50)), '/') . '/' . substr($modifiers, $start, $len);
}
function make_judge_condition() {
    static $names = array('uid', 'nick', 'email', 'url', 'content', 'length', 'ip', 'ua');
    static $optrs = array('==', '!=', '<', '>', '<=', '>=', '<-', '~');
    $name = roll($names);
    $optr = roll($optrs);
    if ($optr == '<-') $target = make_string();
    elseif ($optr == '~') $target = make_regex();
    else $target = strval(mt_rand());
    return "[{$name}{$optr}{$target}]";
}
function make_judge($_) {
    static $signals = array('accept', 'review', 'spam', 'deny', 'skip');
    $res = make_judge_condition();
    if (percent(50)) {
        $res .= percent(50) ? ':' : '!';
        if (percent($_)) $res .= make_judge($_ - mt_rand(0, $_));
        else $res .= roll($signals);
    } else {
        $res .= ':';
        if (percent($_)) $res .= make_judge($_ - mt_rand(0, $_));
        else $res .= roll($signals);
        $res .= '!';
        if (percent($_)) $res .= make_judge($_ - mt_rand(0, $_));
        else $res .= roll($signals);
    }
    return $res . ';';
}
function generate_rule() {
    return make_judge(100);
}
