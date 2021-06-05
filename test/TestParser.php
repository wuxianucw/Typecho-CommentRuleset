<?php
use CommentRuleset\RuleCompiler;

define('__TYPECHO_ROOT_DIR__', '/');
require 'mocks/Helper.php';
require '../libs/RuleCompiler.php';
require 'RuleGenerator.php';
$input = RuleGenerator::newRule();
$compiler = new RuleCompiler();
$compiler->parse($input);
var_dump($compiler);
