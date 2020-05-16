<?php
use CommentRuleset\PhpTranslator;
use CommentRuleset\RuleCompiler;

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
$translator = new PhpTranslator();
$input = <<<EOF
# RuleCompiler Test
[uid==1]:[uid==1]:accept;!
 [ email <- '123' ] : review
  ! [ email <- "@" ]
     : [content~/ucw/i]:review;
     ! deny ; ; ;
EOF;
$compiler = new RuleCompiler();
$compiler->parse($input);
echo '<pre>';
highlight_string($compiler->export($translator));
echo '</pre>';
$input = <<<EOF
# RuleCompiler Test
[uid==1]:!accept;
EOF;
$compiler->parse($input);
echo '<pre>';
highlight_string($compiler->export($translator));
echo '</pre>';
