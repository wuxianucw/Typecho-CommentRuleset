<?php if(!defined('__TYPECHO_ADMIN__')) exit; ?>
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
    <body class="mdui-drawer-body-left mdui-appbar-with-toolbar mdui-theme-primary-red mdui-theme-accent-light-blue">
        <header class="mdui-appbar mdui-appbar-fixed">
            <div class="mdui-toolbar mdui-color-theme">
                <span class="mdui-btn mdui-btn-icon mdui-ripple mdui-ripple-white" mdui-drawer="{target: '#main-drawer', swipe: true}"><i class="mdui-icon material-icons">menu</i></span>
                <span class="mdui-typo-title"><?php $menu->title() ?></span>
                <div class="mdui-toolbar-spacer"></div>
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
                <div class="mdui-collapse-item ">
                    <div class="mdui-collapse-item-header mdui-list-item mdui-ripple">
                        <i class="mdui-list-item-icon mdui-icon material-icons mdui-text-color-blue">near_me</i>
                        <div class="mdui-list-item-content">开始使用</div>
                        <i class="mdui-collapse-item-arrow mdui-icon material-icons">keyboard_arrow_down</i>
                    </div>
                    <div class="mdui-collapse-item-body mdui-list">
                        <a href="./download" class="mdui-list-item mdui-ripple ">安装</a>
                        <a href="./compatibility" class="mdui-list-item mdui-ripple ">兼容性</a>
                        <a href="./jq" class="mdui-list-item mdui-ripple ">JavaScript 工具库</a>
                        <a href="./global" class="mdui-list-item mdui-ripple ">JavaScript 全局方法</a>
                    </div>
                </div>

                <div class="mdui-collapse-item ">
                    <div class="mdui-collapse-item-header mdui-list-item mdui-ripple">
                        <i class="mdui-list-item-icon mdui-icon material-icons mdui-text-color-deep-orange">layers</i>
                        <div class="mdui-list-item-content">样式</div>
                        <i class="mdui-collapse-item-arrow mdui-icon material-icons">keyboard_arrow_down</i>
                    </div>
                    <div class="mdui-collapse-item-body mdui-list">
                        <a href="./color" class="mdui-list-item mdui-ripple ">颜色与主题</a>
                        <a href="./font" class="mdui-list-item mdui-ripple ">字体</a>
                        <a href="./grid" class="mdui-list-item mdui-ripple ">网格布局</a>
                        <a href="./typo" class="mdui-list-item mdui-ripple ">排版</a>
                        <a href="./icon" class="mdui-list-item mdui-ripple ">图标</a>
                        <a href="./media" class="mdui-list-item mdui-ripple ">媒体</a>
                        <a href="./helper" class="mdui-list-item mdui-ripple ">辅助类</a>
                        <a href="./shadow" class="mdui-list-item mdui-ripple ">阴影</a>
                    </div>
                </div>

                <div class="mdui-collapse-item mdui-collapse-item-open">
                    <div class="mdui-collapse-item-header mdui-list-item mdui-ripple">
                        <i class="mdui-list-item-icon mdui-icon material-icons mdui-text-color-green">widgets</i>
                        <div class="mdui-list-item-content">组件</div>
                        <i class="mdui-collapse-item-arrow mdui-icon material-icons">keyboard_arrow_down</i>
                    </div>
                    <div class="mdui-collapse-item-body mdui-list">
                        <a href="./ripple" class="mdui-list-item mdui-ripple ">涟漪动画效果</a>
                        <a href="./button" class="mdui-list-item mdui-ripple ">按钮</a>
                        <a href="./fab" class="mdui-list-item mdui-ripple ">浮动操作按钮</a>
                        <a href="./select" class="mdui-list-item mdui-ripple ">下拉选择</a>
                        <a href="./divider" class="mdui-list-item mdui-ripple ">分隔线</a>
                        <a href="./panel" class="mdui-list-item mdui-ripple ">可扩展面板</a>
                        <a href="./textfield" class="mdui-list-item mdui-ripple ">文本框</a>
                        <a href="./selection_control" class="mdui-list-item mdui-ripple ">选择控件</a>
                        <a href="./slider" class="mdui-list-item mdui-ripple ">滑块</a>
                        <a href="./list" class="mdui-list-item mdui-ripple ">列表</a>
                        <a href="./list_control" class="mdui-list-item mdui-ripple ">列表控制</a>
                        <a href="./grid_list" class="mdui-list-item mdui-ripple ">网格列表</a>
                        <a href="./tab" class="mdui-list-item mdui-ripple ">Tab 选项卡</a>
                        <a href="./toolbar" class="mdui-list-item mdui-ripple ">工具栏</a>
                        <a href="./appbar" class="mdui-list-item mdui-ripple ">应用栏</a>
                        <a href="./drawer" class="mdui-list-item mdui-ripple mdui-list-item-active">抽屉式导航</a>
                        <a href="./bottom_nav" class="mdui-list-item mdui-ripple ">底部导航栏</a>
                        <a href="./card" class="mdui-list-item mdui-ripple ">卡片</a>
                        <a href="./chip" class="mdui-list-item mdui-ripple ">纸片</a>
                        <a href="./tooltip" class="mdui-list-item mdui-ripple ">工具提示</a>
                        <a href="./snackbar" class="mdui-list-item mdui-ripple ">Snackbar</a>
                        <a href="./table" class="mdui-list-item mdui-ripple ">表格</a>
                        <a href="./dialog" class="mdui-list-item mdui-ripple ">对话框</a>
                        <a href="./menu" class="mdui-list-item mdui-ripple ">菜单</a>
                        <a href="./progress" class="mdui-list-item mdui-ripple ">进度指示器</a>
                    </div>
                </div>

                <div class="mdui-collapse-item ">
                    <div class="mdui-collapse-item-header mdui-list-item mdui-ripple">
                        <i class="mdui-list-item-icon mdui-icon material-icons mdui-text-color-brown">view_carousel</i>
                        <div class="mdui-list-item-content">JavaScript 插件</div>
                        <i class="mdui-collapse-item-arrow mdui-icon material-icons">keyboard_arrow_down</i>
                    </div>
                    <div class="mdui-collapse-item-body mdui-list">
                        <a href="./collapse" class="mdui-list-item mdui-ripple ">Collapse</a>
                        <a href="./headroom" class="mdui-list-item mdui-ripple ">Headroom</a>
                    </div>
                </div>

                <div class="mdui-collapse-item ">
                    <div class="mdui-collapse-item-header mdui-list-item mdui-ripple">
                        <i class="mdui-list-item-icon mdui-icon material-icons mdui-text-color-purple">local_mall</i>
                        <div class="mdui-list-item-content">资源</div>
                        <i class="mdui-collapse-item-arrow mdui-icon material-icons">keyboard_arrow_down</i>
                    </div>
                    <div class="mdui-collapse-item-body mdui-list">
                        <a href="./material_icon" class="mdui-list-item mdui-ripple ">Material 图标</a>
                    </div>
                </div>
            </div>
        </div>
        <script src="<?php CommentRuleset_Plugin::mdui() ?>/js/mdui.min.js?v=0.4.3"></script>
    </body>
</html>
