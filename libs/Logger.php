<?php
namespace CommentRuleset;
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
/**
 * Typecho 评论规则集插件 日志记录器
 * 
 * @package CommentRuleset
 * @author wuxianucw
 * @license GNU Affero General Public License v3.0
 * @link https://ucw.moe/
 */
class Logger {
    /**
     * 日志等级常量
     * 
     * @var int
     */
    const VERBOSE = 0, DEBUG = 1, INFO = 2, WARNING = 3, ERROR = 4;

    /**
     * 日志等级
     * 
     * @access protected
     * @var int
     */
    protected $level;

    /**
     * 日志文件
     * 
     * @access protected
     * @var resource
     */
    protected $file;

    /**
     * 构造日志记录器对象
     * 
     * @access public
     * @param int $level 日志等级 默认为 `Logger::INFO`
     * @param string $file 日志文件路径 如果为空则默认为 runtime 目录下以当前日期命名的 .log 文件
     * @return void
     * @throws \Exception
     */
    function __construct($level = self::INFO, $file = '') {
        $this->level = $level;
        if (!$file) $file = dirname(__DIR__) . '/runtime/' . date('Ymd') . '.log';
        $this->file = fopen($file, 'a');
        if (!$this->file) throw new \Exception('Could not open log file.');
    }

    /**
     * 销毁日志记录器对象
     * 
     * @access public
     * @return void
     */
    function __destruct() {
        if ($this->file) fclose($this->file);
    }

    /**
     * 记录一个 `verbose` 级别的日志
     * 
     * @access public
     * @param string $text
     * @return void
     */
    public function verbose($text) {
        if ($this->level > self::VERBOSE) return;
        fprintf($this->file, "[%s][V] %s\n", date('Y-m-d H:i:s'), $text);
    }

    /**
     * 记录一个 `debug` 级别的日志
     * 
     * @access public
     * @param string $text
     * @return void
     */
    public function debug($text) {
        if ($this->level > self::DEBUG) return;
        fprintf($this->file, "[%s][D] %s\n", date('Y-m-d H:i:s'), $text);
    }

    /**
     * 记录一个 `info` 级别的日志
     * 
     * @access public
     * @param string $text
     * @return void
     */
    public function info($text) {
        if ($this->level > self::INFO) return;
        fprintf($this->file, "[%s][I] %s\n", date('Y-m-d H:i:s'), $text);
    }

    /**
     * 记录一个 `warning` 级别的日志
     * 
     * @access public
     * @param string $text
     * @return void
     */
    public function warning($text) {
        if ($this->level > self::WARNING) return;
        fprintf($this->file, "[%s][W] %s\n", date('Y-m-d H:i:s'), $text);
    }

    /**
     * 记录一个 `error` 级别的日志
     * 
     * @access public
     * @param string $text
     * @return void
     */
    public function error($text) {
        if ($this->level > self::ERROR) return;
        fprintf($this->file, "[%s][E] %s\n", date('Y-m-d H:i:s'), $text);
    }
}

/**
 * 屏蔽日志
 */
class NoLog extends Logger {
    function __construct() {}
    function __destruct() {}
    public function verbose($text) {}
    public function debug($text) {}
    public function info($text) {}
    public function warning($text) {}
    public function error($text) {}
}
