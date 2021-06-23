<?php
function error($message) {
    echo $message . "\n";
    exit(1);
}
$input = __DIR__ . '/build/control-panel.php';
if (!file_exists($input)) error('No input file found.');
$content = file_get_contents($input);
$content = '<?php if (!defined(\'__TYPECHO_ADMIN__\')) exit;
require_once __DIR__ . \'/libs/MenuOutputer.php\';
Typecho_Widget::widget(\'CommentRuleset_MenuOutputer\')->to($menuOutputer);
$ruleset = CommentRuleset_Plugin::getRuleset(); ?>' . $content;
$content = preg_replace('/\\<title>.*\\<\\/title>/',
    '<title><?php _e(\'%s - %s - Powered by Typecho\', $menu->title, $options->title); ?></title>', $content, 1);
$content = preg_replace_callback('/\\<script>function createData.+window\\.__pageData.+?\\<\\/script>/', function() {
    $result = '<script>window.__pageData=<?php ';
    $result .= trim(<<<'DATA'
if ($user->logged > 0) {
    $logged = new Typecho_Date($user->logged);
    $logged = '最后登录: ' . $logged->word();
} else $logged = $user->screenName;
$pluginOpt = $options->plugin('CommentRuleset');
$pageData = array(
    'version' => (isset($pluginOpt->checkUpdate) && intval($pluginOpt->checkUpdate) === 1) ? CommentRuleset_Plugin::VERSION : false,
    'apiBase' => $options->index . '/action/manage-commentruleset',
    'sourceBase' => $options->rootUrl . '/usr/plugins/CommentRuleset/',
    'monacoUrl' => CommentRuleset_Plugin::getMonacoUrl(),
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
);
DATA
    );
    $result .= 'echo json_encode($pageData);';
    $result .= ' ?></script>';
    return $result;
}, $content, 1);
$content = str_replace('./static/', '<?php $options->rootUrl(); ?>/usr/plugins/CommentRuleset/static/', $content);
$content = str_replace('"./"', 'window.__pageData.sourceBase', $content);
file_put_contents($input, $content);
$input = glob(__DIR__ . '/build/static/js/runtime-main.*.js');
if (!$input) error('runtime-main.*.js is not found.');
$input = $input[0];
$content = file_get_contents($input);
$content = str_replace('"./"', 'window.__pageData.sourceBase', $content);
file_put_contents($input, $content);
