<?php
class Helper {
    public static function options() {
        return new class {
            public function plugin($_) {
                assert($_ === 'CommentRuleset', "Plugin name should be `CommentRuleset`, `{$_}` found.");

                static $_data = array(
                    'errmsg' => '根据相关设置，该评论被拒绝。',
                    'logLevel' => 0,
                    'monaco' => 0,
                    'customizedMonacoUrl' => '',
                );

                $result = new stdClass;
                foreach ($_data as $k => $v) $result->{$k} = $v;
                return $result;
            }
        };
    }
}
