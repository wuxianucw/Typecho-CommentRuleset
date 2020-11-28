import React from 'react';
import Box from '@material-ui/core/Box';
import Typography from '@material-ui/core/Typography';
import { makeStyles } from '@material-ui/core/styles';
import Tooltip from '@material-ui/core/Tooltip';
import Link from '@material-ui/core/Link';
import Divider from '@material-ui/core/Divider';

const useStyles = makeStyles((theme) => ({
    // TODO
}));

export default function GuidePage() {
    const classes = useStyles();

    return (
        <Typography component="div">
            <Box fontSize="h4.fontSize" m={1}>
                教程：如何优雅地使用评论规则集
                <Box color="text.secondary" fontSize="subtitle1.fontSize" component="span" m={1}>文档版本：v1.0</Box>
            </Box>
            <Box fontSize="body1.fontSize" m={1}>
                Typecho 评论规则集插件（<code>CommentRuleset</code>，后简称“插件”）是一款灵活高效的评论控制插件，其功能的灵活性使得对其的配置较为复杂。为了一定程度上排除这款插件在使用上的疑难，同时对插件用法作出一定的指导，笔者特地撰写此教程，以便插件强大的功能得到充分的使用。
            </Box>
            <Box fontSize="h5.fontSize" m={1}>
                基础玩法
                <Box color="text.secondary" fontSize="subtitle2.fontSize" component="span" m={1}>快速入门</Box>
            </Box>
            <Box fontSize="body1.fontSize" m={1}>
                <p>如果您对插件启用、禁用、增删规则等操作比较熟悉，可以跳过这部分内容。（话说能看到此文就说明插件已经成功启用了吧……）</p>
                <p>首先让我们了解一下插件的目录结构：</p>
                <pre><code>CommentRuleset<br />├───libs<br />├───runtime<br />└───static</code></pre>
                <p>其中 <code>libs</code> 和 <code>static</code> 目录是固有的，运行时不会被修改；<code>runtime</code> 目录是动态生成的，<strong>必须可写且可执行</strong>，将会存放插件的一些必要数据以及规则的编译结果。</p>
                <p>如果您发现插件目录中还有其他子目录，例如 <code>test</code>、<code>tools</code> 甚至 <code>frontend</code>，除非您非常清楚它们的作用，否则笔者建议删除它们，因为它们不是必要的，且在某些情况下可能带来潜在的风险。</p>
                <p><strong>请注意：在插件处于启用状态时，笔者不建议在任何情况下修改或删除插件目录中的文件。</strong></p>
                <p><code>libs</code> 目录用于存放插件用到的额外 PHP 库。其中 <code>MenuOutputer.php</code> 是笔者在 Typecho 原生后台菜单输出组件的基础上编写的用于输出纯数据形式菜单结构的组件，按照 Typecho 的要求以 <Link href="https://www.gnu.org/licenses/old-licenses/gpl-2.0.html" target="_blank" rel="noopener noreferrer">GNU General Public License v2.0</Link> 进行许可；<code>RuleCompiler.php</code> 是笔者编写的一个将笔者设计的“规则语言”编译到 PHP 的小型编译器（当然，没有什么特别复杂的结构，只是一个简单的工具库），<code>Logger.php</code> 是一个日志记录器，这两者都以 <Link href="https://www.gnu.org/licenses/agpl-3.0.en.html" target="_blank" rel="noopener noreferrer">GNU Affero General Public License v3.0</Link> 进行许可。</p>
                <p><code>static</code> 目录用于存放本地资源文件，包括必要的样式表、Roboto 字体和编译后的 JavaScript 文件，其中主要代码（与插件页面逻辑相关的自有代码）以 <Link href="https://www.gnu.org/licenses/agpl-3.0.en.html" target="_blank" rel="noopener noreferrer">GNU Affero General Public License v3.0</Link> 进行许可，部分依赖库的许可协议见目录下 <code>*.LICENSE.txt</code>。</p>
                <p>下面重点介绍 <code>runtime</code> 目录中的文件组成。</p>
                <p>全新安装的插件在启用前并不存在 <code>runtime</code> 目录，启用插件时，插件将会自动创建这个目录以及目录下的 <code>ruleset.php</code> 规则集索引文件。规则集索引文件用于保存当前情况下的整个规则列表。</p>
                <p>编译规则后，目录下会生成相应的 <code>rule_<Tooltip title="RUID"><span style={{ padding: '2px',backgroundColor: '#e8eaf6' }}>xxxxxx</span></Tooltip>.php</code>；如果存在生效的规则，还会生成 <code>rules.php</code>，用于运行时的快速判断。</p>
                <p>这里第一次出现了 RUID 的概念，它的全称是 Rule Unique ID，即规则唯一标识符，是一个字母全部小写的 6 位十六进制数，采用随机的方法生成，用于标识规则。</p>
                <p><strong>再次强调：绝对不要手动修改或删除 <code>runtime</code> 目录下的任何文件！这样做的后果很可能是灾难性的！</strong></p>
                <Divider />
                <p>了解一些基本概念后，我们来尝试新增一条规则。</p>
                <p>在“新增规则”选项卡，我们可以看到“规则名称”、“优先级”、“启用规则”、“规则备注”和“规则内容”五个配置项，下表是对它们的详细说明：</p>
            </Box>
        </Typography>
    );
}
