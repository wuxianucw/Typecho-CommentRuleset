<?php
if (!defined('__TYPECHO_ADMIN__')) exit;
require_once dirname(__FILE__) . '/libs/MenuOutputer.php';
Typecho_Widget::widget('CommentRuleset_MenuOutputer')->to($menuOutputer);
$ruleset = CommentRuleset_Plugin::getRuleset();
?>
<!DOCTYPE HTML>
<html>
    <head>
        <meta charset="<?php $options->charset(); ?>">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="renderer" content="webkit">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?php _e('%s - %s - Powered by Typecho', $menu->title, $options->title); ?></title>
        <meta name="robots" content="noindex, nofollow">
        <link rel="stylesheet" href="<?php CommentRuleset_Plugin::mdui() ?>/css/mdui.min.css?v=0.4.3">
        <style>
            .mdui-textfield-helper>a:before {
                -webkit-transform: scaleX(0)!important;
                transform: scaleX(0)!important;
            }
            .judge-block {
                margin-top: 0.5em;
            }
            .judge-block-label {
                box-sizing: border-box;
                width: 100%;
                line-height: 1;
                padding: 9px 14px;
                color: rgba(0, 0, 0, 0.38);
                font-weight: 700;
                font-size: 12px;
                border: 1px solid rgba(0, 0, 0, 0.12);
                border-radius: 3px 3px 0 0;
                -webkit-user-select: none;
                -moz-user-select: none;
                -ms-user-select: none;
                user-select: none;
            }
            .judge-block-label-highlight {
                background: #fffdd1;
            }
            .judge-block-content {
                border: 1px solid rgba(0, 0, 0, 0.12);
                border-top: none;
                border-radius: 0 0 3px 3px;
                padding: 14px;
            }
            .judge-block-content>div:nth-child(2) {
                margin-top: 10px;
            }
            .judge-target textarea, #text-mode textarea {
                font-family: Consolas,Courier,'Courier New',monospace;
                cursor: text!important;
            }
            .judge-target-value {
                display: inline-block;
                padding: 0;
                vertical-align: middle;
            }
            .judge-back, .judge-then-pos, .judge-else-pos {
                text-transform: none!important;
            }
        </style>
    </head>
    <body class="mdui-drawer-body-left mdui-appbar-with-toolbar mdui-theme-primary-red mdui-theme-accent-blue">
        <header class="mdui-appbar mdui-appbar-fixed">
            <div class="mdui-toolbar mdui-color-theme">
                <span class="mdui-btn mdui-btn-icon mdui-ripple mdui-ripple-white" mdui-drawer="{target: '#main-drawer', swipe: true}"><i class="mdui-icon material-icons">menu</i></span>
                <span class="mdui-typo-title"><?php $menu->title() ?></span>
                <div class="mdui-toolbar-spacer"></div>
                <a class="mdui-btn mdui-btn-icon mdui-ripple mdui-ripple-white" href="<?php $options->adminUrl('profile.php'); ?>" mdui-tooltip="{content: '<?php
                    if ($user->logged > 0) {
                        $logged = new Typecho_Date($user->logged);
                        _e('最后登录: %s', $logged->word());
                    }
                    ?>'}"><i class="mdui-icon material-icons">account_circle</i></a>
                <a class="mdui-btn mdui-btn-icon mdui-ripple mdui-ripple-white" href="<?php $options->logoutUrl(); ?>" mdui-tooltip="{content: '登出'}"><i class="mdui-icon material-icons">exit_to_app</i></a>
                <a href="https://github.com/wuxianucw/Typecho-CommentRuleset" target="_blank" class="mdui-btn mdui-btn-icon mdui-ripple mdui-ripple-white" mdui-tooltip="{content: 'GitHub'}">
                    <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 36 36" enable-background="new 0 0 36 36" xml:space="preserve" class="mdui-icon" style="width: 24px;height:24px;">
                        <path fill-rule="evenodd" clip-rule="evenodd" fill="#ffffff" d="M18,1.4C9,1.4,1.7,8.7,1.7,17.7c0,7.2,4.7,13.3,11.1,15.5
                        c0.8,0.1,1.1-0.4,1.1-0.8c0-0.4,0-1.4,0-2.8c-4.5,1-5.5-2.2-5.5-2.2c-0.7-1.9-1.8-2.4-1.8-2.4c-1.5-1,0.1-1,0.1-1
                        c1.6,0.1,2.5,1.7,2.5,1.7c1.5,2.5,3.8,1.8,4.7,1.4c0.1-1.1,0.6-1.8,1-2.2c-3.6-0.4-7.4-1.8-7.4-8.1c0-1.8,0.6-3.2,1.7-4.4
                        c-0.2-0.4-0.7-2.1,0.2-4.3c0,0,1.4-0.4,4.5,1.7c1.3-0.4,2.7-0.5,4.1-0.5c1.4,0,2.8,0.2,4.1,0.5c3.1-2.1,4.5-1.7,4.5-1.7
                        c0.9,2.2,0.3,3.9,0.2,4.3c1,1.1,1.7,2.6,1.7,4.4c0,6.3-3.8,7.6-7.4,8c0.6,0.5,1.1,1.5,1.1,3c0,2.2,0,3.9,0,4.5
                        c0,0.4,0.3,0.9,1.1,0.8c6.5-2.2,11.1-8.3,11.1-15.5C34.3,8.7,27,1.4,18,1.4z"></path>
                    </svg>
                </a>
            </div>
        </header>
        <div class="mdui-drawer" id="main-drawer">
            <div class="mdui-list" mdui-collapse="{accordion: true}" style="margin-bottom: 76px;">
<?php $menuOutputer->output(); ?>
            </div>
        </div>
        <div class="mdui-container" style="padding-top: 8px;">
            <div class="mdui-tab mdui-tab-full-width" mdui-tab>
                <a href="#view-rules" class="mdui-ripple">规则总览</a>
                <a href="#new-rule" class="mdui-ripple">新增规则</a>
                <a href="#guide" class="mdui-ripple">配置指南</a>
            </div>
            <div id="view-rules" class="mdui-typo mdui-p-a-2">
                <div class="mdui-card mdui-shadow-0">
                    <div class="mdui-card-header">
                        <div class="mdui-toolbar">
                            <span class="mdui-typo-title"><?php _e('目前共 %d 条规则', count($ruleset)); ?></span>
                            <div class="mdui-toolbar-spacer"></div>
                            <button id="add-rule" class="mdui-btn mdui-btn-icon mdui-btn-raised mdui-ripple" mdui-tooltip="{content: '新增规则'}"><i class="mdui-icon material-icons">add</i></button>
                        </div>
                        <div class="mdui-toolbar mdui-hidden">
                            <span class="mdui-typo-title mdui-text-color-theme-accent">选中 0 条规则</span>
                            <div class="mdui-toolbar-spacer"></div>
                            <span class="mdui-text-color-red-900">选中项目中包含锁定项目</span>
                            <button id="remove-rule" class="mdui-btn mdui-btn-icon mdui-btn-raised mdui-ripple" disabled mdui-tooltip="{content: '删除规则'}"><i class="mdui-icon material-icons">delete</i></button>
                        </div>
                    </div>
                    <div class="mdui-card-content">
                        <div class="mdui-table-fluid">
                            <table class="mdui-table mdui-table-hoverable mdui-table-selectable">
                                <thead>
                                    <tr>
                                        <th>规则名称</th>
                                        <th mdui-tooltip="{content: 'Rule Unique ID，规则唯一标识符'}">RUID</th>
                                        <th>规则备注</th>
                                        <th>规则状态</th>
                                        <th class="mdui-table-col-numeric" mdui-tooltip="{content: '数值越大的规则优先级越高'}">优先级</th>
                                        <th>操作</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>演示规则1</td>
                                        <td><code>000000</code></td>
                                        <td><button class="mdui-btn mdui-ripple" data-content="这是一条演示规则">点击查看</button></td>
                                        <td><i class="mdui-icon material-icons mdui-text-color-green" mdui-tooltip="{content: '生效'}">check</i><i class="mdui-icon material-icons flag-lock" mdui-tooltip="{content: '锁定'}">lock</i></td>
                                        <td>10</td>
                                        <td>
                                            <button class="mdui-btn mdui-btn-icon mdui-ripple action-edit" disabled mdui-tooltip="{content: '编辑'}"><i class="mdui-icon material-icons">edit</i></button>
                                            <button class="mdui-btn mdui-btn-icon mdui-ripple action-unlock" mdui-tooltip="{content: '解除锁定'}"><i class="mdui-icon material-icons">lock_open</i></button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>演示规则2</td>
                                        <td><code>ffffff</code></td>
                                        <td><button class="mdui-btn mdui-ripple" data-content="这是另一条演示规则">点击查看</button></td>
                                        <td><i class="mdui-icon material-icons mdui-text-color-red" mdui-tooltip="{content: '未生效'}">clear</i><i class="mdui-icon material-icons mdui-text-color-amber" mdui-tooltip="{content: '未编译'}">error_outline</i></td>
                                        <td>10</td>
                                        <td>
                                            <button class="mdui-btn mdui-btn-icon mdui-ripple action-edit" mdui-tooltip="{content: '编辑'}"><i class="mdui-icon material-icons">edit</i></button>
                                            <button class="mdui-btn mdui-btn-icon mdui-ripple action-lock" mdui-tooltip="{content: '锁定'}"><i class="mdui-icon material-icons">lock_outline</i></button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div id="new-rule" class="mdui-typo mdui-p-a-2">
                <div>
                    <span class="mdui-typo-caption-opacity">是否生效：</span>
                    <label class="mdui-switch">
                        <input type="checkbox" name="active" checked>
                        <i class="mdui-switch-icon"></i>
                    </label>
                </div>
                <div class="mdui-textfield">
                    <label class="mdui-textfield-label">规则名称</label>
                    <input class="mdui-textfield-input" name="name" type="text" required>
                    <div class="mdui-textfield-error">规则名称不能为空</div>
                </div>
                <div class="mdui-textfield">
                    <label class="mdui-textfield-label">规则备注</label>
                    <textarea class="mdui-textfield-input" name="desc"></textarea>
                    <div class="mdui-textfield-helper">可以为空，允许多行</div>
                </div>
                <div class="mdui-textfield">
                    <label class="mdui-textfield-label">规则优先级</label>
                    <input class="mdui-textfield-input" name="priority" type="text" value="10" pattern="^(0|[1-9][0-9]{0,4})$" required>
                    <div class="mdui-textfield-error">请输入一个 0 到 99999 范围内的整数</div>
                </div>
                <label class="mdui-textfield-label">规则内容</label>
                <button id="mode-change" class="mdui-btn mdui-ripple">切换到规则文本编辑模式</button>
                <div id="graphical-mode">
                    <div class="judge-block" data-flag="#Main">
                        <div class="judge-block-label">#Main</div>
                        <div class="judge-block-content">
                            <div>
                                若&emsp;
                                <select class="mdui-select judge-name" mdui-select>
                                    <option value="uid">UID</option>
                                    <option value="nick">昵称</option>
                                    <option value="email">邮箱</option>
                                    <option value="url">个人主页</option>
                                    <option value="content">评论内容</option>
                                    <option value="length">评论有效长度</option>
                                    <option value="ip">IP</option>
                                    <option value="ua">User-Agent</option>
                                </select>
                                &emsp;
                                <select class="mdui-select judge-optr" mdui-select>
                                    <option value="==">等于</option>
                                    <option value="!=">不等于</option>
                                    <option value="<">小于</option>
                                    <option value=">">大于</option>
                                    <option value="<=">小于或等于</option>
                                    <option value=">=">大于或等于</option>
                                </select>
                                &emsp;
                                <div class="mdui-textfield judge-target judge-target-value">
                                    <input class="mdui-textfield-input" type="number" placeholder="数值" required>
                                </div>
                            </div>
                            <div>
                                则&emsp;
                                <select class="mdui-select judge-then" mdui-select>
                                    <option value="skip">无动作</option>
                                    <option value="accept">通过评论（白名单）</option>
                                    <option value="review">标记为待审核</option>
                                    <option value="spam">标记为垃圾</option>
                                    <option value="deny">拒绝评论</option>
                                    <option value="judge">继续判断</option>
                                </select>
                                <span class="mdui-hidden">&emsp;<i class="mdui-icon material-icons">arrow_downward</i><button class="judge-then-pos mdui-btn mdui-ripple"></button></span>
                                &emsp;否则&emsp;
                                <select class="mdui-select judge-else" mdui-select>
                                    <option value="skip">无动作</option>
                                    <option value="accept">通过评论（白名单）</option>
                                    <option value="review">标记为待审核</option>
                                    <option value="spam">标记为垃圾</option>
                                    <option value="deny">拒绝评论</option>
                                    <option value="judge">继续判断</option>
                                </select>
                                <span class="mdui-hidden">&emsp;<i class="mdui-icon material-icons">arrow_downward</i><button class="judge-else-pos mdui-btn mdui-ripple"></button></span>
                            </div>
                        </div>
                    </div>
                    <div class="judge-addition"></div>
                </div>
                <div id="text-mode" class="mdui-hidden">
                    <div class="mdui-textfield">
                        <textarea class="mdui-textfield-input" rows="15"></textarea>
                        <div class="mdui-textfield-helper">在这里书写规则文本，允许多行，规则语法详见<a href="javascript:void(0)" onclick="mdui.Tab('.mdui-container>.mdui-tab').show(2);">配置指南</a></div>
                    </div>
                </div>
                <div id="new-rule-control" class="mdui-m-t-3">
                    <p class="mdui-typo-caption-opacity status"><i class="mdui-icon material-icons" style="font-size: 13px;font-weight: bold;">adjust</i> <span>未保存</span></p>
                    <button class="mdui-btn mdui-color-theme-accent mdui-ripple">保存规则</button>
                </div>
            </div>
            <div id="guide" class="mdui-typo mdui-p-a-2">
                <h2>教程：如何优雅地使用评论规则集 <small>文档版本：v1.0</small></h2>
                <p>Typecho 评论规则集插件（<code>CommentRuleset</code>，后简称“插件”）是一款灵活高效的评论控制插件，其功能的灵活性使得对其的配置较为复杂。为了一定程度上排除这款插件在使用上的疑难，同时对插件用法作出一定的指导，笔者特地撰写此教程，以便插件强大的功能得到合适的使用。</p>
                <h3>基础玩法 <small>快速入门</small></h3>
                <p>如果您对插件启用、禁用、增删规则等操作比较熟悉，可以跳过这部分内容。（话说能看到此文就说明插件已经成功启用了吧……）</p>
                <p>首先让我们了解一下插件的目录结构：</p>
                <pre><code>CommentRuleset
├───libs
├───mdui
└───runtime</code></pre>
                <p>其中 <code>libs</code> 和 <code>mdui</code> 目录是固有的，运行时不会被修改；<code>runtime</code> 目录是动态生成的，<strong>必须可写且可执行</strong>，将会存放插件的一些必要数据。</p>
                <p>如果您是直接 clone repo 或从 GitHub 上 Download ZIP 得到的插件文件，则可能还有一个 <code>test</code> 目录，其中存放了笔者开发时用于测试的文件，它不是必需的，并且有时还可能带来隐患，笔者建议删除它。</p>
                <p><strong>请注意：在插件处于启用状态时，笔者不建议在任何情况下修改或删除插件目录中的文件。</strong></p>
                <p><code>libs</code> 目录用于存放插件用到的额外 PHP 库。其中 <code>MenuOutputer.php</code> 是笔者在 Typecho 原生后台菜单输出组件的基础上编写的用于适配 MDUI 的菜单输出组件，按照 Typecho 的要求以 <a href="https://www.gnu.org/licenses/old-licenses/gpl-2.0.html" target="_blank">GNU General Public License v2.0</a> 进行许可；<code>RuleCompiler.php</code> 是笔者编写的一个将笔者设计的“规则语言”编译到 PHP 的小型编译器（当然，没有什么特别复杂的结构，只是一个简单的工具库），以 <a href="https://www.gnu.org/licenses/agpl-3.0.en.html" target="_blank">GNU Affero General Public License v3.0</a> 进行许可。</p>
                <p><code>mdui</code> 目录用于存放本地 MDUI 库，该库以 <a href="https://mit-license.org/" target="_blank">MIT License</a> 进行许可。</p>
                <p>下面重点介绍 <code>runtime</code> 目录中的文件组成。</p>
                <p>全新安装的插件在启用前并不存在 <code>runtime</code> 目录，启用插件时，插件将会自动创建这个目录以及目录下的 <code>ruleset.php</code> 规则集索引文件。规则集索引文件用于保存当前情况下的整个规则列表。</p>
                <p>编译规则后，目录下会生成相应的 <code>rule_<span style="padding: 2px;background-color: #e8eaf6;" mdui-tooltip="{content: 'RUID'}">xxxxxx</span>.php</code>；如果存在生效的规则，还会生成 <code>rules.php</code>，用于运行时的快速判断。</p>
                <p>这里第一次出现了 RUID 的概念，它的全称是 Rule Unique ID，即规则唯一标识符，是一个字母全部小写的 6 位十六进制数，采用随机的方法生成，用于标识规则。</p>
                <p><strong>再次强调：绝对不要手动修改或删除 <code>runtime</code> 目录下的任何文件！这样做的后果很可能是灾难性的！</strong></p>
                <div class="mdui-divider-inset"></div>
                <p></p>
                <p>了解一些基本概念后，我们来尝试新增一条规则。</p>
                <h3>进阶指导 <small>这才是常规操作</small></h3>
                <p>这部分可以介绍比较复杂的规则……</p>
                <h3>高级技巧 <small>Let's Speed Up!</small></h3>
                <p>这里介绍 Rule 语法以及编译器相关……</p>
                <h3>疑难解答 <small>好像有哪里不对</small></h3>
                <p>如果您在使用过程中遇到了问题，这部分内容可能会有所帮助。如果您无法在这里找到答案，可以<a href="https://github.com/wuxianucw/Typecho-CommentRuleset/issues" target="_blank">在 GitHub 上提出 issue</a>。</p>
                <h4>常见问题 <small>Q&amp;A</small></h4>
                <p>如标题所述，是一个Q&amp;A内容。</p>
                <h4>编译错误参考 <small>解析时遇到了雾之湖的妖精</small></h4>
                <p>如标题所述，是一个编译错误参考。</p>
            </div>
        </div>
        <div id="loading" class="mdui-dialog mdui-dialog-alert" style="text-align: center;">
            <div class="mdui-dialog-content"><div class="mdui-spinner"></div></div>
        </div>
        <script src="<?php CommentRuleset_Plugin::mdui() ?>/js/mdui.min.js?v=0.4.3"></script>
        <script>
            var $$ = mdui.JQ;
            $$.extend({
                randomFlag() {
                    var res = '#';
                    for (var i = 0; i < 6; i++) res += Math.floor(Math.random() * 16).toString(16);
                    return res.toUpperCase();
                }
            });
            $$.fn.extend({
                removeBlock() {
                    if (this.data('flag') == '') return;
                    var $then = this.find('.judge-then-pos'), $else = this.find('.judge-else-pos');
                    if (!$then.parent().hasClass('mdui-hidden')) $$(`div[data-flag="${$then.text()}"]`).removeBlock();
                    if (!$else.parent().hasClass('mdui-hidden')) $$(`div[data-flag="${$else.text()}"]`).removeBlock();
                    this.remove();
                    return;
                },
                select(value) {
                    this.find(`[value="${value}"]`).prop('selected', true);
                    mdui.Select(this).handleUpdate();
                    return this;
                }
            });
            $$(function() {
                var loading = new mdui.Dialog('#loading', {
                    history: false,
                    modal: true,
                    closeOnEsc: false
                });
                $$('button#add-rule').on('click', function() {
                    mdui.Tab('.mdui-container>.mdui-tab').show(1);
                });
                $$('button#remove-rule').on('click', function() {
                    mdui.confirm('确定要删除这些规则吗？该操作不可逆！', '提示', function() {
                        mdui.alert('当前配置不允许删除！', '错误', function() {}, {confirmText: '确定'});
                    }, function() {}, {confirmText: '确定', cancelText: '取消'});
                })
                $$('button.action-edit').on('click', function() {
                    mdui.alert('当前配置不允许编辑！', '错误', function() {}, {confirmText: '确定'});
                });
                $$('button.action-lock').on('click', function() {
                    var e = this;
                    mdui.confirm('确定要锁定该规则吗？处于锁定状态的规则不能被编辑或删除！', '提示', function() {
                        console.log(e);
                    }, function() {}, {confirmText: '确定', cancelText: '取消'});
                });
                $$('button.action-unlock').on('click', function() {
                    var e = this;
                    mdui.confirm('确定要解除锁定该规则吗？解除锁定后的规则将可以被编辑或删除！', '提示', function() {
                        console.log(e);
                    }, function() {}, {confirmText: '确定', cancelText: '取消'});
                });
                $$('#view-rules .mdui-table tbody>tr>td:nth-child(4)>button').on('click', function() {
                    mdui.dialog({
                        title: '规则备注',
                        content: $$(this).data('content'),
                        buttons: [{text: '关闭'}]
                    });
                });
                $$('#view-rules .mdui-table tr .mdui-checkbox>input[type=checkbox]').on('change', function() {
                    var $normal = $$('#view-rules .mdui-card-header .mdui-toolbar:nth-child(1)');
                    var $accent = $$('#view-rules .mdui-card-header .mdui-toolbar:nth-child(2)');
                    var $selected = $$('#view-rules .mdui-table tbody tr.mdui-table-row-selected');
                    if ($selected.length > 0) {
                        $accent.find('.mdui-typo-title:nth-child(1)').text(`选中 ${$selected.length} 条规则`);
                        if ($selected.find('.flag-lock').length > 0) {
                            $accent.find('#remove-rule').prop('disabled', true);
                            $accent.find('.mdui-text-color-red-900').removeClass('mdui-hidden');
                        } else {
                            $accent.find('#remove-rule').prop('disabled', false);
                            $accent.find('.mdui-text-color-red-900').addClass('mdui-hidden');
                        }
                        $normal.addClass('mdui-hidden');
                        $accent.removeClass('mdui-hidden');
                    } else {
                        $accent.addClass('mdui-hidden');
                        $normal.removeClass('mdui-hidden');
                    }
                });
                $$(document).on('change', '#new-rule .judge-block .judge-name', function() {
                    var $optr, $target;
                    if ($$(this).val() == 'uid' || $$(this).val() == 'length') {
                        $optr = $$(this).parent().find('.judge-optr').html(`
                            <option value="==">等于</option>
                            <option value="!=">不等于</option>
                            <option value="<">小于</option>
                            <option value=">">大于</option>
                            <option value="<=">小于或等于</option>
                            <option value=">=">大于或等于</option>
                        `);
                        $target = $$(this).parent().find('.judge-target').addClass('judge-target-value').html(`
                            <input class="mdui-textfield-input" type="number" placeholder="数值" required>
                        `);
                    } else {
                        $optr = $$(this).parent().find('.judge-optr').html(`
                            <option value="==">等于（全字匹配）</option>
                            <option value="!=">不等于（全字匹配）</option>
                            <option value="<-">包含</option>
                            <option value="~">符合（正则表达式）</option>
                        `);
                        $target = $$(this).parent().find('.judge-target').removeClass('judge-target-value').html(`
                            <textarea class="mdui-textfield-input"></textarea>
                            <div class="mdui-textfield-helper">可以为空，允许多行，详见<a href="javascript:void(0)" onclick="mdui.Tab('.mdui-container>.mdui-tab').show(2);">配置指南</a></div>
                        `);
                    }
                    mdui.Select($optr).handleUpdate();
                    mdui.updateTextFields($target);
                });
                const insertBlock = function(flag, parent) {
                    return $$('#new-rule .judge-addition').append(`
                        <div class="judge-block" data-flag="${flag}">
                            <div class="judge-block-label">${flag}</div>
                            <div class="judge-block-content">
                                <div>
                                    <i class="mdui-icon material-icons">arrow_upward</i>
                                    <button class="judge-back mdui-btn mdui-ripple">${parent}</button>
                                    &emsp;若&emsp;
                                    <select class="mdui-select judge-name" mdui-select>
                                        <option value="uid">UID</option>
                                        <option value="nick">昵称</option>
                                        <option value="email">邮箱</option>
                                        <option value="url">个人主页</option>
                                        <option value="content">评论内容</option>
                                        <option value="length">评论有效长度</option>
                                        <option value="ip">IP</option>
                                        <option value="ua">User-Agent</option>
                                    </select>
                                    &emsp;
                                    <select class="mdui-select judge-optr" mdui-select>
                                        <option value="==">等于</option>
                                        <option value="!=">不等于</option>
                                        <option value="<">小于</option>
                                        <option value=">">大于</option>
                                        <option value="<=">小于或等于</option>
                                        <option value=">=">大于或等于</option>
                                    </select>
                                    &emsp;
                                    <div class="mdui-textfield judge-target judge-target-value">
                                        <input class="mdui-textfield-input" type="number" placeholder="数值" required>
                                    </div>
                                </div>
                                <div>
                                    则&emsp;
                                    <select class="mdui-select judge-then" mdui-select>
                                        <option value="skip">无动作</option>
                                        <option value="accept">通过评论（白名单）</option>
                                        <option value="review">标记为待审核</option>
                                        <option value="spam">标记为垃圾</option>
                                        <option value="deny">拒绝评论</option>
                                        <option value="judge">继续判断</option>
                                    </select>
                                    <span class="mdui-hidden">&emsp;<i class="mdui-icon material-icons">arrow_downward</i><button class="judge-then-pos mdui-btn mdui-ripple"></button></span>
                                    &emsp;否则&emsp;
                                    <select class="mdui-select judge-else" mdui-select>
                                        <option value="skip">无动作</option>
                                        <option value="accept">通过评论（白名单）</option>
                                        <option value="review">标记为待审核</option>
                                        <option value="spam">标记为垃圾</option>
                                        <option value="deny">拒绝评论</option>
                                        <option value="judge">继续判断</option>
                                    </select>
                                    <span class="mdui-hidden">&emsp;<i class="mdui-icon material-icons">arrow_downward</i><button class="judge-else-pos mdui-btn mdui-ripple"></button></span>
                                </div>
                            </div>
                        </div>
                    `).find(`div.judge-block[data-flag="${flag}"]`).mutation();
                };
                $$(document).on('change', '#new-rule .judge-block .judge-then, #new-rule .judge-block .judge-else', function() {
                    var type = $$(this).hasClass('judge-then') ? 'then' : 'else';
                    var $mark = $$(this).parent().find(`.judge-${type}-pos`);
                    var hidden = $mark.parent().hasClass('mdui-hidden');
                    if ($$(this).val() == 'judge') {
                        if (!hidden) return;
                        var pos = $$.randomFlag();
                        insertBlock(pos, $$(this).parentsUntil('.judge-block').last().parent().data('flag'));
                        $mark.text(pos).parent().removeClass('mdui-hidden');
                    } else if (!hidden) {
                        $$(`div.judge-block[data-flag="${$mark.text()}"]`).removeBlock();
                        $mark.text('').parent().addClass('mdui-hidden');
                    }
                });
                $$(document).on('click', '#new-rule .judge-block .judge-then-pos, #new-rule .judge-block .judge-else-pos, #new-rule .judge-block .judge-back', function() {
                    var $flag = $$(`#new-rule div.judge-block[data-flag="${$$(this).text()}"]`);
                    window.scrollTo(0, $flag.offset().top - 72);
                    $flag.find('.judge-block-label').addClass('judge-block-label-highlight');
                    $$.throttle(function() {
                        $flag.find('.judge-block-label').removeClass('judge-block-label-highlight');
                    }, 1000)();
                });
                const html2Rule = function(flag) {
                    var $node = $$(`div.judge-block[data-flag="${flag}"]`);
                    var name = $node.find('.judge-name').val();
                    var optr = $node.find('.judge-optr').val();
                    var target = $node.find('.judge-target .mdui-textfield-input').val();
                    if (name != 'uid' && name != 'length' && optr != '~')
                        target = `'${target.replace(/\\/g, '\\\\').replace(/'/g, '\\\'')}'`;
                    var judge_then = $node.find('.judge-then').val();
                    var judge_else = $node.find('.judge-else').val();
                    var res = `[ ${name} ${optr} ${target} ] : `;
                    if (judge_then == 'judge') res += html2Rule($node.find('.judge-then-pos').text());
                    else res += judge_then;
                    res += ' ! ';
                    if (judge_else == 'judge') res += html2Rule($node.find('.judge-else-pos').text());
                    else res += judge_else;
                    return res + ' ;';
                };
                $$('#mode-change').on('click', function() {
                    var e = this;
                    mdui.confirm('确定要切换吗？由于对已有内容的转换不保证 100% 兼容，我们更建议仅使用一种模式来编辑规则。（如果您还没有开始编辑规则，请忽略该提示直接点击确定）', '提示', function() {
                        if ($$(e).text() == '切换到规则文本编辑模式') {
                            $$(e).text('切换到所见即所得编辑模式');
                            $$('#graphical-mode').addClass('mdui-hidden');
                            $$('#text-mode').removeClass('mdui-hidden');
                            $$('#text-mode textarea').val(html2Rule('#Main'));
                            mdui.updateTextFields('#text-mode .mdui-textfield');
                        } else {
                            var rule = $$('#text-mode textarea').val();
                            if (rule == '') {
                                $$(e).text('切换到规则文本编辑模式');
                                $$('#graphical-mode').removeClass('mdui-hidden');
                                $$('#text-mode').addClass('mdui-hidden');
                                return;
                            }
                            loading.open();
                            $$.ajax({
                                method: 'POST',
                                url: '<?php $options->index('/action/manage-commentruleset?a=translate'); ?>',
                                data: {input: rule},
                                dataType: 'json',
                                complete() {
                                    setTimeout(function() {
                                        loading.close();
                                    }, 100);
                                },
                                success(data, _, xhr) {
                                    if (xhr.status == 200) {
                                        $$(e).text('切换到规则文本编辑模式');
                                        $$('#graphical-mode').removeClass('mdui-hidden');
                                        $$('#text-mode').addClass('mdui-hidden');
                                        if (data['#Main'] != null) {
                                            $$('#new-rule .judge-addition').html('');
                                            $$.each(data, function(flag, block) {
                                                var $block;
                                                if (flag == '#Main') $block = $$('#new-rule .judge-block[data-flag="#Main"]');
                                                else $block = insertBlock(flag, block['parent']);
                                                $block.find('.judge-name').select(block['name']).trigger('change');
                                                $block.find('.judge-optr').select(block['optr']).trigger('change');
                                                $block.find('.judge-target .mdui-textfield-input').val(block['target']);
                                                mdui.updateTextFields($block.find('.judge-target'));
                                                if (block['then'].indexOf('#') != -1) {
                                                    $block.find('.judge-then').select('judge');
                                                    $block.find('.judge-then-pos').text(block['then']).parent().removeClass('mdui-hidden');
                                                } else $block.find('.judge-then').select(block['then']);
                                                if (block['else'].indexOf('#') != -1) {
                                                    $block.find('.judge-else').select('judge');
                                                    $block.find('.judge-else-pos').text(block['else']).parent().removeClass('mdui-hidden');
                                                } else $block.find('.judge-else').select(block['else']);
                                            });
                                        }
                                    } else if (xhr.status == 204) {
                                        $$(e).text('切换到规则文本编辑模式');
                                        $$('#graphical-mode').removeClass('mdui-hidden');
                                        $$('#text-mode').addClass('mdui-hidden');
                                    } else {
                                        mdui.alert(`<div class="mdui-typo">规则编译失败！<br>${data.result}<br>如果要强制切换，请将输入框清空。</div>`, '错误', function() {}, {confirmText: '确定'});
                                    }
                                },
                                error(xhr) {
                                    mdui.alert(`请求出错。（${xhr.status} ${xhr.statusText}）`, '错误', function() {}, {confirmText: '确定'});
                                }
                            });
                        }
                    }, function() {}, {confirmText: '确定', cancelText: '取消'});
                });
            });
        </script>
    </body>
</html>
