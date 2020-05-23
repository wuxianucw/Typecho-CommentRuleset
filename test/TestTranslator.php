<?php
use CommentRuleset\JsonTranslator;
use CommentRuleset\PhpTranslator;
use CommentRuleset\RuleCompiler;
use CommentRuleset\RuleTranslator;

define('__TYPECHO_ROOT_DIR__', '/');
require '../libs/RuleCompiler.php';
require 'RuleGenerator.php';
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
        echo '<br>----- Verify: -----<br>';
        echo ' [0] => ' . ($res == $input ? 'true' : 'false');
        echo '<br>';
        echo ' [1] => ' . ($res == $compiler->parse($res)->export($translator) ? 'true' : 'false');
        echo '<br>----- Result: -----<br>';
        echo htmlspecialchars($res);
        echo '</code>';
    } catch (\CommentRuleset\Exception $e) {
        echo $e->getMessage();
    }
    echo '<br>----- Input: -----<br>';
    echo htmlspecialchars($input);
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
$input = RuleGenerator::newRule();
test_rule_translator($input);
test_php_translator($input);
test_json_translator($input);
