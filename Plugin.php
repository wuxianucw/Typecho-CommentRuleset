<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
/**
 * Typecho 评论规则集插件
 * 
 * @package CommentRuleset
 * @author wuxianucw
 * @version 1.0.0
 * @license GNU Affero General Public License v3.0
 * @link https://ucw.moe/
 */
class CommentRuleset_Plugin implements Typecho_Plugin_Interface {
    /**
     * 评论通过标志
     * 
     * @var int
     */
    const FLAG_ACCEPT = 0x00;
    
    /**
     * 评论待审核标志
     * 
     * @var int
     */
    const FLAG_REVIEW = 0x0c;

    /**
     * 评论垃圾标志
     * 
     * @var int
     */
    const FLAG_SPAM = 0x0d;

    /**
     * 评论禁止标志
     * 
     * @var int
     */
    const FLAG_DENY = 0x0b;

    /**
     * 无操作标志
     * 
     * @var int
     */
    const FLAG_SKIP = 0x01;

    /**
     * 激活插件
     * 
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function activate() {
        if (!file_exists(__DIR__ . '/control-panel.php'))
            throw new Typecho_Plugin_Exception('插件文件不完整，请检查插件完整性');
        if (!is_dir(__DIR__ . '/runtime')) mkdir(__DIR__ . '/runtime');
        if (!file_exists(__DIR__ . '/runtime/ruleset.php')) touch(__DIR__ . '/runtime/ruleset.php');
        Typecho_Plugin::factory('Widget_Feedback')->comment = array('CommentRuleset_Plugin', 'render');
        Helper::addPanel(1, 'CommentRuleset/control-panel.php', '评论规则集', '评论规则集管理', 'administrator');
        Helper::addAction('manage-commentruleset', 'CommentRuleset_Action');
        return _t('插件启用成功，请配置评论规则集。');
    }

    /**
     * 禁用插件
     * 
     * @static
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function deactivate() {
        Helper::removePanel(1, 'CommentRuleset/control-panel.php');
        Helper::removeAction('manage-commentruleset');
        return _t('插件已禁用，已保存的规则集自动失效但并未删除。');
    }

    /**
     * 获取插件配置面板
     * 
     * @access public
     * @param Typecho_Widget_Helper_Form $form 配置面板
     * @return void
     */
    public static function config(Typecho_Widget_Helper_Form $form) {
        require_once __DIR__ . '/libs/Logger.php';
        $panelUrl = Helper::url('CommentRuleset/control-panel.php');

        /** 拒绝评论错误提示 */
        $form->addInput(new Typecho_Widget_Helper_Form_Element_Text('errmsg', null, '根据相关设置，该评论被拒绝。', _t('拒绝评论错误提示'), _t(<<<HTML
            本参数用于设置拒绝评论时的提示信息，该信息是否展示取决于您当前使用的模板。<br>
            如果您希望配置评论规则集，请移步<a href="{$panelUrl}" target="_blank">「控制台 -> 评论规则集」</a>。
HTML
        )));

        /** 日志级别 */
        $logLevel = new Typecho_Widget_Helper_Form_Element_Select('logLevel', array(
            -1 => '禁用日志',
            CommentRuleset\Logger::ERROR => '仅 ERROR',
            CommentRuleset\Logger::WARNING => 'WARNING 及以上',
            CommentRuleset\Logger::INFO => 'INFO 及以上',
            CommentRuleset\Logger::DEBUG => 'DEBUG 及以上',
            CommentRuleset\Logger::VERBOSE => 'VERBOSE 及以上',
        ), CommentRuleset\Logger::ERROR, _t('日志级别'), _t(<<<HTML
            本参数用于设置插件记录的日志级别，默认仅记录 ERROR。<br>
            日志级别由高到低：ERROR > WARNING > INFO > DEBUG > VERBOSE。若禁用日志，将不会记录任何内容。
HTML
        ));
        $logLevel->addRule('enum', _t('必须选择合法的日志级别'), array(-1, CommentRuleset\Logger::ERROR, CommentRuleset\Logger::WARNING,
            CommentRuleset\Logger::INFO, CommentRuleset\Logger::DEBUG, CommentRuleset\Logger::VERBOSE));
        $form->addInput($logLevel);

        /** Monaco 加载来源 */
        $monaco = new Typecho_Widget_Helper_Form_Element_Select('monaco', array(
            0 => 'jsDelivr',
            1 => 'BootCDN',
            -1 => '自定义',
        ), 0, _t('Monaco 加载来源'), _t(<<<HTML
            本参数用于设置规则文本编辑模式用到的 Monaco 编辑器的加载来源，默认为 jsDelivr。<br>
            如果您希望自定义加载来源，请在本参数选择自定义后再在下方“自定义 Monaco 加载来源”中填写。所需要的 Monaco 版本为 0.20.0 及以上。
HTML
        ));
        $monaco->addRule('enum', _t('必须正确设置 Monaco 加载来源'), array(0, 1, -1));
        $form->addInput($monaco);

        /** 自定义 Monaco 加载来源 */
        $form->addInput(new Typecho_Widget_Helper_Form_Element_Text('customizedMonacoUrl', null, '', _t('自定义 Monaco 加载来源'), _t(<<<HTML
            本参数用于自定义 Monaco 的加载来源，在使用本参数前，请先将“Monaco 加载来源”设置为“自定义”。<br>
            您自定义的 Monaco 来源 URL 应该指向 Monaco 的 <code>min/vs</code> 目录，形如：<code>https://cdn.jsdelivr.net/npm/monaco-editor@0.22.3/min/vs</code>。
HTML
        )));
    }

    /**
     * 个人用户的配置面板
     * 
     * @access public
     * @param Typecho_Widget_Helper_Form $form
     * @return void
     */
    public static function personalConfig(Typecho_Widget_Helper_Form $form) {}

    /**
     * 读取规则集
     * 
     * @access public
     * @return array
     */
    public static function getRuleset() {
        require __DIR__ . '/runtime/ruleset.php';
        if (!isset($ruleset)) return array();
        $ruleset = unserialize($ruleset);
        if (!is_array($ruleset)) return array();
        return $ruleset;
    }

    /**
     * 保存规则集
     * 
     * @access public
     * @param array $ruleset
     * @return int|false
     */
    public static function saveRuleset($ruleset) {
        return file_put_contents(__DIR__ . '/runtime/ruleset.php',
            "<?php\nif (!defined('__TYPECHO_ROOT_DIR__')) exit;\n\$ruleset = '"
            . str_replace(array('\\', '\''), array('\\\\', '\\\''), serialize($ruleset)) . "';\n");
    }

    /**
     * 获取 Monaco 加载来源
     * 
     * @access public
     * @return string
     */
    public static function getMonacoUrl() {
        $options = Helper::options()->plugin('CommentRuleset');
        $id = isset($options->monaco) ? intval($options->monaco) : 0;
        if ($id == -1) return isset($options->customizedMonacoUrl) ? $options->customizedMonacoUrl : '';
        if ($id < 0 || $id > 1) $id = 0; // range safe
        static $cdn = array(
            0 => 'https://cdn.jsdelivr.net/npm/monaco-editor@0.22.3/min/vs',
            1 => 'https://cdn.bootcdn.net/ajax/libs/monaco-editor/0.21.2/min/vs'
        );
        return $cdn[$id];
    }

    /**
     * 评论过滤
     * 
     * @access public
     * @param array $comment
     * @param mixed $content
     * @return array
     */
    public static function render($comment, $content) {
        static $statusMap = array(
            self::FLAG_ACCEPT => 'approved',
            self::FLAG_REVIEW => 'waiting',
            self::FLAG_SPAM => 'spam',
        );
        $config = Helper::options()->plugin('CommentRuleset');
        $params = array(
            'uid' => empty($comment['authorId']) ? 0 : $comment['authorId'],
            'nick' => $comment['author'],
            'email' => $comment['mail'],
            'url' => $comment['url'],
            'content' => $comment['text'],
            'length' => mb_strlen(preg_replace('/\\s/', '', $comment['text'])),
            'ip' => $comment['ip'],
            'ua' => $comment['agent'],
        );
        $ruleset = self::getRuleset();
        $result = self::FLAG_SKIP;
        foreach ($ruleset as $rule) {
            if (in_array('on', $rule['status'])) {
                $result = self::applyRule($rule['filename'], $params);
                if ($result !== self::FLAG_SKIP) break;
            }
        }
        if ($result === self::FLAG_DENY) {
            Typecho_Cookie::set('__typecho_remember_text', $comment['text']);
            throw new Typecho_Widget_Exception(isset($config->errmsg) ? $config->errmsg : '根据相关设置，该评论被拒绝。');
        } elseif ($result !== self::FLAG_SKIP) {
            $comment['status'] = $statusMap[$result];
        }
        return $comment;
    }

    /**
     * 用指定规则检测评论
     * 
     * @access protected
     * @param string $rule_filename
     * @param array $params
     * @return int
     */
    protected static function applyRule($rule_filename, $params) {
        // $params 将在规则文件中被使用
        $result = include __DIR__ . '/runtime/' . $rule_filename;
        if ($result === false) {
            $result = self::FLAG_SKIP;
            throw new Exception('CommentRuleset: include failed.');
        }
        return $result;
    }
}
