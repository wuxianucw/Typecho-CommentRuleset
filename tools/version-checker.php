<?php
define('__TYPECHO_ROOT_DIR__', true);
interface Typecho_Plugin_Interface {} // mock
require dirname(__DIR__) . '/Plugin.php';

function valid($ver) {
    $pattern = '/^(?P<major>0|[1-9]\\d*)\\.(?P<minor>0|[1-9]\\d*)\\.(?P<patch>0|[1-9]\\d*)(?:-(?P<prerelease>(?:0|[1-9]\\d*|\\d*[a-zA-Z-][0-9a-zA-Z-]*)(?:\\.(?:0|[1-9]\\d*|\\d*[a-zA-Z-][0-9a-zA-Z-]*))*))?(?:\\+(?P<buildmetadata>[0-9a-zA-Z-]+(?:\.[0-9a-zA-Z-]+)*))?$/';
    if (!preg_match($pattern, $ver, $matches)) return false;
    return "{$matches['major']}.{$matches['minor']}.{$matches['patch']}";
}

function parse_ver($plugin_file) {
    $tokens = token_get_all(file_get_contents($plugin_file));

    foreach ($tokens as $token) {
        if (is_array($token) && T_DOC_COMMENT == $token[0]) {
            $described = false;
            $lines = preg_split("(\r|\n)", $token[1]);
            foreach ($lines as $line) {
                $line = trim($line);
                if (!empty($line) && '*' == $line[0]) {
                    $line = trim(substr($line, 1));
                    if (!$described && !empty($line) && '@' == $line[0]) $described = true;
                    if (!$described && !empty($line)) continue; // skip

                    if ($described && !empty($line) && '@' == $line[0]) {
                        $line = trim(substr($line, 1));
                        $args = explode(' ', $line);
                        $key = array_shift($args);

                        if ($key == 'version') {
                            return trim(implode(' ', $args));
                        }
                    }
                }
            }
        }
    }
}

$doc_ver = valid(parse_ver(dirname(__DIR__) . '/Plugin.php'));
$defined_ver = valid(CommentRuleset_Plugin::VERSION);

if (!$doc_ver || !$defined_ver) {
    echo '[ERROR] Invalid version.';
    exit(1);
}

if ($doc_ver !== $defined_ver) {
    echo '[ERROR] Defined versions are inconsistent.';
    exit(1);
}

echo CommentRuleset_Plugin::VERSION;
