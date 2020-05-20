<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
/**
 * Typecho 评论规则集插件 Action
 * 
 * @package CommentRuleset
 * @author wuxianucw
 * @license GNU Affero General Public License v3.0
 * @link https://ucw.moe/
 */
class CommentRuleset_Action extends Typecho_Widget implements Widget_Interface_Do {
    /**
     * Typecho Options
     * 
     * @access private
     * @var stdClass
     */
    private $options;

    /**
     * Action 路由
     * 
     * @access public
     * @return void
     */
    public function action() {
        Typecho_Widget::widget('Widget_User')->pass('administrator');
        $this->options = Helper::options();
        $this->response->redirect($this->options->adminUrl);
    }
}
