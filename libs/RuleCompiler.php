<?php
namespace CommentRuleset;
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
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

    function __construct() {
        $this->_ast = null;
    }

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
        $modifiers = 'imsxADSUXJu'; // 正则表达式修饰符
        $special_flag = array_keys($special); // 预处理一次，提高效率
        $expect = ''; // 当前状态等待读入的字符，只能是 $special_flag 中的字符
        $backslash = false; // 反斜杠标记，上一个字符是否是反斜杠，仅 $ignore == true 时有意义
        $optr_flag = '=!<>~-'; // 运算符表
        $optr = false; // 运算符标记，是否正在读取运算符
        $reading = ''; // 读入串，当前已经读入的内容
        $ast = new Root(); // 抽象语法树
        $node = $ast; // 当前操作的节点
        $eor = false; // end of rule 标志
        foreach ($rule as $line) {
            $line .= "\n"; // 补上行尾
            $len = strlen($line);
            for ($i = 0; $i < $len; $i++) {
                if ($expect == '') {
                    if ($line[$i] == '#') break; // 跳过注释
                    if (strpos(" \t\n\r\x0B\0", $line[$i]) !== false) continue; // 没有 expect 时空白字符一定可以忽略
                    if ($eor) throw new Exception('解析时遇到了意外的 <code>' . htmlspecialchars($line[$i]) . '</code，期望 <code>EOF</code>。');
                    if ($line[$i] == '[') {
                        if ($reading != '' || $node->pos == 0) throw new Exception('解析时遇到了意外的 <code>' . htmlspecialchars($line[$i]) . '</code>。');
                        $method = $node->pos == 1 ? 'then' : 'else';
                        $child = new Judge();
                        $node->$method($child);
                        $node = $child;
                    } elseif ($line[$i] == ']') {
                        if ($node instanceof Value) {
                            if ($reading != '') throw new Exception('解析时遇到了无法识别的结构。');
                            $node = $node->parent;
                        } elseif ($node instanceof Judge) {
                            if ($reading == '') throw new Exception('解析时遇到了意外的 <code>]</code>。');
                            $child = new Value();
                            $child->set($reading);
                            if ($child->type != 'number') throw new Exception('解析时遇到了无法识别的结构。');
                            $node->target($child);
                        } else throw new Exception('解析时遇到了意外的 <code>]</code>。');
                        $reading = '';
                    } elseif ($line[$i] == ':') {
                        if (!$node instanceof Judge || $node->then != null || $reading != '') throw new Exception('解析时遇到了意外的 <code>:</code>。');
                        $node->pos = 1;
                    } elseif ($line[$i] == '!') {
                        if (!$node instanceof Judge || $node->else != null) throw new Exception('解析时遇到了意外的 <code>!</code>。');
                        if ($reading != '' && $node->pos == 1) {
                            $child = new Value();
                            $child->set($reading);
                            if ($child->type != 'signal') throw new Exception('解析时遇到了无法识别的结构。');
                            $node->then($child);
                            $reading = '';
                        }
                        $node->pos = 2;
                    } elseif ($line[$i] == ';') {
                        if (!$node instanceof Judge || $node->pos == 0) throw new Exception('解析时遇到了意外的 <code>;</code>。');
                        if ($reading != '') {
                            $method = $node->pos == 1 ? 'then' : 'else';
                            if ($node->$method != null) throw new Exception('解析时遇到了无法识别的结构。');
                            $child = new Value();
                            $child->set($reading);
                            if ($child->type != 'signal') throw new Exception('解析时遇到了无法识别的结构 <code>' . htmlspecialchars($reading) . ';</code>。');
                            $node->$method($child);
                            $reading = '';
                        }
                        if (!$node->isLegal()) throw new Exception('解析时遇到了意外的 <code>;</code>。');
                        $node = $node->parent;
                        if ($node instanceof Root) $eor = true;
                    } elseif ($node instanceof Judge && $node->pos == 0 && in_array($line[$i], $special_flag)) {
                        $optr = false;
                        if ($node->target != null) throw new Exception('解析时遇到了意外的 <code>' . htmlspecialchars($line[$i]) . '</code>。');
                        if (($node->optr != null && $reading != '') || ($node->optr == null && $reading == '')) throw new Exception('解析时遇到了意外的 <code>' . htmlspecialchars($line[$i]) . '</code>。');
                        if ($reading != '') $node->optr($reading);
                        $reading = '';
                        if ($node->name == '' || $node->optr == null) throw new Exception('解析时遇到了意外的 <code>' . htmlspecialchars($line[$i]) . '</code>。');
                        $expect = $line[$i];
                        $child = new $special[$expect]();
                        $node->target($child);
                        $node = $child;
                    } elseif ($optr == false && $node instanceof Judge && $node->pos == 0 && strpos($optr_flag, $line[$i]) !== false) {
                        if ($node->name != '' || $reading == '') throw new Exception('解析时遇到了意外的 <code>' . htmlspecialchars($line[$i]) . '</code>。');
                        if ($node->optr != null) throw new Exception('解析时遇到了意外的 <code>' . htmlspecialchars($line[$i]) . '</code>。');
                        $node->name($reading);
                        $reading = $line[$i];
                        $optr = true;
                    } elseif ($optr && strpos($optr_flag, $line[$i]) === false) {
                        $optr = false;
                        $node->optr($reading);
                        $reading = $line[$i];
                    } else {
                        $reading .= $line[$i];
                    }
                } else {
                    if ($backslash) {
                        $backslash = false;
                        $reading .= $line[$i];
                    } else {
                        if ($line[$i] == '\\') {
                            $backslash = true;
                            $reading .= $line[$i];
                        } elseif ($line[$i] == $expect) {
                            $node->set($reading);
                            $reading = '';
                            if ($expect == '/') { // 处理正则字面量模式修饰符
                                while ($i < $len - 1 && strpos($modifiers, $line[$i + 1]) !== false) {
                                    $i++;
                                    $reading .= $line[$i];
                                }
                                $node->modifiers = $reading;
                                $reading = '';
                            }
                            $expect = '';
                        } else {
                            $reading .= $line[$i];
                        }
                    }
                }
            }
        }
        if (!$node instanceof Root) throw new Exception('解析时遇到了意外的 <code>EOF</code>。');
        $this->_ast = $ast;
        return true;
    }

    /**
     * 输出语法树
     * 
     * @access public
     * @return \CommentRuleset\Root|null
     */
    public function __debugInfo() {
        return array('ast' => $this->_ast);
    }

    /**
     * 导出为其他形式
     * 
     * @access public
     * @param \CommentRuleset\Translator $translator
     * @param bool $ifPrint 是否输出，默认 false
     * @return string
     * @throws \CommentRuleset\Exception
     */
    public function export($translator, $ifPrint = false) {
        function export_dfs($node, $translator) {
            if (!$node instanceof ASTNode) return '';
            if (!$translator->enterNode()) return '';
            $result = $translator->nodeStartToken($node);
            if ($node instanceof Root) {
                $result .= export_dfs($node->judge, $translator);
            } elseif ($node instanceof Judge) {
                $result .= export_dfs($node->then, $translator);
                $result .= export_dfs($node->else, $translator);
            }
            $result .= $translator->nodeEndToken($node);
            if (!$translator->leaveNode()) return '';
            return $result;
        }
        $result = export_dfs($this->_ast, $translator);
        if ($ifPrint) echo $result;
        return $result;
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
    /**
     * 运算符表
     * 
     * @var array
     */
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
        if (in_array($optr, self::OPTRS)) $this->type = $optr;
        else throw new Exception('解析时遇到了无法识别的运算符 <code>' . htmlspecialchars($optr) . '</code>。');
    }

    public function __toString() {
        return $this->type;
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
     * @var \CommentRuleset\ASTNode
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
     * @var \CommentRuleset\Judge
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
     * 
     * @var array
     */
    const NAMES = array('uid', 'nick', 'email', 'url', 'content', 'length', 'ip', 'ua');

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
     * @var \CommentRuleset\Operator
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
        if (!in_array($name, self::NAMES)) throw new Exception('解析时遇到了无法识别的名称 <code>' . htmlspecialchars($name) . '</code>。');
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
     * signal 表
     * 
     * @var array
     */
    const SIGNALS = array('pass', 'accept', 'review', 'spam', 'deny');

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
        if (!$intval && $value !== '0') {
            if (!in_array($value, self::SIGNALS)) throw new Exception('解析时遇到了无法识别的标识 <code>' . htmlspecialchars($value) . '</code>。');
            $this->type = 'signal';
            $this->value = $value;
        } else {
            $this->type = 'number';
            $this->value = $intval;
        }
    }

    public function __toString() {
        return strval($this->value);
    }
}

/**
 * SingleQuotedText Node
 */
class SingleQuotedText extends Value {
    public function set($text) {
        if (self::checkSingleQuotedText($text)) {
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
        for ($i = 0; $i < $len; $i++) {
            if ($special) {
                if ($str[$i] == '\\' || $str[$i] == '\'') $special = false;
                else throw new Exception('解析时遇到了意外的反斜杠，如果要在单引号字符串中使用反斜杠，请使用反斜杠转义。', $i);
            } else {
                if ($str[$i] == '\\') $special = true;
                elseif ($str[$i] == '\'') throw new Exception('解析时遇到了意外的单引号，如果要在单引号字符串中使用单引号，请使用反斜杠转义。', $i);
            }
        }
        if ($special) throw new Exception('解析时遇到了意外的反斜杠，如果要在单引号字符串中使用反斜杠，请使用反斜杠转义。', $len - 1); // 理论上不会出现这种情况
        return true;
    }

    public function __toString() {
        return "'{$this->value}'";
    }
}

/**
 * DoubleQuotedText Node
 */
class DoubleQuotedText extends Value {
    public function set($text) {
        if (self::checkDoubleQuotedText($text)) {
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
        for ($i = 0; $i < $len; $i++) {
            if ($special) {
                if (in_array($str[$i], $escape)) $special = false;
                else throw new Exception('解析时遇到了意外的反斜杠，如果要在双引号字符串中使用反斜杠，请使用反斜杠转义。', $i);
            } else {
                if ($str[$i] == '\\') $special = true;
                elseif ($str[$i] == '"') throw new Exception('解析时遇到了意外的双引号，如果要在双引号字符串中使用双引号，请使用反斜杠转义。', $i);
            }
        }
        if ($special) throw new Exception('解析时遇到了意外的反斜杠，如果要在双引号字符串中使用反斜杠，请使用反斜杠转义。', $len - 1); // 理论上不会出现这种情况
        return true;
    }

    public function __toString() {
        return "\"{$this->value}\"";
    }
}

/**
 * Regex Node
 */
class Regex extends Value {
    /**
     * 模式修饰符
     * 
     * @access public
     * @var string
     */
    public $modifier;

    public function set($regex) {
        if (self::regexCompileTest($regex)) {
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

    public function __toString() {
        return '\'/' . str_replace(array('\\', '\''), array('\\\\', '\\\''), $this->value) . "/{$this->modifier}'";
    }
}

/**
 * 翻译器抽象类
 */
abstract class Translator {
    /**
     * 进入节点前
     * 
     * @access public
     * @param \CommentRuleset\ASTNode $node
     * @return bool true 表示进入，false 表示不进入。无论进不进入，均会对应调用 leaveNode
     */
    public function enterNode($node) {
        return true;
    }

    /**
     * 离开节点后
     * 
     * @access public
     * @param \CommentRuleset\ASTNode $node
     * @return bool true 表示保留该节点中已翻译的内容，false 表示丢弃。
     */
    public function leaveNode($node) {
        return true;
    }

    /**
     * 节点开始标记
     * 
     * @access public
     * @param \CommentRuleset\ASTNode $node
     * @return string
     * @throws \CommentRuleset\Exception
     */
    abstract public function nodeStartToken($node);

    /**
     * 节点结束标记
     * 
     * @access public
     * @param \CommentRuleset\ASTNode $node
     * @return string
     * @throws \CommentRuleset\Exception
     */
    abstract public function nodeEndToken($node);
}

/**
 * PHP 翻译器
 */
class PhpTranslator extends Translator {
    public function nodeStartToken($node) {
        $result = '';
        if ($node instanceof Root) {
            $result = "<?php\nif (!defined('__TYPECHO_ROOT_DIR__')) exit;\n";
        } elseif ($node instanceof Judge) {
            $result = 'if (';
            if ($node->optr == '<-') {
                if (!$node->target instanceof Value || $node->target instanceof Regex)
                    throw new Exception('<code>&lt;-</code> 运算符的右侧应当为文本。');
                $result .= "stripos(\$param['{$node->name}'], {$node->target}) !== false";
            } elseif ($node->optr == '~') {
                if (!$node->target instanceof Regex)
                    throw new Exception('<code>~</code> 运算符只允许与正则字面量搭配使用。');
                $result .= "preg_match({$node->target}, \$param['{$node->name}']) === 1";
            } else {
                if ($node->target instanceof Regex)
                    throw new Exception('正则字面量只允许与 <code>~</code> 运算符搭配使用。');
                $result .= "\$param['{$node->name}'] {$node->optr} {$node->target}";
            }
            $result .= ') {';
            // TODO: 根据下级节点情况打标记
        }
        return $result;
    }

    public function nodeEndToken($node) {
        return 'TODO';
    }
}
