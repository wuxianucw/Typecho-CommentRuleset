<?php
if(!defined('__TYPECHO_ADMIN__')) exit;
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
    </head>
    <body class="mdui-drawer-body-left mdui-appbar-with-toolbar mdui-theme-primary-red mdui-theme-accent-blue">
        <header class="mdui-appbar mdui-appbar-fixed">
            <div class="mdui-toolbar mdui-color-theme">
                <span class="mdui-btn mdui-btn-icon mdui-ripple mdui-ripple-white" mdui-drawer="{target: '#main-drawer', swipe: true}"><i class="mdui-icon material-icons">menu</i></span>
                <span class="mdui-typo-title"><?php $menu->title() ?></span>
                <div class="mdui-toolbar-spacer"></div>
                <a class="mdui-btn mdui-btn-icon mdui-ripple mdui-ripple-white" href="<?php $options->adminUrl('profile.php'); ?>" mdui-tooltip="{content: '<?php
                    if($user->logged > 0) {
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
        <div class="mdui-container">
            <div class="mdui-typo">
                <h3><?php _e('目前共 %d 条规则', count($ruleset)); ?></h3>
            </div>
            <div class="mdui-tab mdui-tab-full-width" mdui-tab>
                <a href="#view-rules" class="mdui-ripple">规则总览</a>
                <a href="#new-rule" class="mdui-ripple">新增规则</a>
                <a href="#guide" class="mdui-ripple">配置指南</a>
            </div>
            <div id="view-rules" class="mdui-typo mdui-p-a-2">
                暂无规则
            </div>
            <div id="new-rule" class="mdui-typo mdui-p-a-2">
                当前配置不允许新增规则
            </div>
            <div id="guide" class="mdui-typo mdui-p-a-2">
                配置指南
            </div>
        </div>
        <script src="<?php CommentRuleset_Plugin::mdui() ?>/js/mdui.min.js?v=0.4.3"></script>
    </body>
</html>
