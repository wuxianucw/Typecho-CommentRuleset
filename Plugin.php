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
    public static function config(Typecho_Widget_Helper_Form $form)
    {
        $panelUrl = Helper::url('CommentRuleset/control-panel.php');
        /** 拒绝评论错误提示 */
        $form->addInput(new Typecho_Widget_Helper_Form_Element_Text('errmsg', NULL, '根据相关设置，该评论被拒绝。', '拒绝评论错误提示', _t(<<<HTML
            本参数用于设置拒绝评论时的提示信息，该信息是否展示取决于您当前使用的模板。<br>
            如果您希望配置评论规则集，请移步<a href="{$panelUrl}" target="_blank">「控制台 -> 评论规则集」</a>。
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
