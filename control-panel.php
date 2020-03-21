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
    <body class="mdui-drawer-body-left">
    <!--[if lt IE 9]>
        <div class="message error browsehappy" role="dialog"><?php _e('当前网页 <strong>不支持</strong> 你正在使用的浏览器. 为了正常的访问, 请 <a href="http://browsehappy.com/">升级你的浏览器</a>'); ?>.</div>
    <![endif]-->
    <script src="<?php CommentRuleset_Plugin::mdui() ?>/js/mdui.min.js?v=0.4.3"></script>
    </body>
</html>
