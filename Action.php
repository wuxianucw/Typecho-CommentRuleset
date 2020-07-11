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
        Helper::options()->to($this->options);
        $this->on($this->request->is('a=ruleDetails'))->ruleDetails();
        $this->on($this->request->is('a=translate'))->translate();
        $this->response->redirect($this->options->adminUrl);
    }

    /**
     * 规则详情接口
     * 
     * @access public
     * @return void
     */
    public function ruleDetails() {
        if ($this->request->isGet()) {
            $this->response->setContentType('application/json');
            $ruid = $this->request->get('ruid', '');
            if (strlen($ruid) != 6) {
                $this->response->setStatus(404);
                exit;
            }
            $ruleset = CommentRuleset_Plugin::getRuleset();
            foreach ($ruleset as $rule) {
                if ($ruid === $rule['ruid']) {
                    echo json_encode($rule);
                    exit;
                }
            }
            $this->response->setStatus(404);
            exit;
        }
    }

    /**
     * 规则翻译接口
     * 
     * @access public
     * @return void
     */
    public function translate() {
        if ($this->request->isPost()) {
            $this->response->setContentType('application/json');
            $input = $this->request->get('input', '');
            if ($input == '') {
                $this->response->setStatus(204);
                exit;
            }
            require_once __DIR__ . '/libs/RuleCompiler.php';
            try {
                echo (new \CommentRuleset\RuleCompiler())->parse($input)->export(new \CommentRuleset\JsonTranslator());
            } catch (\CommentRuleset\Exception $e) {
                $this->response->setStatus(201);
                echo json_encode(array('result' => $e->getMessage()));
            }
            exit;
        }
    }
}
