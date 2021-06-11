import React from 'react';
import Box from '@material-ui/core/Box';
import Typography from '@material-ui/core/Typography';
import Link from '@material-ui/core/Link';
import Divider from '@material-ui/core/Divider';

function makeTree(source) {
    if (!source) return undefined;
    if (typeof source !== "object") return `└───${source}`;
    const keys = Array.isArray(source) ? source : Object.keys(source);
    const last = keys.pop();
    let result = "";
    for (const key of keys) {
        const item = source[key];
        const t = makeTree(item);
        result += `├───${key}\n${t ? t.split('\n').map((v) => ("│   " + v)).join('\n') + "\n" : ""}`;
    }
    const t = makeTree(source[last]);
    result += `└───${last}\n${t ? t.split('\n').map((v) => ("    " + v)).join('\n') : ""}`;
    return result.trim();
}

export default function MetaPage() {
    return (
        <Typography component="div">
            <Box fontSize="h4.fontSize" m={1}>
                Meta
                <Box color="text.secondary" fontSize="subtitle1.fontSize" component="span" m={1}>of CommentRuleset</Box>
            </Box>
            <Divider />
            <Box fontSize="h5.fontSize" m={1}>
                目录结构与文件
            </Box>
            <Box fontSize="body1.fontSize" m={1}>
                <pre><code>{"CommentRuleset\n" + makeTree({
                    "libs": ["Logger.php", "MenuOutputer.php", "RuleCompiler.php"],
                    "runtime": ["ruleset.php [?]", "... [?]"],
                    "static": ["..."],
                    "Action.php": false,
                    "control-panel.php": false,
                    "LICENSE": false,
                    "Plugin.php": false,
                    "README.md": false,
                })}</code></pre>
                <p>Note：标注 <code>[?]</code> 的项目为可选项目，它们将在运行时被创建及修改。</p>
            </Box>
            <Divider />
            <Box fontSize="h5.fontSize" m={1}>
                LICENSE
            </Box>
            <Box fontSize="body1.fontSize" m={1}>
                <ul>
                    <li><code>libs/MenuOutputer.php</code> 以 <Link href="https://www.gnu.org/licenses/old-licenses/gpl-2.0.html" target="_blank" rel="noopener noreferrer">GNU General Public License v2.0</Link> 进行许可；</li>
                    <li><code>libs/RuleCompiler.php</code> 及 <code>libs/Logger.php</code> 以 <Link href="https://www.gnu.org/licenses/agpl-3.0.en.html" target="_blank" rel="noopener noreferrer">GNU Affero General Public License v3.0</Link> 进行许可；</li>
                    <li><code>static</code> 目录中主要代码（与插件页面逻辑相关的自有代码）以 <Link href="https://www.gnu.org/licenses/agpl-3.0.en.html" target="_blank" rel="noopener noreferrer">GNU Affero General Public License v3.0</Link> 进行许可，部分依赖库的许可协议见目录下 <code>*.LICENSE.txt</code>。</li>
                </ul>
                本 CommentRuleset 插件本体以 <Link href="https://www.gnu.org/licenses/agpl-3.0.en.html" target="_blank" rel="noopener noreferrer">GNU Affero General Public License v3.0</Link> 进行许可。
            </Box>
        </Typography>
    );
}
