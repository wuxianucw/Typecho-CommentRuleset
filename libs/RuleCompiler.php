<?php
namespace CommentRuleset;
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
/**
 * Typecho 评论规则集插件 规则编译器
 * 
 * @package CommentRuleset
 * @author wuxianucw
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
     * @return \CommentRuleset\RuleCompiler
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
        $backslash = false; // 反斜杠标记，上一个字符是否是反斜杠，仅 $expect 不为空时有意义
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
                if ($expect == '') { // expect 为空，正常解析 Token
                    if ($line[$i] == '#') break; // 跳过注释
                    if (strpos(" \t\n\r\x0B\0", $line[$i]) !== false) continue; // 没有 expect 时空白字符一定可以忽略
                    if ($eor) // 此时规则应当已经结束
                        throw new Exception('解析时遇到了意外的 <code>' . htmlspecialchars($line[$i]) . '</code>，期望 <code>EOF</code>。');
                    if ($line[$i] == '[') { // 读到 Judge 开始的 Token
                        if ($reading != '' || $node->pos == 0) // 一个 Judge 之前不能有字面量，同时这个 Judge 也不应该位于另一个 Judge 的条件块中
                            throw new Exception('解析时遇到了意外的 <code>' . htmlspecialchars($line[$i]) . '</code>。');
                        $method = $node->pos == 1 ? 'then' : 'else'; // 处理分支
                        $child = new Judge();
                        $node->$method($child);
                        $node = $child;
                    } elseif ($line[$i] == ']') { // 读到 Judge 条件结束的 Token
                        if ($node instanceof Value) { // 运算符右侧是字符串或正则字面量的情形
                            if ($reading != '') throw new Exception('解析时遇到了无法识别的结构。'); // 此时整个字面量应该已经处理完毕，否则非法
                            $node = $node->parent;
                        } elseif ($node instanceof Judge) { // 运算符右侧是数字的情形
                            if ($reading == '') throw new Exception('解析时遇到了意外的 <code>]</code>。'); // 运算符右侧什么也没有，非法
                            $child = new Value();
                            $child->set($reading);
                            if ($child->type != 'number') throw new Exception('解析时遇到了无法识别的结构。'); // 必须是一个数字
                            $node->target($child);
                        } else throw new Exception('解析时遇到了意外的 <code>]</code>。'); // 其余情况均不应该出现一个 ']'
                        $reading = ''; // 清空读入串以便后续使用
                    } elseif ($line[$i] == ':') { // 读到 Judge then 开始的 Token
                        if (!$node instanceof Judge || $node->then != null || $reading != '') // 当前节点必须是一个未设置 then 的 Judge，Token 前也不应该有多余的东西
                            throw new Exception('解析时遇到了意外的 <code>:</code>。');
                        $node->pos = 1; // 设置标志为处理 then 分支
                    } elseif ($line[$i] == '!') { // 读到 Judge else 开始的 Token (*) 还可能是运算符开始的 Token
                        if (!$node instanceof Judge || $node->else != null) // 当前节点必须是一个未设置 then 的 Judge，但 Token 前可能还有一个 signal 作为前一个分支的内容
                            throw new Exception('解析时遇到了意外的 <code>!</code>。');
                        if ($node->name == '') { // 此时这是一个运算符开始的 Token
                            if ($reading == '') throw new Exception('解析时遇到了意外的 <code>' . htmlspecialchars($line[$i]) . '</code>。');
                            $node->name($reading); // 设置 name
                            $reading = $line[$i];
                            $optr = true; // 设置运算符标记为 true
                        } else {
                            if ($reading != '' && $node->pos == 1) { // 处理这个 signal
                                $child = new Value();
                                $child->set($reading);
                                if ($child->type != 'signal') throw new Exception('解析时遇到了无法识别的结构。'); // 非法
                                $node->then($child);
                                $reading = '';
                            }
                            $node->pos = 2; // 设置标志为处理 else 分支
                        }
                    } elseif ($line[$i] == ';') { // 读到 Judge 结束的 Token
                        if (!$node instanceof Judge || $node->pos == 0) // 当前节点必须是一个有分支的 Judge
                            throw new Exception('解析时遇到了意外的 <code>;</code>。');
                        if ($reading != '') { // 处理前一个分支中的 signal
                            $method = $node->pos == 1 ? 'then' : 'else';
                            if ($node->$method != null) throw new Exception('解析时遇到了无法识别的结构。');
                            $child = new Value();
                            $child->set($reading);
                            if ($child->type != 'signal') throw new Exception('解析时遇到了无法识别的结构 <code>' . htmlspecialchars($reading) . ';</code>。');
                            $node->$method($child);
                            $reading = '';
                        }
                        if (!$node->isLegal()) throw new Exception('解析时遇到了意外的 <code>;</code>。'); // Judge 必须合法
                        $node = $node->parent;
                        if ($node instanceof Root) $eor = true; // 如果已经回到根节点，则期望规则结束
                    } elseif ($node instanceof Judge && $node->pos == 0 && in_array($line[$i], $special_flag)) { // 在 Judge 条件块中读到特殊字面量
                        $optr = false; // 复位运算符标记，结束运算符读入状态
                        if ($node->target != null) // 特殊字面量必须在 target 位置，该位置现在应该为空
                            throw new Exception('解析时遇到了意外的 <code>' . htmlspecialchars($line[$i]) . '</code>。');
                        if (($node->optr != null && $reading != '') || ($node->optr == null && $reading == '')) // 已经存在运算符但读入串不为空、不存在运算符且读入串为空均不合法
                            throw new Exception('解析时遇到了意外的 <code>' . htmlspecialchars($line[$i]) . '</code>。');
                        if ($reading != '') $node->optr($reading); // 设置运算符
                        $reading = '';
                        if ($node->name == '' || $node->optr == null) // 完整性检查
                            throw new Exception('解析时遇到了意外的 <code>' . htmlspecialchars($line[$i]) . '</code>。');
                        $expect = $line[$i]; // 标记 expect
                        $child = new $special[$expect]();
                        $node->target($child);
                        $node = $child;
                    } elseif ($optr == false && $node instanceof Judge && $node->pos == 0 && strpos($optr_flag, $line[$i]) !== false) { // 在 Judge 条件块中当运算符标记为 false 时读到可能构成运算符的字符
                        if ($node->name != '' || $reading == '') // 此时需要设置 name，所以 name 必须为空，同时读入串中必须有内容
                            throw new Exception('解析时遇到了意外的 <code>' . htmlspecialchars($line[$i]) . '</code>。');
                        if ($node->optr != null) // 此时不应存在运算符
                            throw new Exception('解析时遇到了意外的 <code>' . htmlspecialchars($line[$i]) . '</code>。');
                        $node->name($reading); // 设置 name
                        $reading = $line[$i];
                        $optr = true; // 设置运算符标记为 true
                    } elseif ($optr && strpos($optr_flag, $line[$i]) === false) { // 运算符标记为 true 时读到不能构成运算符的字符（处理数字情况）
                        $optr = false;
                        $node->optr($reading);
                        $reading = $line[$i]; // 直接写入读入串
                    } else { // 其余情况，全部写入读入串等待处理
                        $reading .= $line[$i];
                    }
                } else { // expect 不为空，忽略除 expect 外的所有 Token
                    if ($backslash) { // 反斜线转义，这个字符无论是什么都不处理
                        $backslash = false; // 复位反斜线标记
                        $reading .= $line[$i];
                    } else {
                        if ($line[$i] == '\\') { // 读到反斜线
                            $backslash = true; // 设置反斜线标记为 true
                            $reading .= $line[$i];
                        } elseif ($line[$i] == $expect) { // 读到 expect
                            $node->set($reading); // 将读入串内容写入节点
                            $reading = '';
                            if ($expect == '/') { // 处理正则字面量模式修饰符
                                while ($i < $len - 1 && strpos($modifiers, $line[$i + 1]) !== false) { // 只要后面是一个修饰符就不断预读直到行末
                                    $i++;
                                    $reading .= $line[$i];
                                }
                                $node->modifier = $reading; // 设置修饰符
                                if (!Regex::regexCompileTest($node->toString())) throw new Exception('正则表达式编译失败。');
                                $reading = '';
                            }
                            $expect = ''; // 复位 expect
                        } else { // 其余情况，全部写入读入串等待处理
                            $reading .= $line[$i];
                        }
                    }
                }
            }
        }
        if (!$node instanceof Root) throw new Exception('解析时遇到了意外的 <code>EOF</code>。'); // 当前节点不是根节点，结构不完整
        if ($reading != '') throw new Exception('解析时遇到了无法处理的 <code>' . htmlspecialchars($reading) . '</code>。'); // 读入串不为空，非法
        $this->_ast = $ast;
        return $this;
    }

    /**
     * 调试信息
     * 
     * @access public
     * @return array
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
        if (!function_exists('\CommentRuleset\export_dfs')) {
            function export_dfs($node, $translator) { // 内部定义递归函数
                if (!$node instanceof ASTNode) return ''; // 跳过非 ASTNode 节点
                if (!$translator->enterNode($node)) return ''; // Translator::enterNode() 钩子
                $result = $translator->nodeStartToken($node); // Translator::nodeStartToken() 钩子
                if ($node instanceof Root) { // 处理需要递归调用的情况
                    $result .= export_dfs($node->judge, $translator);
                } elseif ($node instanceof Judge) {
                    $result .= export_dfs($node->then, $translator);
                    $result .= export_dfs($node->else, $translator);
                }
                $result .= $translator->nodeEndToken($node); // Translator::nodeEndToken() 钩子
                if (!$translator->leaveNode($node)) return ''; // Translator::leaveNode() 钩子
                return $result;
            }
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
     * @var \CommentRuleset\Value
     */
    public $target;

    /**
     * `:`
     * 
     * @access public
     * @var mixed
     */
    public $then;

    /**
     * `!`
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
    const SIGNALS = array('accept', 'review', 'spam', 'deny', 'skip');

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
        $this->type = 'sqtext';
        $this->value = $text;
    }

    /**
     * 显式转为字符串
     * 
     * @access public
     * @return string 两侧没有引号且反转义后的文本内容
     */
    public function toString() {
        return str_replace(array('\\\\', '\\\''), array('\\', '\''), $this->value);
    }

    /**
     * 显式转为字符串
     * 
     * @access public
     * @return string 两侧有引号且转义后的文本内容
     */
    public function __toString() {
        return "'{$this->value}'";
    }
}

/**
 * DoubleQuotedText Node
 */
class DoubleQuotedText extends Value {
    public function set($text) {
        $this->type = 'dqtext';
        $this->value = $text;
    }

    /**
     * 显式转为字符串
     * 
     * @access public
     * @return string 两侧没有引号且反转义后的文本内容
     */
    public function toString() {
        return stripcslashes($this->value);
    }

    /**
     * 显式转为字符串
     * 
     * @access public
     * @return string 两侧有引号且转义后的文本内容
     */
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
        if (self::regexCompileTest("/$regex/")) {
            $this->type = 'regex';
            $this->value = $regex;
        } else throw new Exception('正则表达式编译失败。');
    }

    /**
     * 正则表达式编译测试
     * 
     * @access public
     * @param string $regex 正则表达式
     * @return bool
     */
    public static function regexCompileTest($regex) {
        return @preg_match($regex, '') !== false;
    }

    /**
     * 显式转为字符串
     * 
     * @access public
     * @return string 两侧没有引号也未被转义的正则表达式
     */
    public function toString() {
        return "/{$this->value}/{$this->modifier}";
    }

    /**
     * 隐式转为字符串 魔术方法
     * 
     * @access public
     * @return string 两侧有引号且转义后的正则表达式
     */
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
 * Rule 翻译器
 */
class RuleTranslator extends Translator {
    public function nodeStartToken($node) {
        $result = '';
        if ($node->parent instanceof Judge) { // 对于上级节点是 Judge 的情况，需要添加前缀
            if ($node === $node->parent->then) $result .= ' : ';
            elseif ($node === $node->parent->else) $result .= ' ! ';
        }
        if ($node instanceof Judge) {
            if ($node->target instanceof Regex) $target = $node->target->toString(); // 此时需要一个不在引号内的正则
            else $target = strval($node->target); // 避免对象引用
            $result .= "[ {$node->name} {$node->optr} {$target} ]"; // 组装条件块
        } elseif ($node instanceof Value) {
            $result .= $node; // Value::__toString() 方法将会隐式调用
        }
        return $result;
    }

    public function nodeEndToken($node) {
        if ($node instanceof Judge) return ' ;'; // Judge 的结束标记
        return '';
    }
}

/**
 * PHP 翻译器
 */
class PhpTranslator extends Translator {
    public function nodeStartToken($node) {
        $result = '';
        if ($node->parent instanceof Judge && $node === $node->parent->else) $result .= ' } else { ';
        if ($node instanceof Root) {
            $result .= "<?php\nif (!defined('__TYPECHO_ROOT_DIR__')) exit;\n"; // 文件头
        } elseif ($node instanceof Judge) {
            $result .= 'if (';
            $origin_text = false;
            if ($node->target instanceof DoubleQuotedText) { // 处理双引号中可能有的未转义的 `$`
                $origin_text = $node->target->value; // 临时保存原文本以便稍后复原
                $new_text = '';
                $len = strlen($origin_text);
                $backslash = false;
                for ($i = 0; $i < $len; $new_text .= $origin_text[$i], $i++) { // 魔法
                    if ($backslash) {
                        $backslash = false;
                        continue;
                    }
                    if ($origin_text[$i] == '\\') $backslash = true;
                    elseif ($origin_text[$i] == '$') $new_text .= '\\'; // 没有与 `$` 搭配的反斜线，补充一个
                }
                $node->target->value = $new_text;
            }
            if ($node->optr == '<-') { // 不是 PHP 运算符，需要详细翻译
                if (!$node->target instanceof Value || $node->target instanceof Regex)
                    throw new Exception('<code>&lt;-</code> 运算符的右侧应当为文本。');
                $result .= "stripos(\$params['{$node->name}'], {$node->target}) !== false"; // 目前版本默认忽略大小写
            } elseif ($node->optr == '~') { // 同上
                if (!$node->target instanceof Regex)
                    throw new Exception('<code>~</code> 运算符只允许与正则字面量搭配使用。');
                $result .= "preg_match({$node->target}, \$params['{$node->name}']) === 1";
            } else {
                if ($node->target instanceof Regex)
                    throw new Exception('正则字面量只允许与 <code>~</code> 运算符搭配使用。');
                $result .= "\$params['{$node->name}'] {$node->optr} {$node->target}";
            }
            if ($origin_text !== false) $node->target->value = $origin_text; // 复原双引号原文本
            $result .= ') { ';
        } elseif ($node instanceof Value) {
            if ($node->type == 'signal') {
                $result .= 'return CommentRuleset_Plugin::FLAG_' . strtoupper($node);
            } else {
                $result .= $node;
            }
        }
        return $result;
    }

    public function nodeEndToken($node) {
        if ($node instanceof Root) return "\n";
        elseif ($node instanceof Judge) return ' }';
        return ';';
    }
}

/**
 * JSON 翻译器
 */
class JsonTranslator extends Translator {
    /**
     * JSON 结构数组
     * 
     * @access protected
     * @var array
     */
    protected $json;

    /**
     * 编号计数器
     * 
     * @access protected
     * @var int
     */
    protected $count;

    /**
     * flag 栈
     * 
     * @access protected
     * @var array
     */
    protected $flags;

    public function nodeStartToken($node) {
        // JsonTranslator 的处理方法与 RuleTranslator 和 PhpTranslator 不同
        // 后两者是直接根据对应节点生成翻译文本，而 JsonTranslator 则是生成以数组形式储存的 JSON 结构
        // 相当于把 Rule AST 先翻译为 JSON 结构，再进行序列化
        if ($node instanceof Root) { // 初始化结构与标志量
            $this->json = array();
            $this->count = 0;
            $this->flags = array();
        } elseif ($node instanceof Judge) {
            if ($node->parent instanceof Root) $flag = '#Main';
            else {
                $this->count++; // 分配一个新的编号
                $flag = "#{$this->count}";
                $parent_flag = end($this->flags); // 从栈中取出父节点 flag
                if ($node === $node->parent->then) $this->json[$parent_flag]['then'] = $flag;
                elseif ($node === $node->parent->else) $this->json[$parent_flag]['else'] = $flag;
            }
            if ($node->target->type == 'number') $target = $node->target->value; // 不进行类型转换以保证其在生成的 JSON 中不被转为文本
            else $target = $node->target->toString();
            $judge = array(
                'flag' => $flag,
                'name' => $node->name,
                'optr' => strval($node->optr),
                'target' => $target,
                'parent' => $flag == '#Main' ? null : $parent_flag,
                'then' => null, // 此时不设置子节点，而是由子节点设置父节点
                'else' => null,
            );
            array_push($this->flags, $flag); // 当前 flag 入栈，便于子节点查找父节点
            $this->json[$flag] = $judge; // 维护 JSON 结构数组
        } elseif ($node instanceof Value) {
            $parent_flag = end($this->flags);
            if ($node === $node->parent->then) $this->json[$parent_flag]['then'] = strval($node);
            elseif ($node === $node->parent->else) $this->json[$parent_flag]['else'] = strval($node);
        }
        return '';
    }

    public function nodeEndToken($node) {
        if ($node instanceof Root) return json_encode($this->json); // 此时，结构转换已经全部完成，将其序列化后返回
        if ($node instanceof Judge) array_pop($this->flags);
        return '';
    }
}
