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
        //Typecho_Widget::widget('Widget_User')->pass('administrator');
        Helper::options()->to($this->options);
        $this->on($this->request->is('a=ruleDetails'))->ruleDetails();
        $this->on($this->request->is('a=translate'))->translate();
        $this->on($this->request->is('a=saveRule'))->saveRule();
        $this->response->redirect($this->options->adminUrl);
    }

    /**
     * 规则详情接口
     * 
     * GET 方式请求 `apiBase?a=ruleDetails`  
     * GET 参数 `ruid` 欲查询规则的 RUID  
     * 返回状态码 `200` 格式 JSON 规则详细信息  
     * 返回状态码 `404` 空文本 规则不存在
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
     * POST 方式请求 `apiBase?a=translate`  
     * FormData 参数 `input` 规则文本  
     * 返回格式 JSON  
     * 返回状态码 `200` 翻译结果结构  
     * 返回状态码 `201` 编译失败错误信息  
     * 返回状态码 `204` 空文本 无需翻译
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

    /**
     * 保存规则接口
     * 
     * POST 方式请求 `apiBase?a=saveRule`  
     * FormData 参数 `ruid` 可选 规则 RUID 为空表示新增  
     * FormData 参数 `name` 规则名称  
     * FormData 参数 `status` 规则状态  
     * FormData 参数 `remark` 可选 规则备注  
     * FormData 参数 `priority` 规则优先级  
     * FormData 参数 `rule` 规则内容  
     * FormData 参数 `editMode` 规则编辑模式  
     * 返回状态码 `200` 格式 JSON 保存成功，包括自动调整后目前的状态以及可能的编译错误信息，还有整个规则列表  
     * 返回状态码 `201` 空文本 保存失败（未知原因）  
     * 返回状态码 `403` 空文本 参数非法
     */
    public function saveRule() {
        if ($this->request->isPost()) {
            $this->response->setContentType('application/json');
            $ruid = $this->request->get('ruid');
            $name = $this->request->get('name');
            $status = $this->request->get('status');
            $remark = $this->request->get('remark', '');
            $priority = intval($this->request->get('priority'));
            $rule = $this->request->get('rule');
            $editMode = $this->request->get('editMode', 0);
            if (!$name || !$status || $priority < 1 || $priority > 99999 || !$rule) {
                $this->response->setStatus(403);
                exit;
            }
            $ruleset = CommentRuleset_Plugin::getRuleset();
            if ($ruid) {
                if (!in_array($ruid, array_keys($ruleset))) {
                    $this->response->setStatus(403);
                    exit;
                }
                if (!empty($ruleset[$ruid]['filename']) && file_exists(__DIR__ . '/runtime/' . $ruleset[$ruid]['filename']))
                    unlink(__DIR__ . '/runtime/' . $ruleset[$ruid]['filename']);
            } else {
                $ruid = '';
                for ($i = 0; $i < 6; $i++) $ruid .= dechex(mt_rand(0, 15));
            }
            $ruleset[$ruid] = array(
                'name' => $name,
                'status' => $status,
                'remark' => $remark,
                'priority' => $priority,
                'editMode' => $editMode,
                'filename' => '',
            );
            $return_json = array('code' => 200);
            if (in_array('on', $status) || !in_array('uncompiled', $status)) {
                require_once __DIR__ . '/libs/RuleCompiler.php';
                try {
                    $compile_result = (new \CommentRuleset\RuleCompiler())->parse($rule)->export(new \CommentRuleset\PhpTranslator());
                    $filename = sha1('rule_' . $ruid . time()) . '.php';
                    if (file_put_contents(__DIR__ . '/runtime/' . $filename, $compile_result) === false) {
                        $this->response->setStatus(201);
                        exit;
                    }
                    $ruleset[$ruid]['filename'] = $filename;
                } catch (\CommentRuleset\Exception $e) {
                    $return_json['code'] = 201;
                    $return_json['message'] = $e->getMessage();
                    $ruleset[$ruid]['status'] = array('off', 'uncompiled');
                }
            }
            uasort($ruleset, function($a, $b) { return $a['priority'] - $b['priority']; });
            if (!CommentRuleset_Plugin::saveRuleset($ruleset)) {
                $this->response->setStatus(201);
                exit;
            }
            $return_json['ruleset'] = $ruleset;
            echo json_encode($return_json);
            exit;
        }
    }
}
