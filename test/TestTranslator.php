<?php
use CommentRuleset\JsonTranslator;
use CommentRuleset\PhpTranslator;
use CommentRuleset\RuleCompiler;
use CommentRuleset\RuleTranslator;

define('__TYPECHO_ROOT_DIR__', '/');
require '../libs/RuleCompiler.php';
echo <<<EOF
<style>
pre {
    white-space: pre-wrap;       /* Since CSS 2.1 */
    white-space: -moz-pre-wrap;  /* Mozilla, since 1999 */
    white-space: -pre-wrap;      /* Opera 4-6 */
    white-space: -o-pre-wrap;    /* Opera 7 */
    word-wrap: break-word;       /* Internet Explorer 5.5+ */
}
</style>
EOF;
function test_php_translator($input) {
    echo '<pre>';
    try {
        highlight_string((new RuleCompiler())->parse($input)->export(new PhpTranslator()));
    } catch (\CommentRuleset\Exception $e) {
        echo $e->getMessage();
    }
    echo '</pre>';
}
function test_rule_translator($input) {
    $compiler = new RuleCompiler();
    $translator = new RuleTranslator();
    echo '<pre>';
    try {
        $res = $compiler->parse($input)->export($translator);
        echo '<code>';
        echo htmlspecialchars($res);
        echo '<br>Verify: ';
        echo $res === $compiler->parse($res)->export($translator) ? 'true' : 'false';
        echo '</code>';
    } catch (\CommentRuleset\Exception $e) {
        echo $e->getMessage();
    }
    echo '</pre>';
}
function test_json_translator($input) {
    echo '<pre>';
    try {
        $res = (new RuleCompiler())->parse($input)->export(new JsonTranslator());
        echo '<code>';
        echo htmlspecialchars($res);
        echo '</code>';
    } catch (\CommentRuleset\Exception $e) {
        echo $e->getMessage();
    }
    echo '</pre>';
}
$input = <<<EOF
# RuleCompiler Test
[uid==1]:[uid==1]:accept;!
 [ email <- '12
 3' ] : review
  ! [ email <- "@" ]
     : [content~/ucw/i]:review;
     ! deny ; ; ;
EOF;
test_php_translator($input);
test_rule_translator($input);
test_json_translator($input);
$input = <<<EOF
# RuleCompiler Test
[uid==1]:!accept;
EOF;
test_php_translator($input);
test_rule_translator($input);
test_json_translator($input);
$input = <<<EOF
# Undefined Behavior
accept
EOF;
test_php_translator($input);
test_rule_translator($input);
test_json_translator($input);
