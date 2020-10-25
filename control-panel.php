<?php if (!defined('__TYPECHO_ADMIN__')) exit;
require_once __DIR__ . '/libs/MenuOutputer.php';
Typecho_Widget::widget('CommentRuleset_MenuOutputer')->to($menuOutputer);
$ruleset = CommentRuleset_Plugin::getRuleset(); ?><!doctype html><html lang="zh-Hans"><head><meta charset="utf-8"/><meta name="viewport" content="minimum-scale=1,initial-scale=1,width=device-width"/><title><?php _e('%s - %s - Powered by Typecho', $menu->title, $options->title); ?></title><link rel="stylesheet" href="<?php $options->rootUrl(); ?>/usr/plugins/CommentRuleset/static/roboto-font.css"/><style>code{padding:2px 6px;color:#c7254e;font-family:'Fira Code',Consolas,'Courier New',monospace;font-feature-settings:"calt" 1;text-rendering:optimizeLegibility;background-color:#f7f7f9;border-radius:2px}</style><script>window.__pageData=<?php if ($user->logged > 0) {
    $logged = new Typecho_Date($user->logged);
    $logged = '最后登录: ' . $logged->word();
} else $logged = $user->screenName;
$pageData = array(
    'apiBase' => $options->index . '/action/manage-commentruleset',
    'sourceBase' => $options->rootUrl . '/usr/plugins/CommentRuleset/',
    'title' => $menu->title,
    'account' => array($logged, $options->adminUrl . 'profile.php', $options->logoutUrl),
    'menu' => $menuOutputer->output(),
    'rules' => array_map(function($k, $v) {
        return array(
            'name' => $v['name'],
            'ruid' => $k,
            'remark' => $v['remark'],
            'status' => $v['status'],
            'priority' => $v['priority'],
        );
    }, array_keys($ruleset), $ruleset),
);echo json_encode($pageData); ?></script></head><body><noscript>请启用 JavaScript 以正常显示本页面。</noscript><div id="root"></div><script>!function(e){function r(r){for(var n,a,i=r[0],c=r[1],f=r[2],s=0,p=[];s<i.length;s++)a=i[s],Object.prototype.hasOwnProperty.call(o,a)&&o[a]&&p.push(o[a][0]),o[a]=0;for(n in c)Object.prototype.hasOwnProperty.call(c,n)&&(e[n]=c[n]);for(l&&l(r);p.length;)p.shift()();return u.push.apply(u,f||[]),t()}function t(){for(var e,r=0;r<u.length;r++){for(var t=u[r],n=!0,i=1;i<t.length;i++){var c=t[i];0!==o[c]&&(n=!1)}n&&(u.splice(r--,1),e=a(a.s=t[0]))}return e}var n={},o={2:0},u=[];function a(r){if(n[r])return n[r].exports;var t=n[r]={i:r,l:!1,exports:{}};return e[r].call(t.exports,t,t.exports,a),t.l=!0,t.exports}a.e=function(e){var r=[],t=o[e];if(0!==t)if(t)r.push(t[2]);else{var n=new Promise((function(r,n){t=o[e]=[r,n]}));r.push(t[2]=n);var u,i=document.createElement("script");i.charset="utf-8",i.timeout=120,a.nc&&i.setAttribute("nonce",a.nc),i.src=function(e){return a.p+"static/js/"+({}[e]||e)+"."+{0:"095e92d9",4:"a446f92c",5:"3108ee9c",6:"679913f4",7:"469c515c",8:"b2f5f4e0"}[e]+".chunk.js"}(e);var c=new Error;u=function(r){i.onerror=i.onload=null,clearTimeout(f);var t=o[e];if(0!==t){if(t){var n=r&&("load"===r.type?"missing":r.type),u=r&&r.target&&r.target.src;c.message="Loading chunk "+e+" failed.\n("+n+": "+u+")",c.name="ChunkLoadError",c.type=n,c.request=u,t[1](c)}o[e]=void 0}};var f=setTimeout((function(){u({type:"timeout",target:i})}),12e4);i.onerror=i.onload=u,document.head.appendChild(i)}return Promise.all(r)},a.m=e,a.c=n,a.d=function(e,r,t){a.o(e,r)||Object.defineProperty(e,r,{enumerable:!0,get:t})},a.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},a.t=function(e,r){if(1&r&&(e=a(e)),8&r)return e;if(4&r&&"object"==typeof e&&e&&e.__esModule)return e;var t=Object.create(null);if(a.r(t),Object.defineProperty(t,"default",{enumerable:!0,value:e}),2&r&&"string"!=typeof e)for(var n in e)a.d(t,n,function(r){return e[r]}.bind(null,n));return t},a.n=function(e){var r=e&&e.__esModule?function(){return e.default}:function(){return e};return a.d(r,"a",r),r},a.o=function(e,r){return Object.prototype.hasOwnProperty.call(e,r)},a.p=window.__pageData.sourceBase,a.oe=function(e){throw console.error(e),e};var i=this.webpackJsonpfrontend=this.webpackJsonpfrontend||[],c=i.push.bind(i);i.push=r,i=i.slice();for(var f=0;f<i.length;f++)r(i[f]);var l=c;t()}([])</script><script src="<?php $options->rootUrl(); ?>/usr/plugins/CommentRuleset/static/js/3.f9a362bf.chunk.js"></script><script src="<?php $options->rootUrl(); ?>/usr/plugins/CommentRuleset/static/js/main.5777c532.chunk.js"></script></body></html>