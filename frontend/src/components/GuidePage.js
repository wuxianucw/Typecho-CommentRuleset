import React from 'react';
import Box from '@material-ui/core/Box';
import Typography from '@material-ui/core/Typography';
import { makeStyles } from '@material-ui/core/styles';
import Tooltip from '@material-ui/core/Tooltip';
import Link from '@material-ui/core/Link';
import Divider from '@material-ui/core/Divider';
import Table from '@material-ui/core/Table';
import TableBody from '@material-ui/core/TableBody';
import TableCell from '@material-ui/core/TableCell';
import TableContainer from '@material-ui/core/TableContainer';
import TableHead from '@material-ui/core/TableHead';
import TableRow from '@material-ui/core/TableRow';
import Paper from '@material-ui/core/Paper';

const useStyles = makeStyles((theme) => ({
    table: {
        minWidth: 750,
    }
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
                <p>在“新增规则”选项卡，我们可以看到“规则名称”、“优先级”、“启用规则”、“规则备注”、“规则内容”以及“编译规则”六个配置项，下表是对它们的详细说明（其中“必要”的“二值”代表这个配置项一定存在且只有两种状态，不存在为空的情况）：</p>
            </Box>
            <TableContainer component={Paper}>
                <Table className={classes.table}>
                    <TableHead>
                        <TableRow>
                            <TableCell>配置项</TableCell>
                            <TableCell>类型</TableCell>
                            <TableCell>映射字段名及类型</TableCell>
                            <TableCell>必要</TableCell>
                            <TableCell>说明</TableCell>
                        </TableRow>
                    </TableHead>
                    <TableBody>
                        <TableRow>
                            <TableCell>规则名称</TableCell>
                            <TableCell><code>string</code></TableCell>
                            <TableCell><code>name: string</code></TableCell>
                            <TableCell>是</TableCell>
                            <TableCell>当前规则的名称。规范清晰的命名将有助于日后对规则的维护。</TableCell>
                        </TableRow>
                        <TableRow>
                            <TableCell>优先级</TableCell>
                            <TableCell><code>int</code></TableCell>
                            <TableCell><code>priority: int</code></TableCell>
                            <TableCell>是</TableCell>
                            <TableCell>一个大于或等于 0 且小于或等于 99999 的整数。当规则有冲突时，优先级高的规则将会优先生效。</TableCell>
                        </TableRow>
                        <TableRow>
                            <TableCell>启用规则</TableCell>
                            <TableCell><code>bool</code></TableCell>
                            <TableCell><code>status: string[]</code></TableCell>
                            <TableCell>二值</TableCell>
                            <TableCell>用于控制规则的启用状态。如果设置为启用，则必须编译规则。</TableCell>
                        </TableRow>
                        <TableRow>
                            <TableCell>规则备注</TableCell>
                            <TableCell><code>string</code></TableCell>
                            <TableCell><code>remark: string</code></TableCell>
                            <TableCell>否</TableCell>
                            <TableCell>为当前规则添加的备注信息，可以是任意内容。</TableCell>
                        </TableRow>
                        <TableRow>
                            <TableCell>规则内容</TableCell>
                            <TableCell><code>RuleData | string</code></TableCell>
                            <TableCell><code>editMode: int</code><br /><code>ruleText: string</code></TableCell>
                            <TableCell>是</TableCell>
                            <TableCell>当前规则的内容，决定该规则的行为。<br /><code>type RuleData = [string, string, string, string, string[], string[], string | undefined][];</code></TableCell>
                        </TableRow>
                        <TableRow>
                            <TableCell>编译规则</TableCell>
                            <TableCell><code>bool</code></TableCell>
                            <TableCell><code>status: string[]</code></TableCell>
                            <TableCell>二值</TableCell>
                            <TableCell>指定是否编译这条规则。如果需要启用规则，则规则必须编译；当编译失败时，规则无法被启用。</TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </TableContainer>
            <Box fontSize="body1.fontSize" m={1}>
                <p>这里我们尝试新增一条在检测到评论中包含网址时，将其标为待审核的规则。这是一种很简陋的防御广告的基本方法，但是很具有代表性，可以帮助您了解规则创建的一般流程。</p>
                <p>规则名称可以按照您的喜好填写，例如“检测网址”；规则备注同理，例如“对于包含网址的评论，将其标为待审核以便手动筛选”。目前我们暂时不用考虑优先级问题，保持默认的“10”即可。勾选“编译规则”和“启用规则”（后者勾选时会自动勾选前者）以确保规则生效，接下来我们开始设置规则内容。</p>
                <p>这里仅讲解默认的“所见即所得编辑模式”的使用方法，“规则文本编辑模式”的用法将在后续“进阶指导”以及“高级技巧”部分展开。在这个模式下，一切都是语义化的，规则可以按照字面意思来理解。</p>
                <p>为了书写规则内容，首先需要构思规则的逻辑。要检测评论中的网址，被检测的对象应该是“评论内容”。下表列出了目前所有可供检测的对象：</p>
            </Box>
            {/* TODO：这里应该有个表 */}
            <Box fontSize="body1.fontSize" m={1}>
                <p>考虑最简单的检测方法，可以使用“包含”运算符，检查评论内容是否包含“http”。如果是，标记为待审核；否则无动作。输入这些内容并保存，我们就完成了我们的第一条最简单的规则。</p>
                <p>但是这样的缺陷十分明显：误判率太高。可能访客输入的是“HTTP 协议”，而“包含”既不区分大小写，也不关注上下文，尽管速度相对较快，但局限性比较大。即使更改为检测“http://”和“https://”，也同样存在很大问题。因此，我们可以考虑用正则表达式优化这条规则。</p>
                <p>要使用正则表达式，首先需要把运算符改为“符合（正则表达式）”，然后书写正则表达式即可。简单的网址匹配可以使用 <code>/https?:\/\/[a-zA-Z0-9]+\.[a-zA-Z0-9]+/</code>，注意这个正则表达式并不是对网址的完整匹配，仅仅是一个特征识别。如果需要一个完整匹配，可以查阅正则表达式的相关资料。</p>
                <p>有时，博主可能会在评论中发送网址，但这同样会被标记为待审核，显得很麻烦。我们可以通过增加判断逻辑来解决这个问题：先检测是否是博主，如果是则直接通过评论，否则再进行后续检测。这个修改比较棘手，因为它涉及判断逻辑的插入，需要重写所有的内容（简便方法将在后续介绍）。首先，将 <code>#Main</code> 中改为“如果 UID 等于 1”（一般情况下博主的 UID 为 1，游客为 0），“那么 通过评论 否则 继续判断”，在选择“继续判断”后，会立刻生成一个新的判断块，在该判断块中重写刚刚的“如果 评论内容 符合（正则表达式）……”即可。</p>
                <p>保存规则，现在它已经具备简陋但很有效的功能了。这就是新增/编辑规则的一般步骤。</p>
            </Box>
            <Box fontSize="h5.fontSize" m={1}>
                进阶指导
                <Box color="text.secondary" fontSize="subtitle2.fontSize" component="span" m={1}>这才是常规操作</Box>
            </Box>
            <Box fontSize="body1.fontSize" m={1}>
                <p>这部分可以介绍比较复杂的规则……</p>
            </Box>
            <Box fontSize="h5.fontSize" m={1}>
                高级技巧
                <Box color="text.secondary" fontSize="subtitle2.fontSize" component="span" m={1}>Let's Speed Up!</Box>
            </Box>
            <Box fontSize="body1.fontSize" m={1}>
                <p>这里介绍 Rule 语法以及编译器相关……</p>
            </Box>
            <Box fontSize="h5.fontSize" m={1}>
                疑难解答
                <Box color="text.secondary" fontSize="subtitle2.fontSize" component="span" m={1}>好像有哪里不对</Box>
            </Box>
            <Box fontSize="body1.fontSize" m={1}>如果您在使用过程中遇到了问题，这部分内容可能会有所帮助。如果您无法在这里找到答案，可以<Link href="https://github.com/wuxianucw/Typecho-CommentRuleset/issues" target="_blank" rel="noopener noreferrer">在 GitHub 上提出 issue</Link>。</Box>
            <Box fontSize="h6.fontSize" m={1}>
                常见问题
                <Box color="text.secondary" fontSize="subtitle2.fontSize" component="span" m={1}>Q&amp;A</Box>
            </Box>
            <Box fontSize="body1.fontSize" m={1}></Box>
            <Box fontSize="h6.fontSize" m={1}>
                编译错误参考
                <Box color="text.secondary" fontSize="subtitle2.fontSize" component="span" m={1}>解析时遇到了雾之湖的妖精</Box>
            </Box>
            <Box fontSize="body1.fontSize" m={1}>注：下表中 <code>xxx</code> 代表任意字符。</Box>
        </Typography>
    );
}
