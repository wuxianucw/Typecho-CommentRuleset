<?php
use CommentRuleset\RuleCompiler;

define('__TYPECHO_ROOT_DIR__', '/');
require '../libs/RuleCompiler.php';
$input = <<<EOF
# RuleCompiler Test
[uid!=1]:[uid==1]:accept;!
 [ email <- '123' ] : review
  ! [ email <- "@" ]
     : [content~/ucw/i]:review;
     !deny;;;
EOF;
$compiler = new RuleCompiler();
$compiler->parse($input);
var_dump($compiler);
$input = <<<EOF
# RuleCompiler Test
[uid==1]:!accept;
EOF;
$compiler->parse($input);
var_dump($compiler);
