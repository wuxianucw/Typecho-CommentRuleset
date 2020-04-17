<?php
if(!defined('__TYPECHO_ROOT_DIR__')) exit;
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
    const FLAG_ACCEPT = 0;
    
    /**
     * 评论待审核标志
     * 
     * @var int
     */
    const FLAG_REVIEW = 1;

    /**
     * 评论垃圾标志
     * 
     * @var int
     */
    const FLAG_SPAM = 2;

    /**
     * 评论禁止标志
     * 
     * @var int
     */
    const FLAG_DENY = 3;

    /**
     * 无操作标志
     * 
     * @var int
     */
    const FLAG_SKIP = 4;

    /**
     * 激活插件
     * 
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function activate() {
        if(!is_dir(dirname(__FILE__) . '/runtime')) mkdir(dirname(__FILE__) . '/runtime');
        if(!file_exists(dirname(__FILE__) . '/runtime/ruleset.php')) touch(dirname(__FILE__) . '/runtime/ruleset.php');
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
        /** MDUI 加载来源 */
        $form->addInput(new Typecho_Widget_Helper_Form_Element_Select('mdui', array(0 => '本地', 1 => 'jsDelivr'), 0, 'MDUI 加载来源', _t(<<<HTML
            本参数用于设置评论规则集管理页面使用到的 MDUI 相关资源的加载来源，目前提供本地源和 jsDelivr 两个选项。<br>
            如果评论规则集页面加载缓慢，可以尝试更改此项目。<br>
            如果您不知道此项目的用途，保持默认即可。（反正也不影响规则集配置）<br>
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
     * 获取 MDUI 加载来源 结尾不含 "/"
     * 
     * @access public
     * @param bool $output
     * @return string
     */
    public static function mdui($output = true) {
        static $result = "";
        if(!empty($result)) {
            if($output) echo $result;
            return $result;
        }
        $mdui = 0;
        try {
            $mdui = Helper::options()->plugin('CommentRuleset')->mdui;
        } catch(Typecho_Plugin_Exception $e) {}
        $mdui = intval($mdui);
        $dist = array(
            0 => Helper::options()->rootUrl . '/usr/plugins/CommentRuleset/mdui',
            1 => 'https://cdn.jsdelivr.net/npm/mdui@0.4.3/dist',
        );
        if($mdui < 0 || $mdui >= count($dist)) $mdui = 0;
        $result = $dist[$mdui];
        if($output) echo $result;
        return $result;
    }

    /**
     * 读取规则集
     * 
     * @access public
     * @return array
     */
    public static function getRuleset() {
        require dirname(__FILE__) . '/runtime/ruleset.php';
        if(!isset($ruleset)) return array();
        $ruleset = unserialize($ruleset);
        if(!is_array($ruleset)) return array();
        return $ruleset;
    }

    /**
     * 保存规则集
     * 
     * @access public
     * @param array $ruleset
     * @return void
     */
    public static function saveRuleset($ruleset) {
        file_put_contents(dirname(__FILE__) . '/runtime/ruleset.php',
            "<?php\nif(!defined('__TYPECHO_ROOT_DIR__')) exit;\n\$ruleset = '"
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
        // touch(dirname(__FILE__) . '/FLAG'); // 临时：帮助我们更好地了解触发规律
        return $comment;
    }
}
