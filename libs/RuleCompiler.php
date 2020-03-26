<?php
namespace CommentRuleset;
if(!defined('__TYPECHO_ROOT_DIR__')) exit;
/**
 * Typecho 评论规则集插件 规则编译器
 * 
 * @package CommentRuleset
 * @author wuxianucw
 * @version 1.0.0
 * @license GNU Affero General Public License v3.0
 * @link https://ucw.moe/
 */
class RuleCompiler {
    /**
     * 抽象语法树
     * 
     * @access protected
     * @var \CommentRuleset\Root
     */
    protected $_ast;

    /**
     * 解析规则
     * 
     * @access public
     * @param string $rule
     * @return bool
     * @throws \CommentRuleset\Exception
     */
    public function parse($rule) {
        $rule = explode("\n", trim($rule)); // 按行分割，便于处理注释
        $special = array( // special 表 'expect' => 'ClassName'
            '"' => '\CommentRuleset\DoubleQuotedText',
            '\'' => '\CommentRuleset\SingleQuotedText',
            '/' => '\CommentRuleset\Regex'
        );
        $special_flag = array_keys($special); // 预处理一次，提高效率
        $expect = ''; // 当前状态等待读入的字符，只能是 $special_flag 中的字符
        $backslash = false; // 反斜杠标记，上一个字符是否是反斜杠，仅 $ignore == true 时有意义
        $optr_flag = '=!<>~-'; // 运算符表
        $optr = false; // 运算符标记，是否正在读取运算符
        $reading = ''; // 读入串，当前已经读入的内容
        $ast = new Root(); // 抽象语法树
        $node = $ast; // 当前操作的节点
        $eor = false; // end of rule 标志
        foreach($rule as $line) {
            $line .= "\n"; // 补上行尾
            $len = strlen($line);
            for($i = 0; $i < $len; $i ++) {
                if($expect == '') {
                    if($line[$i] == '#') break; // 跳过注释
                    if(strpos(" \t\n\r\x0B\0", $line[$i]) !== false) continue; // 没有 expect 时空白字符一定可以忽略
                    if($eor) throw new Exception('解析时遇到了意外的 <code>' . htmlspecialchars($line[$i]) . '</code，期望 <code>EOF</code>。');
                    if($line[$i] == '[') {
                        if($reading != '' || $node->pos == 0) throw new Exception('解析时遇到了意外的 <code>' . htmlspecialchars($line[$i]) . '</code>。');
                        $method = $node->pos == 1 ? 'then' : 'else';
                        $child = new Judge();
                        $node->$method($child);
                        $node = $child;
                    } elseif($line[$i] == ']') {
                        if($node instanceof Value) {
                            if($reading != '') throw new Exception('解析时遇到了无法识别的结构。');
                            $node = $node->parent;
                        } elseif($node instanceof Judge) {
                            if($reading == '') throw new Exception('解析时遇到了意外的 <code>]</code>。');
                            $child = new Value();
                            $child->set($reading);
                            if($child->type != 'number') throw new Exception('解析时遇到了无法识别的结构。');
                            $node->target($child);
                        } else throw new Exception('解析时遇到了意外的 <code>]</code>。');
                        $reading = '';
                    } elseif($line[$i] == ':') {
                        if(!$node instanceof Judge || $node->then != null || $reading != '') throw new Exception('解析时遇到了意外的 <code>:</code>。');
                        $node->pos = 1;
                    } elseif($line[$i] == '!') {
                        if(!$node instanceof Judge || $node->else != null) throw new Exception('解析时遇到了意外的 <code>!</code>。');
                        if($reading != '' && $node->pos == 1) {
                            $child = new Value();
                            $child->set($reading);
                            if($child->type != 'signal') throw new Exception('解析时遇到了无法识别的结构。');
                            $node->then($child);
                            $reading = '';
                        } else throw new Exception('解析时遇到了意外的 <code>!</code>。');
                        $node->pos = 2;
                    } elseif($line[$i] == ';') {
                        if(!$node instanceof Judge || $node->pos == 0) throw new Exception('解析时遇到了意外的 <code>;</code>。');
                        if($reading != '') {
                            $method = $node->pos == 1 ? 'then' : 'else';
                            if($node->$method != null) throw new Exception('解析时遇到了无法识别的结构。');
                            $child = new Value();
                            $child->set($reading);
                            if($child->type != 'signal') throw new Exception('解析时遇到了无法识别的结构 <code>' . htmlspecialchars($reading) . ';</code>。');
                            $node->$method($child);
                            $reading = '';
                        }
                        if(!$node->isLegal()) throw new Exception('解析时遇到了意外的 <code>;</code>。');
                        $node = $node->parent;
                        if($node instanceof Root) $eor = true;
                    } elseif($node instanceof Judge && $node->pos == 0 && in_array($line[$i], $special_flag)) {
                        $optr = false;
                        if($node->target != null) throw new Exception('解析时遇到了意外的 <code>' . htmlspecialchars($line[$i]) . '</code>。');
                        if(($node->optr != null && $reading != '') || ($node->optr == null && $reading == '')) throw new Exception('解析时遇到了意外的 <code>' . htmlspecialchars($line[$i]) . '</code>。');
                        if($reading != '') $node->optr($reading);
                        $reading = '';
                        if($node->name == '' || $node->optr == null) throw new Exception('解析时遇到了意外的 <code>' . htmlspecialchars($line[$i]) . '</code>。');
                        $expect = $line[$i];
                        $child = new $special[$expect]();
                        $node->target($child);
                        $node = $child;
                    } elseif($optr == false && $node instanceof Judge && $node->pos == 0 && strpos($optr_flag, $line[$i]) !== false) {
                        if($node->name != '' || $reading == '') throw new Exception('解析时遇到了意外的 <code>' . htmlspecialchars($line[$i]) . '</code>。');
                        if($node->optr != null) throw new Exception('解析时遇到了意外的 <code>' . htmlspecialchars($line[$i]) . '</code>。');
                        $node->name($reading);
                        $reading = $line[$i];
                        $optr = true;
                    } elseif($optr && strpos($optr_flag, $line[$i]) === false) {
                        $optr = false;
                        $node->optr($reading);
                        $reading = $line[$i];
                    } else {
                        $reading .= $line[$i];
                    }
                } else {
                    if($backslash) {
                        $backslash = false;
                        $reading .= $line[$i];
                    } else {
                        if($line[$i] == '\\') {
                            $backslash = true;
                            $reading .= $line[$i];
                        } elseif($line[$i] == $expect) {
                            $node->set($reading);
                            $reading = '';
                            $expect = '';
                        } else {
                            $reading .= $line[$i];
                        }
                    }
                }
            }
        }
        if(!$node instanceof Root) throw new Exception('解析时遇到了意外的 <code>EOF</code>。');
        $this->_ast = $ast;
        var_dump($ast);
        return true;
    }
}

/**
 * 规则编译器异常类
 */
class Exception extends \Exception {}

/**
 * 运算符处理类
 */
class Operator {
    const OPTRS = array('==', '!=', '<', '>', '<=', '>=', '<-', '~');

    /**
     * 运算符类型
     * 
     * @access public
     * @var string
     */
    public $type;

    /**
     * 构造函数
     * 
     * @access public
     * @param string $operator
     * @return void
     * @throws \CommentRuleset\Exception
     */
    public function __construct($optr) {
        if(in_array($optr, self::OPTRS)) $this->type = $optr;
        else throw new Exception('解析时遇到了无法识别的运算符 <code>' . htmlspecialchars($optr) . '</code>。');
    }
}

/**
 * AST Node
 */
class ASTNode {
    /**
     * 父节点
     * 
     * @access public
     * @var ASTNode
     */
    public $parent;

    public function __construct() {
        $this->parent = null;
    }
}

/**
 * Root Node
 */
class Root extends ASTNode {
    /**
     * 下级 Judge
     * 
     * @access public
     * @var Judge
     */
    public $judge;

    /**
     * 标志量
     * 
     * @access public
     * @var int
     */
    public $pos;

    public function __construct() {
        parent::__construct();
        $this->count = 0;
        $this->judge = null;
        $this->pos = 1;
    }

    public function then($node) {
        $node->parent = $this;
        $this->judge = $node;
    }
}

/**
 * Judge Node
 */
class Judge extends ASTNode {
    /**
     * 合法 name 列表
     */
    const NAMES = array('uid', 'nick', 'email', 'url', 'content', 'ip', 'ua');

    /**
     * 判断标识
     * 
     * @access public
     * @var string
     */
    public $name;

    /**
     * 运算符
     * 
     * @access public
     * @var Operator
     */
    public $optr;

    /**
     * 比较目标
     * 
     * @access public
     * @var string
     */
    public $target;

    /**
     * :
     * 
     * @access public
     * @var mixed
     */
    public $then;

    /**
     * !
     * 
     * @access public
     * @var mixed
     */
    public $else;

    /**
     * 标志量
     * 
     * @access public
     * @var int
     */
    public $pos;

    public function __construct() {
        parent::__construct();
        $this->name = '';
        $this->optr = null;
        $this->target = null;
        $this->then = null;
        $this->else = null;
        $this->pos = 0;
    }

    public function then($node) {
        $node->parent = $this;
        $this->then = $node;
    }

    public function else($node) {
        $node->parent = $this;
        $this->else = $node;
    }

    public function name($name) {
        if(!in_array($name, self::NAMES)) throw new Exception('无法识别的标识名称 <code>' . htmlspecialchars($name) . '</code>。');
        $this->name = $name;
    }

    public function optr($optr) {
        $this->optr = new Operator($optr);
    }

    public function target($target) {
        $target->parent = $this;
        $this->target = $target;
    }

    public function isLegal() {
        return $this->name != ''
            && $this->optr != null
            && $this->target != null
            && ($this->then != null || $this->else != null);
    }
}

/**
 * Value Node
 */
class Value extends ASTNode {
    /**
     * 类型
     * 
     * @access public
     * @var string
     */
    public $type;

    /**
     * 值
     * 
     * @access public
     * @var mixed
     */
    public $value;

    public function set($value) {
        $intval = intval($value);
        if(!$intval && $value !== '0') {
            $this->type = 'signal';
            $this->value = $value;
        } else {
            $this->type = 'number';
            $this->value = $intval;
        }
    }
}

/**
 * SingleQuotedText Node
 */
class SingleQuotedText extends Value {
    public function set($text) {
        if(self::checkSingleQuotedText($text)) {
            $this->type = 'sqtext';
            $this->value = $text;
        }
    }

    /**
     * 单引号语法检查
     * 
     * @access public
     * @param string $str 文本 不含两侧的单引号
     * @return bool
     * @throws \CommentRuleset\Exception
     */
    public static function checkSingleQuotedText($str) {
        $len = strlen($str);
        $special = false;
        for($i = 0; $i < $len; $i ++) {
            if($special) {
                if($str[$i] == '\\' || $str[$i] == '\'') $special = false;
                else throw new Exception('解析时遇到了意外的反斜杠，如果要在单引号字符串中使用反斜杠，请使用反斜杠转义。', $i);
            } else {
                if($str[$i] == '\\') $special = true;
                elseif($str[$i] == '\'') throw new Exception('解析时遇到了意外的单引号，如果要在单引号字符串中使用单引号，请使用反斜杠转义。', $i);
            }
        }
        if($special) throw new Exception('解析时遇到了意外的反斜杠，如果要在单引号字符串中使用反斜杠，请使用反斜杠转义。', $len - 1); // 理论上不会出现这种情况
        return true;
    }
}

/**
 * DoubleQuotedText Node
 */
class DoubleQuotedText extends Value {
    public function set($text) {
        if(self::checkDoubleQuotedText($text)) {
            $this->type = 'dqtext';
            $this->value = $text;
        }
    }

    /**
     * 双引号语法检查
     * 
     * @access public
     * @param string $str 文本 不含两侧的双引号
     * @return bool
     * @throws \CommentRuleset\Exception
     */
    public static function checkDoubleQuotedText($str) {
        $len = strlen($str);
        $escape = array('\\', '"', 'n', 'r', 't', '$', '0', '1', '2', '3', '4', '5', '6', '7', 'x');
        $special = false;
        for($i = 0; $i < $len; $i ++) {
            if($special) {
                if(in_array($str[$i], $escape)) $special = false;
                else throw new Exception('解析时遇到了意外的反斜杠，如果要在双引号字符串中使用反斜杠，请使用反斜杠转义。', $i);
            } else {
                if($str[$i] == '\\') $special = true;
                elseif($str[$i] == '"') throw new Exception('解析时遇到了意外的双引号，如果要在双引号字符串中使用双引号，请使用反斜杠转义。', $i);
            }
        }
        if($special) throw new Exception('解析时遇到了意外的反斜杠，如果要在双引号字符串中使用反斜杠，请使用反斜杠转义。', $len - 1); // 理论上不会出现这种情况
        return true;
    }
}

/**
 * Regex Node
 */
class Regex extends Value {
    public function set($regex) {
        if(self::regexCompileTest($regex)) {
            $this->type = 'regex';
            $this->value = $regex;
        } else throw new Exception('正则表达式编译失败。');
    }

    /**
     * 正则表达式编译测试
     * 
     * @access public
     * @param string $regex 正则表达式 不含两侧的 "/"
     * @return bool
     */
    public static function regexCompileTest($regex) {
        return @preg_match("/$regex/", '') !== false;
    }
}
